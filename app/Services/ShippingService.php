<?php

namespace App\Services;

use App\Exceptions\ShippingServiceException;
use App\Models\ShippingSetting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class ShippingService
{
    private const DESTINATION_PAGE_SIZE = 10;
    private const DESTINATION_CACHE_MINUTES = 30;

    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            config('rajaongkir.base_url'),
            '/'
        );

        $this->apiKey = config('rajaongkir.api_key') ?? '';
    }

    /**
     * Membuat koneksi ke API RajaOngkir.
     */
    protected function api()
    {
        if (empty($this->apiKey)) {
            throw new ShippingServiceException(
                'API key RajaOngkir belum dikonfigurasi.',
                ShippingServiceException::API_ERROR
            );
        }

        return Http::acceptJson()
            ->withHeaders([
                'key' => $this->apiKey,
            ])
            ->timeout(20);
    }

    /**
     * Mencari lokasi berdasarkan nama wilayah.
     */
    public function searchDestination(
        string $keyword,
        ?callable $matches = null
    ): array
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            throw new RuntimeException(
                'Nama lokasi tujuan tidak boleh kosong.'
            );
        }

        $destinations = [];
        $seenDestinationIds = [];
        $seenPages = [];
        $offset = 0;
        $pageCount = 0;

        do {
            $data = $this->destinationPage($keyword, $offset);

            $pageFingerprint = hash('sha256', serialize($data));

            if (isset($seenPages[$pageFingerprint])) {
                Log::warning(
                    'RajaOngkir destination pagination returned a repeated page.',
                    [
                        'page_count' => $pageCount,
                        'result_count' => count($destinations),
                    ]
                );

                throw new ShippingServiceException(
                    'Pagination pencarian lokasi RajaOngkir tidak bergerak.',
                    ShippingServiceException::API_ERROR
                );
            }

            $seenPages[$pageFingerprint] = true;
            $pageCount++;
            $pageHasMatch = false;

            foreach ($data as $destination) {
                $id = $destination['id'] ?? null;

                if (!is_scalar($id) || !isset($seenDestinationIds[(string) $id])) {
                    $destinations[] = $destination;

                    if (is_scalar($id)) {
                        $seenDestinationIds[(string) $id] = true;
                    }
                }

                if ($matches && $matches($destination)) {
                    $pageHasMatch = true;
                }
            }

            $pageSize = count($data);
            $offset += self::DESTINATION_PAGE_SIZE;
        } while (
            $pageSize === self::DESTINATION_PAGE_SIZE &&
            !$pageHasMatch
        );

        Log::debug('RajaOngkir destination search completed.', [
            'page_count' => $pageCount,
            'result_count' => count($destinations),
            'stopped_after_match' => $pageHasMatch,
        ]);

        return $destinations;
    }

    /**
     * Mengambil satu halaman hasil pencarian menggunakan cache file, tanpa menulis ke database.
     */
    private function destinationPage(string $keyword, int $offset): array
    {
        $cache = Cache::store('file');
        $cacheKey = $this->destinationPageCacheKey($keyword, $offset);

        return $cache->remember(
            $cacheKey,
            now()->addMinutes(self::DESTINATION_CACHE_MINUTES),
            function () use ($cache, $keyword, $offset): array {
                $this->throwIfRateLimited($cache);

                try {
                    $response = $this->api()->get(
                        $this->baseUrl . '/destination/domestic-destination',
                        [
                            'search' => $keyword,
                            'limit' => self::DESTINATION_PAGE_SIZE,
                            'offset' => $offset,
                        ]
                    );
                } catch (ConnectionException) {
                    Log::warning(
                        'RajaOngkir destination search connection failed.',
                        [
                            'page_count' => (int) ($offset / self::DESTINATION_PAGE_SIZE),
                            'result_count' => 0,
                        ]
                    );

                    throw new ShippingServiceException(
                        'Koneksi pencarian lokasi RajaOngkir gagal.',
                        ShippingServiceException::CONNECTION_FAILED
                    );
                }

                if ($response->failed()) {
                    // RajaOngkir can return 404 when a search keyword has no
                    // destination matches. Treat that as an empty search result
                    // so the caller can try its next keyword, rather than
                    // aborting the entire automatic address lookup.
                    $metaMessage = (string) $response->json('meta.message', '');
                    $metaCode = (int) $response->json('meta.code', $response->status());

                    if (
                        $response->status() === 404 &&
                        $metaCode === 404 &&
                        stripos($metaMessage, 'not found') !== false
                    ) {
                        Log::debug('RajaOngkir destination search returned no matches.', [
                            'keyword' => $keyword,
                            'http_status' => 404,
                        ]);

                        return [];
                    }

                    $this->failForResponse(
                        $response,
                        'destination-search',
                        [$keyword],
                        $cache
                    );
                }

                $data = $response->json('data');

                if (
                    !is_array($data) ||
                    array_filter($data, fn ($item) => !is_array($item)) !== []
                ) {
                    $this->logApiFailure(
                        $response,
                        'destination-search',
                        ShippingServiceException::API_ERROR,
                        [$keyword]
                    );

                    throw new ShippingServiceException(
                        'Format respons pencarian lokasi RajaOngkir tidak valid.',
                        ShippingServiceException::API_ERROR,
                        $response->status()
                    );
                }

                return $data;
            }
        );
    }

    private function destinationPageCacheKey(
        string $keyword,
        int $offset
    ): string {
        return 'rajaongkir:destination:' . hash('sha256', serialize([
            'endpoint' => $this->baseUrl . '/destination/domestic-destination',
            'search' => $keyword,
            'limit' => self::DESTINATION_PAGE_SIZE,
            'offset' => $offset,
        ]));
    }

    /**
     * Status cooldown RajaOngkir saat ini (read-only), dipakai chatbot
     * untuk mendiagnosis kegagalan ongkir tanpa memanggil API.
     *
     * @return array{reason:string,retry_after:int}|null
     */
    public function cooldownStatus(): ?array
    {
        $cooldown = Cache::store('file')->get($this->rateLimitCacheKey());

        if (!is_array($cooldown)) {
            return null;
        }

        $retryAfter = max(0, (int) ($cooldown['retry_at'] ?? 0) - now()->timestamp);

        if ($retryAfter === 0) {
            return null;
        }

        return [
            'reason' => (string) ($cooldown['reason'] ?? ShippingServiceException::RATE_LIMITED),
            'retry_after' => $retryAfter,
        ];
    }

    public function hasApiKey(): bool
    {
        return $this->apiKey !== '';
    }

    private function rateLimitCacheKey(): string
    {
        return 'rajaongkir:rate-limit:' . hash(
            'sha256',
            $this->baseUrl . '|' . $this->apiKey
        );
    }

    private function throwIfRateLimited($cache): void
    {
        $cooldown = $cache->get($this->rateLimitCacheKey());

        if (!is_array($cooldown)) {
            return;
        }

        $retryAfter = max(
            0,
            (int) ($cooldown['retry_at'] ?? 0) - now()->timestamp
        );

        if ($retryAfter === 0) {
            $cache->forget($this->rateLimitCacheKey());

            return;
        }

        $reason = $cooldown['reason']
            ?? ShippingServiceException::RATE_LIMITED;

        throw new ShippingServiceException(
            $this->failureMessage($reason),
            $reason,
            429,
            $retryAfter
        );
    }

    private function failForResponse(
        Response $response,
        string $endpoint,
        array $sensitiveValues,
        $cache
    ): void {
        $quotaExhausted = $this->responseIndicatesQuotaExhaustion($response);
        $reason = $quotaExhausted
            ? ShippingServiceException::QUOTA_EXHAUSTED
            : (
                $response->status() === 429
                    ? ShippingServiceException::RATE_LIMITED
                    : ShippingServiceException::API_ERROR
            );

        $retryAfter = null;

        if ($response->status() === 429) {
            $retryAfter = $this->cooldownSeconds(
                $response,
                $quotaExhausted
            );

            $cache->put(
                $this->rateLimitCacheKey(),
                [
                    'reason' => $reason,
                    'retry_at' => now()->addSeconds($retryAfter)->timestamp,
                ],
                now()->addSeconds($retryAfter)
            );
        }

        $this->logApiFailure(
            $response,
            $endpoint,
            $reason,
            $sensitiveValues,
            $retryAfter
        );

        throw new ShippingServiceException(
            $this->failureMessage($reason),
            $reason,
            $response->status(),
            $retryAfter
        );
    }

    private function responseIndicatesQuotaExhaustion(
        Response $response
    ): bool {
        return preg_match(
            '/(?:quota|credit|plan).{0,60}(?:exhaust|deplet|exceed|limit|reached|insufficient)|(?:exhaust|deplet|exceed).{0,60}(?:quota|credit|plan)/i',
            $response->body()
        ) === 1;
    }

    private function cooldownSeconds(
        Response $response,
        bool $quotaExhausted
    ): int {
        $retryAfter = $this->retryAfterSeconds($response);

        if ($retryAfter !== null) {
            return max(1, $retryAfter);
        }

        $resetAfter = $this->rateLimitResetSeconds($response);

        if ($resetAfter !== null) {
            return max(1, $resetAfter);
        }

        return $quotaExhausted ? 300 : 30;
    }

    private function retryAfterSeconds(Response $response): ?int
    {
        $header = trim((string) $response->header('Retry-After'));

        if ($header === '') {
            return null;
        }

        if (ctype_digit($header)) {
            return (int) $header;
        }

        $timestamp = strtotime($header);

        return $timestamp === false
            ? null
            : max(0, $timestamp - now()->timestamp);
    }

    private function rateLimitResetSeconds(Response $response): ?int
    {
        $header = trim((string) $response->header('X-RateLimit-Reset'));

        if ($header === '') {
            return null;
        }

        if (ctype_digit($header)) {
            $timestamp = (int) $header;

            return $timestamp > now()->timestamp
                ? $timestamp - now()->timestamp
                : null;
        }

        $timestamp = strtotime($header);

        return $timestamp === false
            ? null
            : max(0, $timestamp - now()->timestamp);
    }

    private function logApiFailure(
        Response $response,
        string $endpoint,
        string $reason,
        array $sensitiveValues,
        ?int $retryAfterSeconds = null
    ): void {
        $sensitiveValues[] = $this->apiKey;

        Log::warning('RajaOngkir API request failed.', [
            'endpoint' => $endpoint,
            'reason' => $reason,
            'http_status' => $response->status(),
            'retry_after' => $this->safeHeader($response, 'Retry-After'),
            'x_rate_limit_limit' => $this->safeHeader(
                $response,
                'X-RateLimit-Limit'
            ),
            'x_rate_limit_remaining' => $this->safeHeader(
                $response,
                'X-RateLimit-Remaining'
            ),
            'x_rate_limit_reset' => $this->safeHeader(
                $response,
                'X-RateLimit-Reset'
            ),
            'retry_after_seconds' => $retryAfterSeconds,
            'response_body' => $this->sanitizedResponseBody(
                $response,
                $sensitiveValues
            ),
        ]);
    }

    private function safeHeader(Response $response, string $name): ?string
    {
        $value = $response->header($name);

        if ($value === null || $value === '') {
            return null;
        }

        return substr(
            preg_replace('/[\r\n\x00-\x1F\x7F]/', '', (string) $value) ?? '',
            0,
            100
        );
    }

    private function sanitizedResponseBody(
        Response $response,
        array $sensitiveValues
    ): string {
        $body = $response->json();

        if (!is_array($body)) {
            return $response->body() === ''
                ? ''
                : '[non-JSON response omitted]';
        }

        $body = $this->redactLogValue($body, $sensitiveValues);

        return substr(
            json_encode(
                $body,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ) ?: '{}',
            0,
            1000
        );
    }

    private function redactLogValue(mixed $value, array $sensitiveValues): mixed
    {
        if (is_array($value)) {
            $redacted = [];

            foreach ($value as $key => $item) {
                if (preg_match(
                    '/api.?key|authorization|token|secret|password|search|keyword|query|address|postal|postcode|zip|phone|recipient|detail|province|city|district|subdistrict|village|location|destination|data/i',
                    (string) $key
                )) {
                    $redacted[$key] = '[REDACTED]';

                    continue;
                }

                $redacted[$key] = $this->redactLogValue(
                    $item,
                    $sensitiveValues
                );
            }

            return $redacted;
        }

        if (!is_string($value)) {
            return $value;
        }

        foreach (array_filter($sensitiveValues, fn ($item) => $item !== '') as $sensitiveValue) {
            $value = str_ireplace(
                (string) $sensitiveValue,
                '[REDACTED]',
                $value
            );
        }

        $value = preg_replace(
            '/(api.?key|authorization|bearer|token|secret|password)\s*[:=]\s*["\']?[^,\s"\']+/i',
            '$1=[REDACTED]',
            $value
        ) ?? $value;

        return substr($value, 0, 500);
    }

    private function failureMessage(string $reason): string
    {
        return match ($reason) {
            ShippingServiceException::RATE_LIMITED =>
                'RajaOngkir sedang membatasi jumlah permintaan.',
            ShippingServiceException::QUOTA_EXHAUSTED =>
                'Kuota API RajaOngkir telah habis.',
            ShippingServiceException::CONNECTION_FAILED =>
                'Koneksi ke RajaOngkir gagal.',
            default => 'RajaOngkir gagal memproses permintaan.',
        };
    }

    /**
     * Menghitung ongkos kirim.
     */
    public function calculate(
        string $destination,
        int $weight,
        string $courier
    ): array {
        $setting = ShippingSetting::first();

        if (!$setting || !$setting->rajaongkir_origin_id) {
            throw new RuntimeException(
                'Lokasi asal pengiriman toko belum diatur.'
            );
        }

        if (trim($destination) === '') {
            throw new RuntimeException(
                'Lokasi tujuan pengiriman belum dipilih.'
            );
        }

        if ($weight < 1) {
            throw new RuntimeException(
                'Berat pengiriman harus lebih dari 0 gram.'
            );
        }

        if (trim($courier) === '') {
            throw new RuntimeException(
                'Kurir pengiriman belum dipilih.'
            );
        }

        $cache = Cache::store('file');
        $this->throwIfRateLimited($cache);

        try {
            $response = $this->api()
                ->asForm()
                ->post(
                    $this->baseUrl . '/calculate/domestic-cost',
                    [
                        'origin' => $setting->rajaongkir_origin_id,
                        'destination' => $destination,
                        'weight' => $weight,
                        'courier' => strtolower(trim($courier)),
                        'price' => 'lowest',
                    ]
                );
        } catch (ConnectionException) {
            Log::warning('RajaOngkir cost request connection failed.');

            throw new ShippingServiceException(
                'Koneksi perhitungan ongkir RajaOngkir gagal.',
                ShippingServiceException::CONNECTION_FAILED
            );
        }

        if ($response->failed()) {
            $this->failForResponse(
                $response,
                'domestic-cost',
                [
                    (string) $setting->rajaongkir_origin_id,
                    $destination,
                    (string) $weight,
                    strtolower(trim($courier)),
                ],
                $cache
            );
        }

        $data = $response->json('data');

        if (!is_array($data) || empty($data)) {
            $this->logApiFailure(
                $response,
                'domestic-cost',
                ShippingServiceException::API_ERROR,
                [
                    (string) $setting->rajaongkir_origin_id,
                    $destination,
                    (string) $weight,
                    strtolower(trim($courier)),
                ]
            );

            throw new ShippingServiceException(
                'RajaOngkir tidak mengembalikan layanan ongkir yang tersedia.',
                ShippingServiceException::API_ERROR,
                $response->status()
            );
        }

        return $data;
    }
    /**
     * Mengambil tarif dari beberapa kurir lalu menyederhanakannya menjadi
     * kategori Regular, Hemat, dan Kargo. Hasil per kurir di-cache agar
     * perubahan alamat tidak terus menghabiskan kuota API.
     */
    public function calculateGrouped(string $destination, int $weight): array
    {
        $couriers = ['jne', 'jnt', 'sicepat', 'anteraja', 'pos', 'ninja', 'tiki', 'wahana'];
        $cache = Cache::store('file');
        $all = [];
        $errors = [];

        foreach ($couriers as $courier) {
            $key = 'rajaongkir:rates:' . hash('sha256', serialize([$destination, $weight, $courier]));
            try {
                $rates = $cache->remember($key, now()->addMinutes(10), fn () => $this->calculate($destination, $weight, $courier));
                foreach ($rates as $rate) {
                    $rate['code'] = $rate['code'] ?? $courier;
                    $rate['name'] = $rate['name'] ?? strtoupper($courier);
                    $all[] = $rate;
                }
            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
                Log::notice('Courier rate lookup skipped.', ['courier' => $courier, 'reason' => $e->getMessage()]);
            }
        }

        if (!$all) {
            throw new ShippingServiceException(
                $errors[0] ?? 'Tidak ada tarif pengiriman yang tersedia.',
                ShippingServiceException::API_ERROR
            );
        }

        $groups = ['regular' => [], 'hemat' => [], 'kargo' => []];
        foreach ($all as $rate) {
            $service = strtolower(($rate['service'] ?? '') . ' ' . ($rate['description'] ?? ''));
            if (preg_match('/cargo|kargo|trucking|big.?pack|freight/i', $service)) {
                $groups['kargo'][] = $rate;
            } elseif (preg_match('/economy|eco|hemat|saver|cheap/i', $service)) {
                $groups['hemat'][] = $rate;
            } else {
                $groups['regular'][] = $rate;
            }
        }

        // Jika provider tidak memberi label hemat, jadikan tarif termurah
        // yang bukan layanan kargo sebagai opsi Hemat.
        if (!$groups['hemat']) {
            $nonCargo = array_merge($groups['regular']);
            usort($nonCargo, fn ($a, $b) => (int)($a['cost'] ?? 0) <=> (int)($b['cost'] ?? 0));
            if ($nonCargo) $groups['hemat'] = [$nonCargo[0]];
        }
        foreach ($groups as $key => $rates) {
            usort($rates, fn ($a, $b) => (int)($a['cost'] ?? 0) <=> (int)($b['cost'] ?? 0));
            $groups[$key] = $rates ? [$rates[0]] : [];
        }
        return $groups;
    }

}
