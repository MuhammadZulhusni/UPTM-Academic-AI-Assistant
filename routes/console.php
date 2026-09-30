<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('activity:cleanup')
    ->daily()
    ->at('00:00')
    ->when(function () {
        return \App\Models\SystemSetting::isAutoCleanupEnabled();
    });

Schedule::command('documents:cleanup')
    ->daily()
    ->at('00:30')
    ->when(function () {
        return \App\Models\SystemSetting::isDocumentAutoCleanupEnabled();
    });

Schedule::command('ops:weekly-brief')
    ->weeklyOn(1, '08:00')
    ->when(function () {
        return \App\Models\SystemSetting::isAiOpsBriefEnabled();
    });

Schedule::command('users:inactive-nudge')
    ->weeklyOn(1, '09:00')
    ->when(function () {
        return \App\Models\SystemSetting::isInactiveUserNudgeEnabled();
    });
