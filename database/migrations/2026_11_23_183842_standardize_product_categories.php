
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            // Pindahkan produk dari kategori duplikat jika ada
            DB::table('products')
                ->where('product_cat', 11)
                ->update(['product_cat' => 7]);

            DB::table('products')
                ->where('product_cat', 12)
                ->update(['product_cat' => 10]);

            // Standarisasi nama kategori
            DB::table('categories')
                ->where('cat_id', 7)
                ->update(['cat_title' => 'Hair Care']);

            DB::table('categories')
                ->where('cat_id', 10)
                ->update(['cat_title' => 'Sunscreen']);

            // Hapus kategori duplikat
            DB::table('categories')
                ->whereIn('cat_id', [11, 12])
                ->delete();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('categories')->insert([
                [
                    'cat_id' => 11,
                    'cat_title' => 'Hair Care',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'cat_id' => 12,
                    'cat_title' => 'Sunscreen',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);

            DB::table('categories')
                ->where('cat_id', 7)
                ->update(['cat_title' => 'Haircare']);

            DB::table('categories')
                ->where('cat_id', 10)
                ->update(['cat_title' => 'Sun Care']);
        });
    }
};
