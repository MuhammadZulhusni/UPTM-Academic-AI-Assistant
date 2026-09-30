<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            if (!Schema::hasColumn('templates', 'allow_brief_upload')) {
                $table->boolean('allow_brief_upload')->default(false)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            if (Schema::hasColumn('templates', 'allow_brief_upload')) {
                $table->dropColumn('allow_brief_upload');
            }
        });
    }
};
