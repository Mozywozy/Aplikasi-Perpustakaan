<?php

namespace App\Console;

use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $peminjamanApproved = Peminjaman::where('status', 'approved')->get();
            $peminjamanApproved->each(function ($peminjaman) {
                if (Carbon::now()->greaterThanOrEqualTo(Carbon::parse($peminjaman->tanggal_pengembalian))) {
                    $peminjaman->status = 'Buku harus dikembalikan segera!';
                    $peminjaman->save();
                    Log::info('Peminjaman ' . $peminjaman->id . ' status updated to "Buku harus dikembalikan segera!"');
                }
            });
        })->daily(); // Menjalankan pemeriksaan setiap hari. Anda bisa menyesuaikan intervalnya.
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
