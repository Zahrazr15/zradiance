
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Jangan rollback jika masih ada produk tanpa harga.
        if (DB::table('products')->whereNull('price')->exists()) {
            throw new RuntimeException(
                'Tidak bisa rollback: terdapat produk dengan harga NULL.'
            );
        }

        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable(false)->change();
        });
    }
};
