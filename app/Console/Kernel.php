<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
date_default_timezone_set('Asia/Jakarta');
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // $current_time   = date('H:i');

        $schedule->command('app:get-data-absen')->everyFiveSeconds();
    }

    protected function commands()
    {
        // Daftarkan semua command di siniq
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}