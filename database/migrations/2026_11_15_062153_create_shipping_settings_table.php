<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id();

            // Identitas lokasi toko
            $table->string('store_name')->default('ZRadiance');
            $table->string('province_name')->nullable();
            $table->string('province_code')->nullable();

            $table->string('city_name')->nullable();
            $table->string('city_code')->nullable();

            $table->string('district_name')->nullable();
            $table->string('district_code')->nullable();

            // ID lokasi yang digunakan RajaOngkir
            $table->string('rajaongkir_origin_id')->nullable();

            // Alamat lengkap toko atau gudang
            $table->text('address')->nullable();
            $table->string('postal_code', 10)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_settings');
    }
};
