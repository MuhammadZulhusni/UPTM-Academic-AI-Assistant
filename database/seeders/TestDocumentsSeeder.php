<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeneratedContent;
use App\Models\User;
use App\Models\Template;
use Carbon\Carbon;

class TestDocumentsSeeder extends Seeder
{
    public function run(): void
    {
        // Get a user and template 
        $user = User::find(28);
        $template = Template::first();

        if (!$user || !$template) {
            $this->command->error('Please ensure you have at least one user and one template in the database first!');
            return;
        }

        $this->command->info('Creating test documents...');

        // Create documents with different ages
        $testData = [
            ['days_ago' => 5, 'count' => 3, 'label' => '5 days old (should NOT be deleted)'],
            ['days_ago' => 30, 'count' => 5, 'label' => '30 days old (should NOT be deleted)'],
            ['days_ago' => 60, 'count' => 4, 'label' => '60 days old (should NOT be deleted)'],
            ['days_ago' => 91, 'count' => 6, 'label' => '91 days old (WILL be deleted if retention is 90 days)'],
            ['days_ago' => 120, 'count' => 8, 'label' => '120 days old (WILL be deleted)'],
            ['days_ago' => 180, 'count' => 10, 'label' => '180 days old (WILL be deleted)'],
            ['days_ago' => 365, 'count' => 5, 'label' => '1 year old (WILL be deleted)'],
        ];

        foreach ($testData as $data) {
            $this->command->info("Creating {$data['count']} documents: {$data['label']}");
            
            for ($i = 1; $i <= $data['count']; $i++) {
                GeneratedContent::create([
                    'user_id' => $user->id,
                    'template_id' => $template->id,
                    'input' => json_encode(['test' => 'data']),
                    'output' => "Test document created {$data['days_ago']} days ago - #{$i}",
                    'word_count' => 10,
                    'created_at' => Carbon::now()->subDays($data['days_ago']),
                    'updated_at' => Carbon::now()->subDays($data['days_ago']),
                ]);
            }
        }

        $total = array_sum(array_column($testData, 'count'));
        $this->command->info("✅ Successfully created {$total} test documents!");
        $this->command->info("📊 Documents older than 90 days: " . (6 + 8 + 10 + 5) . " documents");
    }
}