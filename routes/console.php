<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Daily automatic backup. Time/type configurable via .env:
// BACKUP_SCHEDULE_DAILY_AT=02:00, BACKUP_SCHEDULE_TYPE=database
Schedule::command('backup:run --type='.env('BACKUP_SCHEDULE_TYPE', 'database'))
    ->dailyAt((string) env('BACKUP_SCHEDULE_DAILY_AT', '02:00'))
    ->name('daily-backup')
    ->withoutOverlapping()
    ->onFailure(function () {
        logger()->error('Scheduled daily backup failed.');
    });

// Weekly retention cleanup as an extra safety net
// (backup:run already cleans after each run).
Schedule::command('backup:clean')
    ->weekly()
    ->name('weekly-backup-cleanup')
    ->withoutOverlapping();
