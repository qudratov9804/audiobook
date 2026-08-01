<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('queue:prune-batches', ['--hours=48'])->daily();
Schedule::command('queue:prune-failed-jobs', ['--hours=720'])->weekly();
Schedule::command('app:backup')->dailyAt('02:00');
