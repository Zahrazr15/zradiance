<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicAndCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_home_login_and_registration_pages(): void
    {
        $this->get(route('home'))->assertOk();
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();
    }

    public function test_guest_cannot_open_the_customer_product_catalog(): void
    {
        $this->get(route('products.index'))
            ->assertRedirect(route('login'));
    }

    public function test_customer_can_search_filter_and_sort_products(): void
    {
        $skincare = Category::create(['cat_title' => 'Skincare']);
        $makeup = Category::create(['cat_title' => 'Makeup']);

        $lowPrice = $this->createProduct($skincare, [
            'brand' => 'Glow Lab',
            'name' => 'Glow Serum',
            'price' => 100,
        ]);
        $highPrice = $this->createProduct($skincare, [
            'brand' => 'Glow Lab',
            'name' => 'Glow Cream',
            'price' => 300,
        ]);
        $otherCategory = $this->createProduct($makeup, [
            'brand' => 'Glow Lab',
            'name' => 'Glow Lipstick',
            'price' => 200,
        ]);
        $unmatched = $this->createProduct($skincare, [
            'brand' => 'Other Brand',
            'name' => 'Cleanser',
            'price' => 50,
        ]);

        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($customer)
            ->get(route('products.index', ['q' => 'Serum']))
            ->assertOk()
            ->assertViewHas('products', fn ($products) =>
                $products->total() === 1 &&
                $products->getCollection()->pluck('id')->all() === [$lowPrice->id]
            );

        $this->get(route('products.index', [
            'category' => $makeup->cat_id,
        ]))
            ->assertOk()
            ->assertViewHas('products', fn ($products) =>
                $products->total() === 1 &&
                $products->getCollection()->pluck('id')->all() === [$otherCategory->id]
            );

        $this->get(route('products.index', [
            'q' => 'Glow Lab',
            'category' => $skincare->cat_id,
            'sort' => 'price_low',
        ]))
            ->assertOk()
            ->assertViewHas('products', fn ($products) =>
                $products->getCollection()->pluck('id')->all() === [
                    $lowPrice->id,
                    $highPrice->id,
                ] &&
                !$products->getCollection()->contains('id', $otherCategory->id) &&
                !$products->getCollection()->contains('id', $unmatched->id)
            );
    }

    public function test_customer_can_open_a_product_detail(): void
    {
        $category = Category::create(['cat_title' => 'Skincare']);
        $product = $this->createProduct($category);
        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($customer)
            ->get(route('products.show', $product))
            ->assertOk()
            ->assertViewHas('product', fn ($viewProduct) =>
                $viewProduct->is($product)
            )
            ->assertViewHas('canReview', false);
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
