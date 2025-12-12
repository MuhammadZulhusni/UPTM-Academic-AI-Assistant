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
        Schema::create('templates', function (Blueprint $table) {
            $table->id(); 
            $table->string('title'); 
            $table->text('description'); 
            $table->string('category'); 
            $table->string('icon')->nullable(); // A small icon to represent the template. Can be empty.
            $table->text('prompt'); // The main text or question that the template is based on.
            $table->boolean('is_active')->default(1); // Determines if the template is currently available for use. It's active by default.
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Links the template to the user who created it. Deletes the template if the user is deleted.
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};