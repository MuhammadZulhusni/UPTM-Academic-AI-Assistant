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
        Schema::create('template_input_fields', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('template_id')->constrained('templates')->onDelete('cascade'); // Links this input field to a specific template. Deletes this field if the template is deleted.
            $table->string('title'); 
            $table->text('description')->nullable(); 
            $table->enum('type', ['text', 'textarea']); // The type of input field, either a short 'text' box or a longer 'textarea'.
            $table->json('options')->nullable(); // Stores options for certain field types, like a dropdown menu. Can be empty.
            $table->boolean('is_required')->default(true); // Determines if the user must fill out this field. It's required by default.
            // $table->integer('order')->default(0); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_input_fields');
    }
};