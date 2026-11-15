<?php

namespace App\Services;

use App\Models\ShippingSetting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class ShippingService
{
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
            throw new RuntimeException(
                'API Key RajaOngkir belum dikonfigurasi.'
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
    public function searchDestination(string $keyword): array
    {
        $keyword = trim($keyword);

        if ($keyword === '') {
            throw new RuntimeException(
                'Nama lokasi tujuan tidak boleh kosong.'
            );
        }

        $response = $this->api()->get(
            $this->baseUrl . '/destination/domestic-destination',
            [
                'search' => $keyword,
                'limit' => 10,
                'offset' => 0,
            ]
        );

        if ($response->failed()) {
            throw new RuntimeException(
                'Gagal mencari lokasi RajaOngkir: ' .
                $response->body()
            );
        }

        $data = $response->json('data');

        if (!is_array($data)) {
            throw new RuntimeException(
                $response->json('meta.message')
                    ?? 'Format respons pencarian lokasi tidak valid.'
            );
        }

        return $data;
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

        if ($response->failed()) {
            throw new RuntimeException(
                'Gagal menghitung ongkir: ' .
                $response->body()
            );
        }

        $data = $response->json('data');

        if (!is_array($data) || empty($data)) {
            throw new RuntimeException(
                $response->json('meta.message')
                    ?? 'Tidak ditemukan layanan pengiriman untuk tujuan ini.'
            );
        }

        return $data;
    }
}
