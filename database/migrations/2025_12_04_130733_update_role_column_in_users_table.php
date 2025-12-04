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
            // Update the enum to include student and lecturer
            $table->enum('role', ['admin', 'student', 'lecturer'])
                  ->default('student')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Revert to previous enum
            $table->enum('role', ['admin', 'user'])
                  ->default('user')
                  ->change();
        });
    }
};
