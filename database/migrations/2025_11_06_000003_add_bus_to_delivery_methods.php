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
        Schema::table('orders', function (Blueprint $table) {
            // Extend delivery_method enum to include 'Bus'
            $table->enum('delivery_method', ['Self Collect', 'Shipping', 'Grab', 'Lalamove', 'Bus'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert to previous set without 'Bus'
            $table->enum('delivery_method', ['Self Collect', 'Shipping', 'Grab', 'Lalamove'])->change();
        });
    }
};


