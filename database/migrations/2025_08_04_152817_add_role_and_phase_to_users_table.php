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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->after('id');
            $table->enum('role', ['SuperAdmin', 'Admin', 'Sales Manager', 'Designer', 'Production Staff'])->default('Admin')->after('email');
            $table->enum('phase', ['PRINT', 'PRESS', 'CUT', 'SEW', 'QC', 'IRON/PACKING'])->nullable()->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'phase']);
        });
    }
};
