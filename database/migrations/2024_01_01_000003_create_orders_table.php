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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('order_id');
            $table->foreignId('client_id')->constrained('clients', 'client_id')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products', 'product_id')->onDelete('set null');
            $table->string('job_name');
            $table->enum('delivery_method', ['Self Collect', 'Shipping']);
            $table->enum('status', ['Pending', 'Approved', 'On Hold', 'In Progress', 'Completed']);
            $table->text('status_comment')->nullable();
            $table->decimal('design_deposit', 10, 2);
            $table->decimal('production_deposit', 10, 2);
            $table->decimal('balance_payment', 10, 2);
            $table->date('due_date_design');
            $table->date('due_date_production');
            $table->text('remarks')->nullable();
            $table->string('receipts')->nullable(); // File path
            $table->string('job_sheet')->nullable(); // File path
            $table->string('download_link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
}; 