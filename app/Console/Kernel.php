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
        // Cek gabah kadaluarsa setiap hari jam 2 pagi
        $schedule->command('check:expired-grain')
                 ->dailyAt('02:00')
                 ->description('Cek gabah yang sudah kadaluarsa dan trigger notifikasi');
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
