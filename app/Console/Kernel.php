<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SafieApiCommand;
use App\Console\Commands\S3Command;
use App\Console\Commands\AICommand;
use App\Console\Commands\ShelfSortedCommand;
use App\Console\Commands\RetryRequestNight;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected $commands = [
        SafieApiCommand::class,
        S3Command::class,
        AICommand::class,
        ShelfSortedCommand::class,
        RetryRequestNight::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('safie:refresh_access_token')->daily();
        // $schedule->command('s3:video_get_save')->everyFiveMinutes();
        $schedule->command('s3:video_get_save')->everyMinute();
        $schedule->command('ai:retry_request_night')->everyMinute();
        $schedule->command('ai:check_request_download')->everyMinute();
        $schedule->command('s3:sorted_image_save')->everyMinute();
        $schedule->command('auto_open_camera')->everyFiveMinutes();
        $schedule->command('ai:get_heatmap_data')->everyFiveMinutes();
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
