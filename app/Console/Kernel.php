<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Clean up old activity logs daily at midnight
        // Only runs if auto-cleanup is enabled in system settings
        $schedule->command('activity:cleanup')
            ->daily()
            ->at('00:00')
            ->when(function () {
                return \App\Models\SystemSetting::isAutoCleanupEnabled();
            })
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Activity logs cleanup completed successfully');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Activity logs cleanup failed');
            });

        // Clean up old documents daily at 00:30
        // Only runs if auto-cleanup is enabled in system settings
        $schedule->command('documents:cleanup')
            ->daily()
            ->at('00:30')
            ->when(function () {
                return \App\Models\SystemSetting::isDocumentAutoCleanupEnabled();
            })
            ->onSuccess(function () {
                \Illuminate\Support\Facades\Log::info('Documents cleanup completed successfully');
            })
            ->onFailure(function () {
                \Illuminate\Support\Facades\Log::error('Documents cleanup failed');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

// namespace App\Console;

// use Illuminate\Console\Scheduling\Schedule;
// use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

// class Kernel extends ConsoleKernel
// {
//     /**
//      * Define the application's command schedule.
//      */
//     protected function schedule(Schedule $schedule): void
//     {
//         // Clean up old activity logs daily at midnight
//         // Only runs if auto-cleanup is enabled in system settings
//         $schedule->command('activity:cleanup')
//             ->daily()
//             ->at('00:00')
//             ->when(function () {
//                 return \App\Models\SystemSetting::isAutoCleanupEnabled();
//             })
//             ->onSuccess(function () {
//                 \Illuminate\Support\Facades\Log::info('Activity logs cleanup completed successfully');
//             })
//             ->onFailure(function () {
//                 \Illuminate\Support\Facades\Log::error('Activity logs cleanup failed');
//             });
//     }

//     /**
//      * Register the commands for the application.
//      */
//     protected function commands(): void
//     {
//         $this->load(__DIR__.'/Commands');

//         require base_path('routes/console.php');
//     }
// }

