<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'id' => 1,
            'name' => 'Diamond',
            'monthly_word_limit' => null, // null = unlimited words
            'templates' => '6',
        ]);
    }
}