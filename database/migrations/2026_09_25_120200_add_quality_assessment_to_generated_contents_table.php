<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generated_contents', function (Blueprint $table) {
            $table->json('quality_assessment')->nullable()->after('word_count');
        });
    }

    public function down(): void
    {
        Schema::table('generated_contents', function (Blueprint $table) {
            $table->dropColumn('quality_assessment');
        });
    }
};
