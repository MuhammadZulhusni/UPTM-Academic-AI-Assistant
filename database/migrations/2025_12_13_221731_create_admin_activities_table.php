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

            // Primary key (unique ID for each activity log)
            $table->id();

            // Stores the admin (user) who performed the activity
            // Linked to users.id
            $table->foreignId('admin_id')
                ->constrained('users')
                ->onDelete('cascade');

            // Type of activity performed by admin
            // Used to categorize actions for filtering and reporting
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

            // Short readable description of the activity
            // Example: "Admin updated template A"
            $table->string('activity_description');

            // Type of entity affected by the activity
            // Example values: 'template', 'user', 'document'
            // Nullable because some actions like login/logout
            // do not involve a specific entity
            $table->string('entity_type')->nullable();

            // ID of the affected entity
            // Example: template_id, user_id, document_id
            // Nullable for actions without entities (login/logout)
            $table->unsignedBigInteger('entity_id')->nullable();

            // Stores additional information in JSON format
            // Example: old data, new data, changes made
            $table->json('metadata')->nullable();

            // IP address of the admin during the activity
            // Useful for security and audit tracking
            $table->ipAddress('ip_address')->nullable();

            // Browser and device information of the admin
            // Helps in activity tracking and security analysis
            $table->string('user_agent')->nullable();

            // Stores created_at and updated_at timestamps
            $table->timestamps();

            // Indexes to improve query performance
            // Faster filtering by admin and date
            $table->index(['admin_id', 'created_at']);

            // Faster filtering by activity type
            $table->index('activity_type');

            // Faster lookup for entity-based activities
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