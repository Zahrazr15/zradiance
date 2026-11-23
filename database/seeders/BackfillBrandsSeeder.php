<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BackfillBrandsSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $brandNames = DB::table('products')
                ->whereNotNull('brand')
                ->where('brand', '!=', '')
                ->distinct()
                ->orderBy('brand')
                ->pluck('brand');

            foreach ($brandNames as $brandName) {
                $brandName = trim($brandName);

                if ($brandName === '') {
                    continue;
                }

                $brand = DB::table('brands')
                    ->where('brand_name', $brandName)
                    ->first();

                if (!$brand) {
                    $baseSlug = Str::slug($brandName);
                    $slug = $baseSlug;
                    $suffix = 1;

                    while (
                        DB::table('brands')
                            ->where('brand_slug', $slug)
                            ->exists()
                    ) {
                        $slug = $baseSlug . '-' . $suffix;
                        $suffix++;
                    }

                    $brandId = DB::table('brands')->insertGetId([
                        'brand_name' => $brandName,
                        'brand_slug' => $slug,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ], 'brand_id');
                } else {
                    $brandId = $brand->brand_id;
                }

                $updated = DB::table('products')
                    ->where('brand', $brandName)
                    ->whereNull('brand_id')
                    ->update([
                        'brand_id' => $brandId,
                        'updated_at' => now(),
                    ]);

                $this->command->info(
                    "{$brandName}: brand_id {$brandId}, {$updated} product(s) linked."
                );
            }
        });

        $this->command->info('Brand backfill completed successfully.');
    }
}
