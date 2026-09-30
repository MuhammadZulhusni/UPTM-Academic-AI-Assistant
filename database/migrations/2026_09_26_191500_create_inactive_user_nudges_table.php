<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inactive_user_nudges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('days_inactive');
            $table->string('subject');
            $table->text('body');
            $table->string('status')->default('sent');
            $table->string('trigger')->default('scheduled');
            $table->string('model')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });

        DB::table('system_settings')->insert([
            'key' => 'inactive_user_nudge_enabled',
            'value' => '1',
            'type' => 'boolean',
            'description' => 'Send AI-written reminder emails to students and lecturers idle for 14 days (Mondays 09:00)',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('inactive_user_nudges');

        DB::table('system_settings')
            ->where('key', 'inactive_user_nudge_enabled')
            ->delete();
    }
};
