<?php

namespace App\Console\Commands;

use App\Services\AiOpsBriefService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateWeeklyAiOpsBrief extends Command
{
    protected $signature = 'ops:weekly-brief {--force : Generate even if weekly automation is off or a brief already exists}';

    protected $description = 'Generate an AI operations brief from the last 7 days of system usage';

    public function handle(AiOpsBriefService $service): int
    {
        $force = (bool) $this->option('force');

        if (!$force && !AiOpsBriefService::isEnabled()) {
            $this->info('Weekly AI ops brief is disabled in system settings.');
            return self::SUCCESS;
        }

        if (!$force && $service->hasRecentBrief()) {
            $this->info('A brief was already generated in the last 6 days. Use --force to generate again.');
            return self::SUCCESS;
        }

        $this->info('Collecting last 7 days of usage and asking OpenAI to write the brief...');

        try {
            $brief = $service->generate($force ? 'manual' : 'scheduled');
        } catch (\Throwable $e) {
            $this->error('Failed to generate brief: '.$e->getMessage());
            Log::error('Weekly AI ops brief command failed', ['error' => $e->getMessage()]);
            return self::FAILURE;
        }

        if ($brief->status === 'success') {
            $this->info("AI brief #{$brief->id} saved ({$brief->period_start->toDateString()} to {$brief->period_end->toDateString()}).");
        } elseif ($brief->status === 'fallback') {
            $this->warn("Brief #{$brief->id} saved with numbers-only summary because OpenAI failed.");
            $this->line($brief->error_message);
        } else {
            $this->error("Brief #{$brief->id} failed.");
            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
