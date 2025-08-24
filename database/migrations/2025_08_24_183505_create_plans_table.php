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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('monthly_word_limit'); // Column for the plan's monthly word limit.
            // Creates a decimal column for the plan's price, with a total of 8 digits and 2 decimal places.
            // It is also set to be nullable, meaning a price is not always required.
            $table->decimal('price', 8, 2)->nullable();
            $table->string('templates')->nullable(); // Column for plan templates, allowing it to be nullable.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
