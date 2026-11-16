<?php

namespace Tests\Unit;

use App\Http\Controllers\CheckoutController;
use App\Exceptions\ShippingServiceException;
use App\Models\Address;
use App\Services\AdminNotificationService;
use App\Services\CartService;
use App\Services\MidtransService;
use App\Services\ShippingService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;
use ReflectionMethod;
use RuntimeException;
use Tests\TestCase;

class ShippingDestinationLookupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'rajaongkir.api_key' => 'synthetic-test-key',
            'rajaongkir.base_url' => 'https://rajaongkir.test/api/v1',
            'cache.stores.file.driver' => 'array',
        ]);

        Cache::store('file')->flush();
    }

    public function test_destination_search_reads_all_offset_pages(): void
    {
        $firstPage = array_map(
            fn (int $id) => ['id' => $id],
            range(1, 10)
        );

        Http::fakeSequence()
            ->push(['data' => $firstPage], 200)
            ->push(['data' => [['id' => 11]]], 200);

        Log::shouldReceive('debug')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'RajaOngkir destination search completed.'
                    && $context === [
                        'page_count' => 2,
                        'result_count' => 11,
                        'stopped_after_match' => false,
                    ];
            });

        $destinations = app(ShippingService::class)
            ->searchDestination('Synthetic Village');

        $this->assertCount(11, $destinations);
        Http::assertSentCount(2);
        Http::assertSent(function (ClientRequest $request): bool {
            $query = [];
            parse_str(
                (string) parse_url($request->url(), PHP_URL_QUERY),
                $query
            );

            return ($query['offset'] ?? null) === '10'
                && ($query['limit'] ?? null) === '10';
        });
    }

    public function test_search_cache_reuses_pages_without_mixing_keywords(): void
    {
        Http::fakeSequence()
            ->push(['data' => [['id' => 'location-a']]], 200)
            ->push(['data' => [['id' => 'location-b']]], 200);

        Log::shouldReceive('debug')
            ->times(3)
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'RajaOngkir destination search completed.'
                    && $context === [
                        'page_count' => 1,
                        'result_count' => 1,
                        'stopped_after_match' => false,
                    ];
            });

        $service = app(ShippingService::class);
        $locationA = $service->searchDestination('Synthetic Location A');
        $cachedLocationA = $service->searchDestination('Synthetic Location A');
        $locationB = $service->searchDestination('Synthetic Location B');

        $this->assertSame('location-a', $locationA[0]['id']);
        $this->assertSame($locationA, $cachedLocationA);
        $this->assertSame('location-b', $locationB[0]['id']);
        Http::assertSentCount(2);
    }

    public function test_search_cache_keeps_pages_with_the_same_keyword_separate_by_offset(): void
    {
        $firstPage = array_map(
            fn (int $id) => ['id' => 'page-zero-' . $id],
            range(1, 10)
        );

        Http::fakeSequence()
            ->push(['data' => $firstPage], 200)
            ->push(['data' => [['id' => 'page-ten-result']]], 200);

        Log::shouldReceive('debug')
            ->twice()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'RajaOngkir destination search completed.'
                    && $context['page_count'] === 2
                    && $context['result_count'] === 11;
            });

        $service = app(ShippingService::class);
        $firstSearch = $service->searchDestination('Synthetic Same Keyword');
        $cachedSearch = $service->searchDestination('Synthetic Same Keyword');

        $this->assertCount(11, $firstSearch);
        $this->assertSame($firstSearch, $cachedSearch);
        Http::assertSentCount(2);
    }

    public function test_destination_search_stops_paging_after_a_matching_page(): void
    {
        $match = ['id' => 'synthetic-match'];

        Http::fakeSequence()
            ->push(['data' => [$match]], 200)
            ->push(['data' => [['id' => 'unneeded-page']]], 200);

        Log::shouldReceive('debug')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'RajaOngkir destination search completed.'
                    && ($context['stopped_after_match'] ?? false) === true;
            });

        $results = app(ShippingService::class)->searchDestination(
            'Synthetic Location',
            fn (array $candidate): bool => $candidate['id'] === 'synthetic-match'
        );

        $this->assertSame([$match], $results);
        Http::assertSentCount(1);
    }

    public function test_429_uses_rate_headers_and_blocks_repeated_requests(): void
    {
        $resetAt = now()->addSeconds(90)->timestamp;

        Http::fake(fn () => Http::response([
            'meta' => [
                'message' => 'Rate limit for 99999; key synthetic-test-key',
            ],
        ], 429, [
            'Retry-After' => '75',
            'X-RateLimit-Limit' => '100',
            'X-RateLimit-Remaining' => '0',
            'X-RateLimit-Reset' => (string) $resetAt,
        ]));

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function (string $message, array $context) use ($resetAt): bool {
                $loggedContext = json_encode($context);

                return $message === 'RajaOngkir API request failed.'
                    && ($context['http_status'] ?? null) === 429
                    && ($context['reason'] ?? null) === ShippingServiceException::RATE_LIMITED
                    && ($context['retry_after'] ?? null) === '75'
                    && ($context['x_rate_limit_limit'] ?? null) === '100'
                    && ($context['x_rate_limit_remaining'] ?? null) === '0'
                    && ($context['x_rate_limit_reset'] ?? null) === (string) $resetAt
                    && !str_contains($loggedContext, '99999')
                    && !str_contains($loggedContext, 'synthetic-test-key');
            });

        $service = app(ShippingService::class);

        try {
            $service->searchDestination('99999');
            $this->fail('Expected a rate-limit exception.');
        } catch (ShippingServiceException $exception) {
            $this->assertSame(
                ShippingServiceException::RATE_LIMITED,
                $exception->reason
            );
            $this->assertSame(429, $exception->httpStatus);
            $this->assertSame(75, $exception->retryAfterSeconds);
        }

        try {
            $service->searchDestination('Synthetic Other Location');
            $this->fail('Expected the cached rate-limit cooldown.');
        } catch (ShippingServiceException $exception) {
            $this->assertSame(
                ShippingServiceException::RATE_LIMITED,
                $exception->reason
            );
            $this->assertGreaterThan(0, $exception->retryAfterSeconds);
        }

        Http::assertSentCount(1);
    }

    public function test_quota_exhaustion_is_classified_separately(): void
    {
        Http::fake(fn () => Http::response([
            'meta' => [
                'message' => 'Monthly quota exhausted',
            ],
        ], 429, [
            'Retry-After' => '120',
        ]));

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(fn (string $message, array $context): bool =>
                $message === 'RajaOngkir API request failed.'
                && ($context['reason'] ?? null) === ShippingServiceException::QUOTA_EXHAUSTED
            );

        try {
            app(ShippingService::class)
                ->searchDestination('Synthetic Location');

            $this->fail('Expected a quota exhaustion exception.');
        } catch (ShippingServiceException $exception) {
            $this->assertSame(
                ShippingServiceException::QUOTA_EXHAUSTED,
                $exception->reason
            );
        }
    }

    public function test_destination_search_does_not_log_or_return_api_error_body(): void
    {
        Http::fake(fn () => Http::response([
            'message' => 'Failure for 99999 with key synthetic-test-key',
        ], 401));

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                $loggedContext = json_encode($context);

                return $message === 'RajaOngkir API request failed.'
                    && ($context['http_status'] ?? null) === 401
                    && ($context['reason'] ?? null) === ShippingServiceException::API_ERROR
                    && array_key_exists('response_body', $context)
                    && !str_contains($loggedContext, '99999')
                    && !str_contains($loggedContext, 'synthetic-test-key');
            });

        try {
            app(ShippingService::class)
                ->searchDestination('99999');

            $this->fail('Expected the RajaOngkir request to fail.');
        } catch (RuntimeException $exception) {
            $this->assertSame(
                'RajaOngkir gagal memproses permintaan.',
                $exception->getMessage()
            );
            $this->assertSame(ShippingServiceException::API_ERROR, $exception->reason);
            $this->assertSame(401, $exception->httpStatus);
        }
    }

    public function test_destination_search_sanitizes_connection_errors(): void
    {
        Http::fake(fn () => throw new ConnectionException(
            'Synthetic Private Query synthetic-test-key'
        ));

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'RajaOngkir destination search connection failed.'
                    && $context === [
                        'page_count' => 0,
                        'result_count' => 0,
                    ];
            });

        try {
            app(ShippingService::class)
                ->searchDestination('Synthetic Private Query');

            $this->fail('Expected the RajaOngkir connection to fail.');
        } catch (ShippingServiceException $exception) {
            $this->assertSame(
                'Koneksi pencarian lokasi RajaOngkir gagal.',
                $exception->getMessage()
            );
            $this->assertSame(
                ShippingServiceException::CONNECTION_FAILED,
                $exception->reason
            );
        }
    }

    public function test_destination_search_stops_if_the_api_repeats_a_page(): void
    {
        $page = array_map(
            fn (int $id) => ['id' => $id],
            range(1, 10)
        );

        Http::fakeSequence()
            ->push(['data' => $page], 200)
            ->push(['data' => $page], 200);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'RajaOngkir destination pagination returned a repeated page.'
                    && $context === [
                        'page_count' => 1,
                        'result_count' => 10,
                    ];
            });

        try {
            app(ShippingService::class)
                ->searchDestination('Synthetic Village');

            $this->fail('Expected repeated pagination to fail safely.');
        } catch (ShippingServiceException $exception) {
            $this->assertSame(
                'Pagination pencarian lokasi RajaOngkir tidak bergerak.',
                $exception->getMessage()
            );
            $this->assertSame(ShippingServiceException::API_ERROR, $exception->reason);
        }

        Http::assertSentCount(2);
    }

    public function test_it_matches_a_later_candidate_with_administrative_prefix_variants(): void
    {
        $decoys = array_map(fn (int $id) => [
            'id' => 'decoy-' . $id,
            'province_name' => 'Provinsi Lain',
            'city_name' => 'Kota Lain',
            'district_name' => 'Kecamatan Lain',
            'subdistrict_name' => 'Kelurahan Lain',
        ], range(1, 10));

        $expected = [
            'id' => 'synthetic-destination-11',
            'province_name' => 'Pulau Contoh',
            'city_name' => 'Jakarta Selatan',
            'district_name' => 'Kebayoran Baru',
            'subdistrict_name' => 'Senayan',
        ];

        Http::fakeSequence()
            ->push(['data' => $decoys], 200)
            ->push(['data' => [$expected]], 200);

        Log::shouldReceive('debug')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'RajaOngkir destination search completed.'
                    && $context === [
                        'page_count' => 2,
                        'result_count' => 11,
                        'stopped_after_match' => true,
                    ];
            });

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                return $message === 'RajaOngkir destination matched.'
                    && $context === [
                        'query_type' => 'postal_code',
                        'candidate_count' => 11,
                    ];
            });

        $result = $this->findDestination(
            app(ShippingService::class),
            $this->address()
        );

        $this->assertSame($expected, $result);
        Http::assertSentCount(2);
    }

    public function test_it_rejects_similar_subdistricts_in_a_different_hierarchy(): void
    {
        $candidate = [
            'id' => 'wrong-hierarchy',
            'province_name' => 'Provinsi Tetangga',
            'city_name' => 'Kota Bandung',
            'district_name' => 'Kebayoran Baru',
            'subdistrict_name' => 'Senayan',
        ];

        $shipping = Mockery::mock(ShippingService::class);
        $shipping->shouldReceive('searchDestination')
            ->once()
            ->with('12345', Mockery::type('callable'))
            ->andReturn([$candidate]);
        $shipping->shouldReceive('searchDestination')
            ->once()
            ->with('Kelurahan Senayan', Mockery::type('callable'))
            ->andReturn([$candidate]);

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function (string $message, array $context): bool {
                $loggedContext = json_encode($context);

                return $message === 'RajaOngkir destination lookup found no exact hierarchy match.'
                    && str_contains($loggedContext, '"province":false')
                    && str_contains($loggedContext, '"city":false')
                    && !str_contains($loggedContext, '12345')
                    && !str_contains($loggedContext, 'Senayan')
                    && !str_contains($loggedContext, 'Kelurahan');
            });

        $this->assertNull(
            $this->findDestination($shipping, $this->address())
        );
    }

    public function test_checkout_failure_messages_distinguish_api_conditions(): void
    {
        $controller = $this->makeController(
            Mockery::mock(ShippingService::class)
        );
        $messageMethod = new ReflectionMethod(
            CheckoutController::class,
            'shippingFailureMessage'
        );

        $rateLimit = new ShippingServiceException(
            'rate limit',
            ShippingServiceException::RATE_LIMITED,
            429,
            75
        );
        $quota = new ShippingServiceException(
            'quota',
            ShippingServiceException::QUOTA_EXHAUSTED,
            429,
            120
        );
        $connection = new ShippingServiceException(
            'connection',
            ShippingServiceException::CONNECTION_FAILED
        );
        $apiError = new ShippingServiceException(
            'api error',
            ShippingServiceException::API_ERROR,
            500
        );

        $rateMessage = $messageMethod->invoke($controller, $rateLimit);
        $this->assertStringContainsString('membatasi', $rateMessage);
        $this->assertStringContainsString('75 detik', $rateMessage);
        $this->assertStringContainsString(
            'Kuota',
            $messageMethod->invoke($controller, $quota)
        );
        $this->assertStringContainsString(
            'Koneksi',
            $messageMethod->invoke($controller, $connection)
        );
        $this->assertStringContainsString(
            'kesalahan',
            $messageMethod->invoke($controller, $apiError)
        );

        $responseMethod = new ReflectionMethod(
            CheckoutController::class,
            'shippingFailureResponse'
        );
        $response = $responseMethod->invoke($controller, $rateLimit);

        $this->assertSame(429, $response->getStatusCode());
        $this->assertSame('75', $response->headers->get('Retry-After'));
        $this->assertSame(
            75,
            $response->getData(true)['retry_after_seconds']
        );
    }

    private function findDestination(
        ShippingService $shipping,
        Address $address
    ): ?array {
        $controller = $this->makeController($shipping);

        $method = new ReflectionMethod(
            CheckoutController::class,
            'findDestination'
        );

        return $method->invoke($controller, $shipping, $address);
    }

    private function makeController(
        ShippingService $shipping
    ): CheckoutController {
        return new CheckoutController(
            Mockery::mock(CartService::class),
            $shipping,
            Mockery::mock(MidtransService::class),
            Mockery::mock(AdminNotificationService::class)
        );
    }

    private function address(): Address
    {
        $address = new Address();
        $address->setRawAttributes([
            'province_name' => 'Provinsi Pulau Contoh',
            'city_name' => 'Kota Administrasi Jakarta Selatan',
            'district_name' => 'Kecamatan Kebayoran Baru',
            'village_name' => 'Kelurahan Senayan',
            'postal_code' => '12345',
        ]);

        return $address;
    }
}
