<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Storage;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Daily Backups. (Runs clean/prune first to free up space)
        $schedule->command('backup:clean')->daily()->at('03:40');
        $schedule->command('backup:run')->daily()->at('03:50');

        // Spatie Activity Log pruning - set number of days in config/activitylog.php file
        $schedule->command('activitylog:clean')->daily();

        // Prayer Wheel
        $schedule->call(function () {
            \App\Jobs\SendPrayerWheelAcknowledgements::dispatch();
        })->everyTenMinutes();

        $schedule->call(function () {
            \App\Jobs\SendPrayerWheelReminderEmails::dispatch();
        })->dailyAt('16:00:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
