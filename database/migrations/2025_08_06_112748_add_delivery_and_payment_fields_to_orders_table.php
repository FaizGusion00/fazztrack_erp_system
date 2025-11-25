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
            // Delivery Management Fields
            $table->enum('delivery_status', ['Pending', 'In Transit', 'Delivered', 'Failed'])->default('Pending')->after('status');
            $table->string('tracking_number')->nullable()->after('delivery_status');
            $table->timestamp('delivery_date')->nullable()->after('tracking_number');
            $table->text('delivery_notes')->nullable()->after('delivery_date');
            $table->string('delivery_company')->nullable()->after('delivery_notes');
            
            // Payment Collection Fields
            $table->enum('payment_status', ['Pending', 'Partial', 'Completed', 'Overdue'])->default('Pending')->after('delivery_company');
            $table->decimal('paid_amount', 10, 2)->default(0.00)->after('payment_status');
            $table->timestamp('last_payment_date')->nullable()->after('paid_amount');
            $table->text('payment_notes')->nullable()->after('last_payment_date');
            $table->date('payment_due_date')->nullable()->after('payment_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_status',
                'tracking_number', 
                'delivery_date',
                'delivery_notes',
                'delivery_company',
                'payment_status',
                'paid_amount',
                'last_payment_date',
                'payment_notes',
                'payment_due_date'
            ]);
        });
    }
};
