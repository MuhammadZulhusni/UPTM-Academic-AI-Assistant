<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('system_settings')->updateOrInsert(
            ['key' => 'inactive_user_nudge_idle_days'],
            [
                'value' => '14',
                'type' => 'integer',
                'description' => 'Idle days before a student or lecturer is included in the Monday reminder job',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('system_settings')
            ->where('key', 'inactive_user_nudge_idle_days')
            ->delete();
    }
};
