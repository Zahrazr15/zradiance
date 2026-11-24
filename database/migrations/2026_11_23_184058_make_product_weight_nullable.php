
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('weight')->nullable()->change();
        });
    }

    public function down(): void
    {
        if (DB::table('products')->whereNull('weight')->exists()) {
            throw new RuntimeException(
                'Rollback gagal: terdapat produk dengan weight NULL.'
            );
        }

        Schema::table('products', function (Blueprint $table) {
            $table->integer('weight')->nullable(false)->change();
        });
    }
};
