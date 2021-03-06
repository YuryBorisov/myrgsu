<?php

namespace App\Console;

use App\Console\Commands\EveningSchedule;
use App\Console\Commands\MorningSchedule;
use App\Console\Commands\Parse;
use App\Console\Commands\SendMessage;
use App\Models\UserVK;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        EveningSchedule::class,
        SendMessage::class,
        MorningSchedule::class,
        Parse::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('morning_schedule')->dailyAt('7:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
