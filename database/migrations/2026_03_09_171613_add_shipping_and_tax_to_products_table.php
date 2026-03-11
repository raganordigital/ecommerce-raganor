<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('shipping_cost', 10, 2)->nullable()->after('allow_cod');
            $table->decimal('tax_rate', 5, 2)->nullable()->after('shipping_cost');
            $table->boolean('free_shipping')->default(false)->after('tax_rate');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['shipping_cost', 'tax_rate', 'free_shipping']);
        });
    }
};