<?php

namespace App\Console\Commands;

use App\Services\InactiveUserNudgeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInactiveUserNudges extends Command
{
    protected $signature = 'users:inactive-nudge {--force : Run even if the automation setting is off}';

    protected $description = 'Email students and lecturers who have been idle longer than the SuperAdmin setting';

    public function handle(InactiveUserNudgeService $service): int
    {
        $force = (bool) $this->option('force');

        if (!$force && !InactiveUserNudgeService::isEnabled()) {
            $this->info('Inactive user reminders are disabled in system settings.');

            return self::SUCCESS;
        }

        $this->info('Finding idle students and lecturers, writing reminder copy, and sending email...');

        try {
            $result = $service->run($force ? 'manual' : 'scheduled');
        } catch (\Throwable $e) {
            $this->error('Inactive user nudge failed: '.$e->getMessage());
            Log::error('Inactive user nudge command failed', ['error' => $e->getMessage()]);

            return self::FAILURE;
        }

        $this->info("Eligible: {$result['eligible']}. Sent: {$result['sent']}. Failed: {$result['failed']}.");

        return self::SUCCESS;
    }
}
