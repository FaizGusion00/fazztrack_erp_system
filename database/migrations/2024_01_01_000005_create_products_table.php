<?php

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
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('size'); // S, M, L, XL, XXL, etc.
            $table->decimal('price', 10, 2);
            $table->integer('stock')->default(0);
            $table->json('images')->nullable(); // Store multiple product images
            $table->text('comments')->nullable(); // Comments/notes about the product
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->string('category')->nullable(); // T-Shirt, Hoodie, Cap, etc.
            $table->string('color')->nullable();
            $table->string('material')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
}; 