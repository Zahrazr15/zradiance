
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'province')) {
                $table->string('province')->nullable();
            }

            if (!Schema::hasColumn('orders', 'city')) {
                $table->string('city')->nullable();
            }

            if (!Schema::hasColumn('orders', 'district')) {
                $table->string('district')->nullable();
            }

            if (!Schema::hasColumn('orders', 'postal_code')) {
                $table->string('postal_code', 10)->nullable();
            }

            if (!Schema::hasColumn('orders', 'address_detail')) {
                $table->text('address_detail')->nullable();
            }

            if (!Schema::hasColumn('orders', 'delivery_note')) {
                $table->text('delivery_note')->nullable();
            }

            if (!Schema::hasColumn('orders', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }

            if (!Schema::hasColumn('orders', 'payment_status')) {
                $table->string('payment_status')->default('unpaid');
            }

            if (!Schema::hasColumn('orders', 'payment_reference')) {
                $table->string('payment_reference')->nullable();
            }

            if (!Schema::hasColumn('orders', 'paid_at')) {
                $table->timestamp('paid_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'province',
                'city',
                'district',
                'postal_code',
                'address_detail',
                'delivery_note',
                'payment_method',
                'payment_status',
                'payment_reference',
                'paid_at',
            ]);
        });
    }
};
