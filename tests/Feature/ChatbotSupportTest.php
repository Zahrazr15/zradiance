<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatbotSupportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'cache.stores.file.driver' => 'array',
            'services.gemini.key' => null,
            'services.support.whatsapp' => '085282985123',
        ]);

        Http::preventStrayRequests();
    }

    private function healthySystem(): void
    {
        config([
            'rajaongkir.api_key' => 'synthetic-test-key',
            'midtrans.server_key' => 'synthetic-server-key',
            'midtrans.client_key' => 'synthetic-client-key',
        ]);

        ShippingSetting::create(['rajaongkir_origin_id' => '123']);
    }

    private function customer(): User
    {
        return User::factory()->create(['role' => 'user']);
    }

    private function address(User $user, array $override = []): Address
    {
        return Address::create(array_merge([
            'user_id' => $user->id,
            'label' => 'Rumah',
            'recipient_name' => 'Pelanggan Uji',
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
            'detail' => 'Jalan Contoh 1',
            'is_default' => true,
        ], $override));
    }

    private function product(array $override = []): Product
    {
        $category = Category::firstOrCreate(['cat_title' => 'Skincare']);

        return Product::create(array_merge([
            'product_cat' => $category->cat_id,
            'brand' => 'Brand Uji',
            'name' => 'Serum Uji',
            'price' => 50000,
            'stock' => 5,
            'weight' => 100,
        ], $override));
    }

    private function ask(User $user, string $message, array $session = [])
    {
        return $this->actingAs($user)
            ->withSession($session)
            ->postJson(route('chatbot.send'), ['message' => $message]);
    }

    private function whatsappLinks(array $json): array
    {
        return array_values(array_filter(
            $json['links'] ?? [],
            fn ($link) => str_starts_with($link['url'], 'https://wa.me/')
        ));
    }

    public function test_empty_cart_is_diagnosed_without_ai_and_needs_no_admin(): void
    {
        $this->healthySystem();
        $user = $this->customer();
        $this->address($user);

        $json = $this->ask($user, 'kenapa aku gak bisa checkout ya')
            ->assertOk()
            ->json();

        $this->assertStringContainsString('Keranjangmu masih kosong', $json['reply']);
        $this->assertSame([], $this->whatsappLinks($json));
        $this->assertContains(route('cart.index'), array_column($json['links'], 'url'));
    }

    public function test_missing_address_and_stock_problems_are_reported(): void
    {
        $this->healthySystem();
        $user = $this->customer();
        $product = $this->product(['stock' => 1]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $json = $this->ask($user, 'checkout gagal terus')->json();

        $this->assertStringContainsString('belum punya alamat', $json['reply']);
        $this->assertStringContainsString('Belum ada produk yang dicentang', $json['reply']);
    }

    public function test_product_without_weight_is_an_admin_side_problem_with_whatsapp_link(): void
    {
        $this->healthySystem();
        $user = $this->customer();
        $this->address($user);
        $product = $this->product(['weight' => null]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $json = $this->ask(
            $user,
            'ongkir tidak muncul',
            ['cart.selected.' . $user->id => [$product->id]]
        )->json();

        $this->assertStringContainsString('Berat produk belum diatur', $json['reply']);

        $links = $this->whatsappLinks($json);
        $this->assertCount(1, $links);
        $this->assertStringStartsWith('https://wa.me/6285282985123?text=', $links[0]['url']);
        $this->assertStringContainsString('0852-8298-5123', $links[0]['label']);
    }

    public function test_missing_shipping_origin_escalates_to_admin(): void
    {
        config([
            'rajaongkir.api_key' => 'synthetic-test-key',
            'midtrans.server_key' => 'x',
            'midtrans.client_key' => 'x',
        ]);

        $user = $this->customer();

        $json = $this->ask($user, 'kenapa ongkir error')->json();

        $this->assertStringContainsString('Lokasi asal pengiriman toko belum diatur', $json['reply']);
        $this->assertCount(1, $this->whatsappLinks($json));
    }

    public function test_expired_online_payment_explains_cancel_and_reorder(): void
    {
        $this->healthySystem();
        $user = $this->customer();

        $order = Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Pelanggan Uji',
            'phone' => '081234567890',
            'address' => 'Alamat contoh',
            'total' => 60000,
            'status' => 'pending',
            'payment_method' => 'online',
            'payment_status' => 'expired',
            'snap_token' => 'token',
        ]);

        $json = $this->ask($user, 'pembayaran saya gagal terus')->json();

        $this->assertStringContainsString("#{$order->id}", $json['reply']);
        $this->assertStringContainsString('Batalkan', $json['reply']);
        $this->assertContains(route('orders.index'), array_column($json['links'], 'url'));
    }

    public function test_paid_but_cancelled_order_is_sent_to_admin(): void
    {
        $this->healthySystem();
        $user = $this->customer();

        Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Pelanggan Uji',
            'phone' => '081234567890',
            'address' => 'Alamat contoh',
            'total' => 60000,
            'status' => 'cancelled',
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
        ]);

        $json = $this->ask($user, 'pesanan saya dibatalkan padahal sudah bayar')->json();

        $this->assertCount(1, $this->whatsappLinks($json));
    }

    public function test_follow_up_after_a_support_answer_escalates_to_admin(): void
    {
        $this->healthySystem();
        $user = $this->customer();

        $json = $this->ask(
            $user,
            'masih gagal',
            ['chatbot.support_topic' => 'payment']
        )->json();

        $this->assertCount(1, $this->whatsappLinks($json));
    }

    public function test_explicit_request_for_admin_returns_whatsapp_link(): void
    {
        $user = $this->customer();

        $json = $this->ask($user, 'aku mau komplain, hubungi admin dong')->json();

        $this->assertCount(1, $this->whatsappLinks($json));
    }

    public function test_informational_question_does_not_trigger_support_diagnosis(): void
    {
        $this->healthySystem();
        $this->seed(\Database\Seeders\KnowledgeSeeder::class);
        $user = $this->customer();

        $json = $this->ask($user, 'Bagaimana cara memesan produk?')->json();

        $this->assertStringNotContainsString('Aku cek akunmu', $json['reply']);
        $this->assertSame([], $this->whatsappLinks($json));
    }

    public function test_gemini_receives_diagnosis_and_history_and_marker_becomes_whatsapp_button(): void
    {
        $this->healthySystem();
        config([
            'services.gemini.key' => 'synthetic-gemini-key',
            'services.gemini.model' => 'gemini-2.5-flash',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => "Keranjangmu kosong, tambahkan produk dulu ya.\n[[HUBUNGI_ADMIN]]",
                    ]]],
                ]],
            ]),
        ]);

        $user = $this->customer();
        $this->address($user);

        $json = $this->ask($user, 'kenapa aku tidak bisa checkout')->json();

        $this->assertSame('Keranjangmu kosong, tambahkan produk dulu ya.', $json['reply']);
        $this->assertCount(1, $this->whatsappLinks($json));

        Http::assertSent(function ($request) {
            $body = $request->data();
            $last = end($body['contents']);

            return isset($body['systemInstruction'])
                && str_contains($last['parts'][0]['text'], 'Keranjangmu masih kosong')
                && str_contains($last['parts'][0]['text'], '<pesan_pelanggan>');
        });
    }

    public function test_unanswerable_question_with_ai_down_offers_whatsapp(): void
    {
        config([
            'services.gemini.key' => 'synthetic-gemini-key',
            'services.gemini.model' => 'gemini-2.5-flash',
        ]);

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response(['error' => ['message' => 'quota']], 429),
        ]);

        $user = $this->customer();

        $json = $this->ask($user, 'qwerty zxcvb lorem')->json();

        $this->assertNotEmpty($json['reply']);
        $this->assertCount(1, $this->whatsappLinks($json));
    }

    public function test_internal_metadata_is_never_sent_to_the_browser(): void
    {
        $this->healthySystem();
        $user = $this->customer();

        $json = $this->ask($user, 'kenapa aku gak bisa checkout')->json();

        foreach (array_keys($json) as $key) {
            $this->assertStringStartsNotWith('_', $key);
        }
    }
}
