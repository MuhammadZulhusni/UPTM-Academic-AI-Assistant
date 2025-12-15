<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AdminActivity;
use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupOldActivityLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activity:cleanup {--days= : Number of days to keep logs (overrides system setting)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old admin activity logs based on retention settings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if auto cleanup is enabled
        if (!SystemSetting::isAutoCleanupEnabled() && !$this->option('days')) {
            $this->info('Auto cleanup is disabled. Enable it in system settings or use --days option.');
            return 0;
        }

        // Get retention days from option or system setting
        $retentionDays = $this->option('days') 
            ?? SystemSetting::getActivityLogRetentionDays();

        if ($retentionDays <= 0) {
            $this->error('Retention days must be greater than 0');
            return 1;
        }

        $this->info("Cleaning up activity logs older than {$retentionDays} days...");

        // Calculate cutoff date
        $cutoffDate = Carbon::now()->subDays($retentionDays);

        // Count logs to be deleted
        $count = AdminActivity::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('No old activity logs to clean up.');
            return 0;
        }

        // Confirm before deletion (skip in scheduled mode)
        if ($this->input->isInteractive()) {
            if (!$this->confirm("This will delete {$count} activity log(s). Continue?")) {
                $this->info('Cleanup cancelled.');
                return 0;
            }
        }

        // Delete old logs
        try {
            $deleted = AdminActivity::where('created_at', '<', $cutoffDate)->delete();

            $this->info("Successfully deleted {$deleted} old activity log(s).");
            
            // Log the cleanup action
            Log::info('Activity logs cleanup completed', [
                'retention_days' => $retentionDays,
                'deleted_count' => $deleted,
                'cutoff_date' => $cutoffDate->toDateTimeString(),
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error('Error during cleanup: ' . $e->getMessage());
            
            Log::error('Activity logs cleanup failed', [
                'error' => $e->getMessage(),
                'retention_days' => $retentionDays,
            ]);

            return 1;
        }
    }
}