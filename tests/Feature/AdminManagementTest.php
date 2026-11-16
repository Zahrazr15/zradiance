<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_dashboard_and_customer_is_forbidden(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();

        $this->actingAs($customer)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->get(route('admin.products.index'))
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_a_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['cat_title' => 'Skincare']);
        $payload = $this->productData($category);

        $this->actingAs($admin)
            ->get(route('admin.products.index'))
            ->assertOk()
            ->assertViewHas('products');

        $this->post(route('admin.products.store'), $payload)
            ->assertRedirect(route('admin.products.index'));

        $product = Product::query()->firstOrFail();
        $this->assertSame('Synthetic Product', $product->name);

        $payload['name'] = 'Updated Synthetic Product';
        $this->put(route('admin.products.update', $product), $payload)
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Synthetic Product',
        ]);

        $this->delete(route('admin.products.destroy', $product))
            ->assertRedirect(route('admin.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_product_validation_rejects_bad_price_stock_and_weight(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $category = Category::create(['cat_title' => 'Skincare']);
        $payload = $this->productData($category, [
            'price' => 'not-a-price',
            'stock' => -1,
            'weight' => 0,
        ]);

        $this->actingAs($admin)
            ->from(route('admin.products.create'))
            ->post(route('admin.products.store'), $payload)
            ->assertRedirect(route('admin.products.create'))
            ->assertSessionHasErrors(['price', 'stock', 'weight']);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_admin_can_manage_categories_without_cascading_product_deletion(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'cat_title' => 'Skincare',
                'description' => 'Initial description',
            ])
            ->assertRedirect(route('admin.categories.index'));

        $category = Category::query()->firstOrFail();
        $this->put(route('admin.categories.update', $category), [
            'cat_title' => 'Updated Skincare',
            'description' => 'Updated description',
        ])->assertRedirect(route('admin.categories.index'));

        $product = Product::create($this->productData($category));

        $this->from(route('admin.categories.index'))
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('categories', ['cat_id' => $category->cat_id]);
        $this->assertDatabaseHas('products', ['id' => $product->id]);

        $this->delete(route('admin.products.destroy', $product));
        $this->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['cat_id' => $category->cat_id]);
    }

    public function test_admin_can_view_orders_and_process_cod_once_with_status_guards(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'user']);
        $category = Category::create(['cat_title' => 'Skincare']);
        $product = Product::create($this->productData($category, ['stock' => 5]));
        $order = $this->createOrder($customer, 'cod');

        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => 100,
            'quantity' => 2,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertViewHas('orders', fn ($orders) =>
                $orders->contains('id', $order->id)
            );

        $this->get(route('admin.orders.show', $order))->assertOk();

        $this->put(route('admin.orders.update', $order), [
            'status' => 'processing',
        ])->assertRedirect(route('admin.orders.show', $order));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);

        $this->put(route('admin.orders.update', $order), [
            'status' => 'processing',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 3,
        ]);

        $this->from(route('admin.orders.show', $order))
            ->put(route('admin.orders.update', $order), [
                'status' => 'cancelled',
            ])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'processing',
        ]);
    }

    public function test_unpaid_online_order_cannot_enter_processing(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'user']);
        $order = $this->createOrder($customer, 'online');

        $this->actingAs($admin)
            ->from(route('admin.orders.show', $order))
            ->put(route('admin.orders.update', $order), [
                'status' => 'processing',
            ])
            ->assertRedirect(route('admin.orders.show', $order))
            ->assertSessionHasErrors('status');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_update_shipping_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->put(route('admin.shipping-settings.update'), [
                'store_name' => 'Synthetic Store',
                'province_name' => 'Provinsi Contoh',
                'province_code' => '31',
                'city_name' => 'Kota Contoh',
                'city_code' => '3171',
                'district_name' => 'Kecamatan Contoh',
                'district_code' => '317101',
                'rajaongkir_origin_id' => 'synthetic-origin',
                'address' => 'Synthetic store address',
                'postal_code' => '12345',
            ])
            ->assertRedirect(route('admin.shipping-settings.edit'));

        $this->assertDatabaseHas('shipping_settings', [
            'store_name' => 'Synthetic Store',
            'rajaongkir_origin_id' => 'synthetic-origin',
        ]);

        $this->get(route('admin.shipping-settings.edit'))
            ->assertOk()
            ->assertViewHas('shippingSetting');
    }

    private function productData(
        Category $category,
        array $overrides = []
    ): array {
        return array_merge([
            'product_cat' => $category->cat_id,
            'brand' => 'Synthetic Brand',
            'name' => 'Synthetic Product',
            'price' => 100,
            'stock' => 5,
            'weight' => 100,
            'description' => 'Synthetic description',
        ], $overrides);
    }

    private function createOrder(User $user, string $paymentMethod): Order
    {
        return Order::create([
            'user_id' => $user->id,
            'recipient_name' => 'Synthetic Recipient',
            'phone' => '081234567890',
            'address' => 'Synthetic address',
            'total' => 1000,
            'status' => 'pending',
            'payment_method' => $paymentMethod,
            'payment_status' => 'unpaid',
        ]);
    }
}
