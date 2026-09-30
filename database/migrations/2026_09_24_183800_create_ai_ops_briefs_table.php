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
        Schema::create('ai_ops_briefs', function (Blueprint $table) {
            $table->id();
            $table->date('period_start');
            $table->date('period_end');
            $table->json('metrics');
            $table->longText('summary')->nullable();
            $table->string('status')->default('success'); // success, fallback, failed
            $table->string('trigger')->default('scheduled'); // scheduled, manual
            $table->string('model')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        DB::table('system_settings')->insert([
            'key' => 'ai_ops_brief_enabled',
            'value' => '1',
            'type' => 'boolean',
            'description' => 'Enable automatic weekly AI operations brief every Monday at 08:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_ops_briefs');

        DB::table('system_settings')
            ->where('key', 'ai_ops_brief_enabled')
            ->delete();
    }
};
