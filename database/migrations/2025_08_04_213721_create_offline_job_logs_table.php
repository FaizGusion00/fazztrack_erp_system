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
        Schema::create('offline_job_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('user_id'); // Production staff who performed the action
            $table->string('action'); // 'start', 'end', 'pause', 'resume'
            $table->timestamp('action_time'); // When the action was performed offline
            $table->text('notes')->nullable(); // Any notes from production staff
            $table->json('offline_data')->nullable(); // Store any additional offline data
            $table->boolean('synced')->default(false); // Whether this log has been synced
            $table->timestamp('synced_at')->nullable(); // When it was synced
            $table->timestamps();
            
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['job_id', 'synced']);
            $table->index(['user_id', 'synced']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_job_logs');
    }
};
