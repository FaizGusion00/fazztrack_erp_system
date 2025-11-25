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
        Schema::create('design_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('template_files'); // Store multiple design files
            $table->string('category')->nullable(); // e.g., 'T-Shirt', 'Hoodie', 'Cap'
            $table->string('tags')->nullable(); // Comma-separated tags
            $table->unsignedBigInteger('created_by'); // Designer who created the template
            $table->boolean('is_public')->default(false); // Whether other designers can see it
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('design_templates');
    }
};
