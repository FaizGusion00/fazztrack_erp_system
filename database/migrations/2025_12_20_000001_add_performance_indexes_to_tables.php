<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add indexes for performance optimization
     */
    public function up(): void
    {
        // Indexes for orders table
        Schema::table('orders', function (Blueprint $table) {
            // Index for status filtering (very common query)
            $table->index('status', 'orders_status_index');
            
            // Index for created_at (for sorting and date filtering)
            $table->index('created_at', 'orders_created_at_index');
            
            // Composite index for common queries: status + created_at
            $table->index(['status', 'created_at'], 'orders_status_created_at_index');
            
            // Composite index for client_id + status (common filter combination)
            // Note: client_id already has index from foreign key, but composite helps
            $table->index(['client_id', 'status'], 'orders_client_status_index');
        });

        // Indexes for production_jobs table
        Schema::table('production_jobs', function (Blueprint $table) {
            // Index for status filtering
            $table->index('status', 'jobs_status_index');
            
            // Index for phase filtering
            $table->index('phase', 'jobs_phase_index');
            
            // Composite index for order_id + status (common filter)
            // Note: order_id already has index from foreign key
            $table->index(['order_id', 'status'], 'jobs_order_status_index');
            
            // Composite index for phase + status (common filter)
            $table->index(['phase', 'status'], 'jobs_phase_status_index');
        });

        // Indexes for designs table
        Schema::table('designs', function (Blueprint $table) {
            // Index for status filtering
            $table->index('status', 'designs_status_index');
            
            // Composite index for order_id + status (common filter)
            // Note: order_id already has index from foreign key
            $table->index(['order_id', 'status'], 'designs_order_status_index');
            
            // Composite index for designer_id + status (for designer's own designs)
            // Note: designer_id already has index from foreign key
            $table->index(['designer_id', 'status'], 'designs_designer_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_index');
            $table->dropIndex('orders_created_at_index');
            $table->dropIndex('orders_status_created_at_index');
            $table->dropIndex('orders_client_status_index');
        });

        Schema::table('production_jobs', function (Blueprint $table) {
            $table->dropIndex('jobs_status_index');
            $table->dropIndex('jobs_phase_index');
            $table->dropIndex('jobs_order_status_index');
            $table->dropIndex('jobs_phase_status_index');
        });

        Schema::table('designs', function (Blueprint $table) {
            $table->dropIndex('designs_status_index');
            $table->dropIndex('designs_order_status_index');
            $table->dropIndex('designs_designer_status_index');
        });
    }
};

