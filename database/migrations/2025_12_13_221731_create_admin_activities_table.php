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
        Schema::create('admin_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->enum('activity_type', [
                'template_created',
                'template_updated',
                'template_deleted',
                'user_created',
                'user_updated',
                'user_deleted',
                'document_created',
                'document_updated',
                'document_deleted',
                'login',
                'logout'
            ]);
            $table->string('activity_description');
            $table->string('entity_type')->nullable(); // e.g., 'template', 'user', 'document'
            $table->unsignedBigInteger('entity_id')->nullable(); // ID of the affected entity
            $table->json('metadata')->nullable(); // Additional data (old values, new values, etc.)
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['admin_id', 'created_at']);
            $table->index('activity_type');
            $table->index(['entity_type', 'entity_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_activities');
    }
};