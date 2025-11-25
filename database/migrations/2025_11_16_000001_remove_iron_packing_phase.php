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
        // IMPORTANT: Delete IRON/PACKING jobs FIRST before modifying enum
        // MySQL will fail if we try to modify enum while data with old value exists
        DB::table('production_jobs')->where('phase', 'IRON/PACKING')->delete();
        
        // Update any users with IRON/PACKING phase to NULL (they can be reassigned to QC)
        DB::table('users')->where('phase', 'IRON/PACKING')->update(['phase' => null]);
        
        // Now modify enum to remove IRON/PACKING from production_jobs table
        DB::statement("ALTER TABLE production_jobs MODIFY COLUMN phase ENUM('PRINT', 'PRESS', 'CUT', 'SEW', 'QC') NOT NULL");
        
        // Modify enum to remove IRON/PACKING from users table
        DB::statement("ALTER TABLE users MODIFY COLUMN phase ENUM('PRINT', 'PRESS', 'CUT', 'SEW', 'QC') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore IRON/PACKING phase (if needed for rollback)
        DB::statement("ALTER TABLE production_jobs MODIFY COLUMN phase ENUM('PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING') NOT NULL");
        DB::statement("ALTER TABLE users MODIFY COLUMN phase ENUM('PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING') NULL");
    }
};

