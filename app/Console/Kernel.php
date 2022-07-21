<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\SafieApiCommand;
use App\Console\Commands\S3Command;
use App\Console\Commands\AICommand;
use App\Console\Commands\ShelfSortedCommand;

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
    ];

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('safie:refresh_access_token')->daily();
        // $schedule->command('s3:video_get_save')->everyFiveMinutes();
        $schedule->command('s3:video_get_save')->everyMinute();
        $schedule->command('ai:check_request_download')->everyMinute();
        $schedule->command('s3:sorted_image_save')->everyMinute();
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
