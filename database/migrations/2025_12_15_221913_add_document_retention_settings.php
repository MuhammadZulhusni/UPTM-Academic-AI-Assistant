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
        // Add default document retention settings to system_settings table
        DB::table('system_settings')->insert([
            [
                'key' => 'document_retention_days',
                'value' => '90', // Default: 90 days (3 months)
                'type' => 'integer',
                'description' => 'Number of days to keep user-generated documents before auto-deletion',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'document_auto_cleanup',
                'value' => '1', // Default: Enabled
                'type' => 'boolean',
                'description' => 'Enable automatic cleanup of old documents',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')
            ->whereIn('key', ['document_retention_days', 'document_auto_cleanup'])
            ->delete();
    }
};