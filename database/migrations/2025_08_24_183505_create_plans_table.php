<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Plan name (Diamond)
            $table->integer('monthly_word_limit'); // Monthly word usage limit
            $table->string('templates')->nullable(); // Number of available templates
            $table->timestamps();
        });

        // Insert Diamond plan for all users
        DB::table('plans')->insert([
            'id' => 1,
            'name' => 'Diamond',
            'monthly_word_limit' => 5000, // Word limit per month
            'templates' => '6', // Number of templates allowed
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
    
    // Personal note: Access Levels Are Determined By:
    // - monthly_word_limit = How many words user can generate
    // - templates = How many templates user can access
    // All users get plan_id = 1 (Diamond plan)

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};