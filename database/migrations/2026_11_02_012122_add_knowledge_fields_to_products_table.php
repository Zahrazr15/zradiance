<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('skin_type')->nullable()->after('description');
            $table->text('ingredients')->nullable()->after('skin_type');
            $table->text('benefits')->nullable()->after('ingredients');
            $table->text('how_to_use')->nullable()->after('benefits');
            $table->text('warnings')->nullable()->after('how_to_use');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['skin_type', 'ingredients', 'benefits', 'how_to_use', 'warnings']);
        });
    }
};