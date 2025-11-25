<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the delivery_method enum to add Grab and Lalamove
        DB::statement("ALTER TABLE orders MODIFY COLUMN delivery_method ENUM('Self Collect', 'Shipping', 'Grab', 'Lalamove') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE orders MODIFY COLUMN delivery_method ENUM('Self Collect', 'Shipping') NOT NULL");
    }
};

