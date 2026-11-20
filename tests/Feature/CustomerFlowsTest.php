<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CustomerFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_a_customer_and_rejects_duplicate_email(): void
    {
        $payload = [
            'name' => 'Synthetic Customer',
            'email' => 'customer@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $this->post(route('register'), $payload)
            ->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'email' => $payload['email'],
            'role' => 'user',
        ]);

        $this->from(route('register'))
            ->post(route('register'), $payload)
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors('email');
    }

    public function test_registration_requires_name_valid_email_and_confirmed_password(): void
    {
        $this->from(route('register'))
            ->post(route('register'), [
                'name' => '',
                'email' => 'not-an-email',
                'password' => 'secret',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['name', 'email', 'password']);

        $this->assertDatabaseCount('users', 0);
    }

    public function test_login_rejects_wrong_password_and_accepts_valid_credentials(): void
    {
        $customer = User::factory()->create([
            'email' => 'customer@example.test',
            'password' => 'password123',
            'role' => 'user',
        ]);

        $this->from(route('login'))
            ->post(route('login'), [
                'email' => $customer->email,
                'password' => 'incorrect',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->post(route('login'), [
            'email' => $customer->email,
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($customer);
    }

    public function test_login_is_rate_limited_after_five_failed_attempts(): void
    {
        $customer = User::factory()->create([
            'email' => 'ratelimit@example.test',
            'password' => 'password123',
            'role' => 'user',
        ]);

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->post(route('login'), [
                'email' => $customer->email,
                'password' => 'wrong-password',
            ])->assertRedirect()
            ->assertSessionHasErrors('email');
        }

        $this->post(route('login'), [
            'email' => $customer->email,
            'password' => 'wrong-password',
        ])->assertTooManyRequests();
    }

    public function test_customer_can_log_out(): void
    {
        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($customer)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_customer_can_view_and_update_profile(): void
    {
        $customer = User::factory()->create([
            'name' => 'Before Update',
            'email' => 'before@example.test',
            'role' => 'user',
        ]);

        $this->actingAs($customer)
            ->get(route('profile'))
            ->assertOk()
            ->assertViewHas('addresses');

        $this->put(route('profile.update'), [
            'name' => 'After Update',
            'email' => 'after@example.test',
            'phone' => '+628123456789',
        ])->assertRedirect(route('profile'));

        $this->assertDatabaseHas('users', [
            'id' => $customer->id,
            'name' => 'After Update',
            'email' => 'after@example.test',
            'phone' => '+628123456789',
        ]);
    }

    public function test_customer_can_create_and_update_an_address_but_not_another_users_address(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $otherCustomer = User::factory()->create(['role' => 'user']);

        $this->actingAs($owner)
            ->post(route('addresses.store'), $this->addressData())
            ->assertRedirect(route('profile'));

        $address = Address::query()->where('user_id', $owner->id)->firstOrFail();
        $this->assertTrue($address->is_default);

        $updated = $this->addressData();
        $updated['label'] = 'Kantor';
        $updated['detail'] = 'Synthetic updated address';

        $this->put(route('addresses.update', $address), $updated)
            ->assertRedirect(route('profile'));

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'label' => 'Kantor',
            'detail' => 'Synthetic updated address',
        ]);

        $this->actingAs($otherCustomer)
            ->put(route('addresses.update', $address), $updated)
            ->assertForbidden();
    }

    public function test_order_history_and_details_are_scoped_to_the_customer(): void
    {
        $owner = User::factory()->create(['role' => 'user']);
        $otherCustomer = User::factory()->create(['role' => 'user']);
        $ownOrder = $this->createOrder($owner);
        $otherOrder = $this->createOrder($otherCustomer);

        $this->actingAs($owner)
            ->get(route('orders.index'))
            ->assertOk()
            ->assertViewHas('orders', fn ($orders) =>
                $orders->pluck('id')->all() === [$ownOrder->id]
            );

        $this->get(route('orders.show', $ownOrder))->assertOk();
        $this->get(route('orders.show', $otherOrder))->assertForbidden();
    }

    public function test_cart_addition_respects_stock_and_product_can_be_removed(): void
    {
        $customer = User::factory()->create(['role' => 'user']);
        $category = Category::create(['cat_title' => 'Skincare']);
        $product = $this->createProduct($category, ['stock' => 2]);

        $this->actingAs($customer)
            ->post(route('cart.add', $product))
            ->assertRedirect();

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->post(route('cart.increase', $product->id));
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->post(route('cart.increase', $product->id))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->delete(route('cart.remove', $product->id))
            ->assertRedirect(route('cart.index'));

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $customer->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_checkout_requires_authentication_and_validates_an_owned_address(): void
    {
        $this->get(route('checkout.index'))
            ->assertRedirect(route('login'));

        $customer = User::factory()->create(['role' => 'user']);
        $category = Category::create(['cat_title' => 'Skincare']);
        $product = $this->createProduct($category);

        CartItem::create([
            'user_id' => $customer->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $shipping = Mockery::mock(ShippingService::class);
        $shipping->shouldNotReceive('searchDestination');
        $this->app->instance(ShippingService::class, $shipping);

        $this->actingAs($customer)
            ->withSession([
                'cart.selected.' . $customer->id => [$product->id],
            ])
            ->from(route('cart.index'))
            ->post(route('checkout.store'), [
                'address_id' => 99999,
                'selected' => [$product->id],
                'payment_method' => 'cod',
                'shipping_courier' => 'jne',
                'shipping_service' => 'REG',
            ])
            ->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors('address_id');
    }

    private function addressData(): array
    {
        return [
            'label' => 'Rumah',
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
        ];
    }

    private function createOrder(User $user): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Synthetic Recipient',
            'phone' => '081234567890',
            'address' => 'Synthetic address',
            'total' => 1000,
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
        ]);
    }

    private function createProduct(
        Category $category,
        array $overrides = []
    ): Product {
        return Product::create(array_merge([
            'product_cat' => $category->cat_id,
            'brand' => 'Synthetic Brand',
            'name' => 'Synthetic Product',
            'price' => 100,
            'stock' => 5,
            'weight' => 100,
        ], $overrides));
    }
}
