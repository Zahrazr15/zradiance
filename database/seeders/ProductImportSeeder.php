<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductImportSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seed-data/zradiance-products.json');

        if (!file_exists($path)) {
            $this->command->error('Dataset JSON tidak ditemukan.');
            return;
        }

        $products = json_decode(file_get_contents($path), true);

        if (!is_array($products)) {
            $this->command->error('Format JSON tidak valid.');
            return;
        }

        $categoryMap = [
            5  => 'Skincare',
            6  => 'Makeup',
            7  => 'Hair Care',
            8  => 'Body Care',
            9  => 'Fragrance',
            10 => 'Sunscreen',
        ];

        $imported = 0;
        $updated = 0;
        $skipped = 0;
        $withoutPrice = 0;

        DB::transaction(function () use (
            $products,
            $categoryMap,
            &$imported,
            &$updated,
            &$skipped,
            &$withoutPrice
        ) {
            foreach ($products as $item) {
                $name = trim($item['name'] ?? '');
                $brandName = trim($item['brand'] ?? '');
                $legacyCategoryId = $item['product_cat'] ?? null;

                if (
                    $name === '' ||
                    $brandName === '' ||
                    !isset($categoryMap[$legacyCategoryId])
                ) {
                    $this->command->warn(
                        'Produk dilewati: nama, brand, atau kategori tidak valid.'
                    );
                    $skipped++;
                    continue;
                }

                $categoryName = $categoryMap[$legacyCategoryId];

                $category = Category::firstOrCreate([
                    'cat_title' => $categoryName,
                ]);

                $brand = Brand::where('brand_name', $brandName)->first();

                if (!$brand) {
                    $baseSlug = Str::slug($brandName);
                    $slug = $baseSlug;
                    $suffix = 1;

                    while (Brand::where('brand_slug', $slug)->exists()) {
                        $slug = $baseSlug . '-' . $suffix;
                        $suffix++;
                    }

                    $brand = Brand::create([
                        'brand_name' => $brandName,
                        'brand_slug' => $slug,
                        'is_active' => true,
                    ]);
                }

                $ingredients = $item['ingredients'] ?? null;

                if (is_array($ingredients)) {
                    $ingredients = implode(', ', $ingredients);
                }

                $price = isset($item['price']) &&
                    is_numeric($item['price']) &&
                    $item['price'] > 0
                    ? $item['price']
                    : null;

                if ($price === null) {
                    $withoutPrice++;
                }

                $existingProduct = Product::where('name', $name)
                    ->where('brand', $brandName)
                    ->exists();

                Product::updateOrCreate(
                    [
                        'name' => $name,
                        'brand' => $brandName,
                    ],
                    [
                        'product_cat' => $category->cat_id,
                        'brand_id' => $brand->brand_id,
                        'description' => $item['description'] ?? null,
                        'price' => $price,
                        'stock' => $item['stock'] ?? 0,
                        'weight' => $item['weight'] ?? null,
                        'image' => $item['image'] ?? null,
                        'skin_type' => $item['skin_type'] ?? null,
                        'ingredients' => $ingredients,
                        'benefits' => $item['benefits'] ?? null,
                        'how_to_use' => $item['how_to_use'] ?? null,
                        'warnings' => $item['warnings'] ?? null,
                    ]
                );

                if ($existingProduct) {
                    $updated++;
                } else {
                    $imported++;
                }
            }
        });

        $this->command->newLine();
        $this->command->info('===== PRODUCT IMPORT REPORT =====');
        $this->command->info("Produk baru: {$imported}");
        $this->command->info("Produk diperbarui: {$updated}");
        $this->command->warn("Produk dilewati: {$skipped}");
        $this->command->warn("Produk tanpa harga: {$withoutPrice}");
        $this->command->info('Import selesai.');
    }
}
