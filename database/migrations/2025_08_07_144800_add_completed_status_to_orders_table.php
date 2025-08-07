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
            $table->enum('status', [
                'Order Created',
                'Order Approved', 
                'Design Review',
                'Design Approved',
                'Job Created',
                'Job Start',
                'Job Complete',
                'Order Packaging',
                'Order Finished',
                'Completed',
                'On Hold'
            ])->default('Order Created')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', [
                'Order Created',
                'Order Approved', 
                'Design Review',
                'Design Approved',
                'Job Created',
                'Job Start',
                'Job Complete',
                'Order Packaging',
                'Order Finished',
                'On Hold'
            ])->default('Order Created')->change();
        });
    }
}; 