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
        Schema::create('production_jobs', function (Blueprint $table) {
            $table->id('job_id');
            $table->foreignId('order_id')->constrained('orders', 'order_id')->onDelete('cascade');
            $table->enum('phase', ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING']);
            $table->enum('status', ['Pending', 'In Progress', 'Completed']);
            $table->string('qr_code')->unique();
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->integer('duration')->nullable(); // in minutes
            $table->text('remarks')->nullable();
            $table->integer('start_quantity')->nullable();
            $table->integer('end_quantity')->nullable();
            $table->integer('reject_quantity')->nullable();
            $table->string('reject_status')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_jobs');
    }
}; 