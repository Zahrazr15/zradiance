<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Product;
use App\Models\ShippingSetting;
use App\Models\User;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ShippingAndErrorHandlingTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_unknown_route_returns_not_found(): void
    {
        $this->get('/path-that-does-not-exist')
            ->assertNotFound();
    }

    public function test_server_error_response_does_not_expose_exception_details(): void
    {
        config(['app.debug' => false]);

        Route::get('/__test/synthetic-error', function () {
            throw new \RuntimeException('synthetic-secret-detail');
        });

        $response = $this->get('/__test/synthetic-error');

        $response->assertStatus(500)
            ->assertDontSee('synthetic-secret-detail');
    }

    public function test_wilayah_api_failure_returns_sanitized_error(): void
    {
        Http::fake([
            'https://wilayah.id/api/provinces.json' => Http::response([
                'message' => 'synthetic private upstream detail',
            ], 503),
        ]);

        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($customer)
            ->getJson(route('wilayah.provinces'))
            ->assertStatus(502)
            ->assertJson([
                'message' => 'Gagal mengambil data wilayah dari server.',
            ])
            ->assertDontSee('synthetic private upstream detail');
    }

    public function test_shipping_cost_calculation_uses_a_mocked_form_request(): void
    {
        ShippingSetting::create([
            'store_name' => 'Synthetic Store',
            'rajaongkir_origin_id' => 'synthetic-origin',
        ]);

        Http::fake([
            'https://rajaongkir.test/api/v1/calculate/domestic-cost' =>
                Http::response([
                    'data' => [[
                        'code' => 'jne',
                        'name' => 'JNE',
                        'service' => 'REG',
                        'description' => 'Regular',
                        'cost' => 12000,
                        'etd' => '2-3',
                    ]],
                ], 200),
        ]);

        $result = app(ShippingService::class)
            ->calculate('synthetic-destination', 300, 'JNE');

        $this->assertSame('jne', $result[0]['code']);
        $this->assertSame(12000, $result[0]['cost']);

        Http::assertSent(function (ClientRequest $request): bool {
            $data = $request->data();

            return str_ends_with(
                $request->url(),
                '/calculate/domestic-cost'
            ) &&
                ($data['origin'] ?? null) === 'synthetic-origin' &&
                ($data['destination'] ?? null) === 'synthetic-destination' &&
                (string) ($data['weight'] ?? '') === '300' &&
                ($data['courier'] ?? null) === 'jne' &&
                ($data['price'] ?? null) === 'lowest';
        });
    }

    public function test_checkout_returns_rate_limit_and_does_not_repeat_the_api_request(): void
    {
        $customer = User::factory()->create(['role' => 'user']);
        $address = $this->createAddress($customer);
        $category = Category::create(['cat_title' => 'Skincare']);
        $product = Product::create([
            'product_cat' => $category->cat_id,
            'brand' => 'Synthetic Brand',
            'name' => 'Synthetic Product',
            'price' => 10000,
            'stock' => 5,
            'weight' => 200,
        ]);

        CartItem::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        Http::fake(fn () => Http::response([
            'meta' => ['message' => 'Synthetic rate limit'],
        ], 429, [
            'Retry-After' => '45',
            'X-RateLimit-Limit' => '10',
            'X-RateLimit-Remaining' => '0',
        ]));

        $this->actingAs($customer)
            ->withSession([
                'cart.selected.' . $customer->id => [$product->id],
            ]);

        $payload = [
            'address_id' => $address->id,
            'selected' => [$product->id],
            'courier' => 'jne',
        ];

        $this->postJson(route('checkout.shipping-cost'), $payload)
            ->assertStatus(429)
            ->assertJsonPath('success', false)
            ->assertJsonPath('retry_after_seconds', 45);

        $this->postJson(route('checkout.shipping-cost'), $payload)
            ->assertStatus(429)
            ->assertJsonPath('success', false);

        Http::assertSentCount(1);
    }

    private function createAddress(User $user): Address
    {
        return Address::create([
            'user_id' => $user->id,
            'label' => 'Synthetic',
            'recipient_name' => 'Synthetic Recipient',
            'phone' => '081234567890',
            'province_code' => '31',
            'province_name' => 'Provinsi Contoh',
            'city_code' => '3171',
            'city_name' => 'Kota Contoh',
            'district_code' => '317101',
            'district_name' => 'Kecamatan Contoh',
            'village_code' => '31710101',
            'village_name' => 'Kelurahan Contoh',
            'postal_code' => '12345',
            'detail' => 'Synthetic address detail',
        ]);
    }
}
