
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Informasi penerima
            $table->string('label', 50)->default('Rumah');
            $table->string('recipient_name');
            $table->string('phone', 20);

            // Data wilayah
            $table->string('province_code')->nullable();
            $table->string('province_name');

            $table->string('city_code')->nullable();
            $table->string('city_name');

            $table->string('district_code')->nullable();
            $table->string('district_name');

            $table->string('village_code')->nullable();
            $table->string('village_name');

            $table->string('postal_code', 10)->nullable();

            // Detail alamat: nama jalan, nomor rumah, patokan, dll.
            $table->text('detail');

            // Penanda alamat utama
            $table->boolean('is_default')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
