<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Fixed users
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'plan_id' => 1,
            'current_word_usage' => null,
            'words_used' => 0,
            'status' => '1',
        ]);

        User::create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@example.com',
            'password' => Hash::make('password'),
            'role' => 'lecturer',
            'plan_id' => 1,
            'current_word_usage' => 0,
            'words_used' => 0,
            'status' => '1',
        ]);

        // SET NUMBER OF STUDENTS HERE
        $totalStudents = 20; 

        for ($i = 1; $i <= $totalStudents; $i++) {
            User::create([
                'name' => 'Student '.$i,
                'email' => 'student'.$i.'@example.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'plan_id' => 1,
                'current_word_usage' => 0,
                'words_used' => 0,
                'status' => '1',
            ]);
        }
    }
}
