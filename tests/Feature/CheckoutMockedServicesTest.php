<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\AdminNotificationService;
use App\Services\MidtransService;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CheckoutMockedServicesTest extends TestCase
{
    use RefreshDatabase;

    public function test_cod_checkout_creates_order_using_mock_shipping_without_calling_midtrans(): void
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
            'weight' => 150,
        ]);

        CartItem::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $destination = [
            'id' => 'synthetic-destination',
            'province_name' => 'Provinsi Contoh',
            'city_name' => 'Kota Contoh',
            'district_name' => 'Kecamatan Contoh',
            'subdistrict_name' => 'Kelurahan Contoh',
            'label' => 'Synthetic Destination',
        ];
        $shipping = Mockery::mock(ShippingService::class);
        $shipping->shouldReceive('searchDestination')
            ->once()
            ->with('12345', Mockery::type('callable'))
            ->andReturn([$destination]);
        $shipping->shouldReceive('calculate')
            ->once()
            ->with('synthetic-destination', 300, 'jne')
            ->andReturn([[
                'code' => 'jne',
                'name' => 'JNE',
                'service' => 'REG',
                'description' => 'Regular',
                'cost' => 9000,
                'etd' => '2-3',
            ]]);
        $this->app->instance(ShippingService::class, $shipping);

        $notification = Mockery::mock(AdminNotificationService::class);
        $notification->shouldReceive('notifyOrderEvent')
            ->once()
            ->with(Mockery::type(Order::class), 'new_order');
        $this->app->instance(AdminNotificationService::class, $notification);

        $midtrans = Mockery::mock(MidtransService::class);
        $midtrans->shouldNotReceive('createSnapToken');
        $this->app->instance(MidtransService::class, $midtrans);

        $this->actingAs($customer)
            ->withSession([
                'cart.selected.' . $customer->id => [$product->id],
            ])
            ->post(route('checkout.store'), [
                'address_id' => $address->id,
                'selected' => [$product->id],
                'payment_method' => 'cod',
                'shipping_courier' => 'jne',
                'shipping_service' => 'REG',
                'delivery_note' => 'Synthetic delivery note',
            ])
            ->assertRedirect();

        $order = Order::query()->where('user_id', $customer->id)->firstOrFail();

        $this->assertSame('pending', $order->status);
        $this->assertSame('cod', $order->payment_method);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame(300, $order->shipping_weight);
        $this->assertSame(9000, $order->shipping_cost);
        $this->assertSame(29000.0, (float) $order->total);
        $this->assertSame('Synthetic address detail', $order->address_detail);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 5,
        ]);
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
