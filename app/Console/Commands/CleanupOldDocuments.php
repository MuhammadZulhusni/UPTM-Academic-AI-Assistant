<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GeneratedContent;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupOldDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:cleanup {--days= : Number of days to keep documents (overrides system setting)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old user-generated documents based on retention settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if auto cleanup is enabled
        if (!SystemSetting::isDocumentAutoCleanupEnabled() && !$this->option('days')) {
            $this->info('Auto cleanup is disabled. Enable it in system settings or use --days option.');
            return 0;
        }

        // Get retention days from option or system setting
        $retentionDays = $this->option('days') 
            ?? SystemSetting::getDocumentRetentionDays();

        if ($retentionDays <= 0) {
            $this->error('Retention days must be greater than 0');
            return 1;
        }

        $this->info("Cleaning up documents older than {$retentionDays} days...");

        // Calculate cutoff date
        $cutoffDate = Carbon::now()->subDays($retentionDays);

        // Count documents to be deleted
        $count = GeneratedContent::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('No old documents to clean up.');
            return 0;
        }

        // Confirm before deletion (skip in scheduled mode)
        if ($this->input->isInteractive()) {
            if (!$this->confirm("This will delete {$count} document(s). Continue?")) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }

        // Delete old documents
        try {
            $deleted = GeneratedContent::where('created_at', '<', $cutoffDate)->delete();

            $this->info("Successfully deleted {$deleted} old document(s).");
            
            // Log the cleanup action
            Log::info('Documents cleanup completed', [
                'retention_days' => $retentionDays,
                'deleted_count' => $deleted,
                'cutoff_date' => $cutoffDate->toDateTimeString(),
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Error during cleanup: ' . $e->getMessage());
            
            Log::error('Documents cleanup failed', [
                'error' => $e->getMessage(),
                'retention_days' => $retentionDays,
            ]);

            return 1;
        }
    }
}