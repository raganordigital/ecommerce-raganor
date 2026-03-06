<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('restrict');
            $table->string('product_name'); // Snapshot of product name at time of order
            $table->string('product_sku'); // Snapshot of SKU
            $table->decimal('price', 10, 2); // Price at time of purchase
            $table->integer('quantity');
            $table->decimal('subtotal', 10, 2); // price * quantity
            $table->json('options')->nullable(); // For product options/variations if needed
            $table->timestamps();
            
            // Indexes
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};