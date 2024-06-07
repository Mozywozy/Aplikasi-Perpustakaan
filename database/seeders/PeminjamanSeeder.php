<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeminjamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('peminjaman')->insert([
            [
                'buku_id' => 1,
                'user_id' => 1,
                'tanggal_peminjaman' => Carbon::now(),
                'tanggal_pengembalian' => null,
                'kondisi_buku' => 'Normal',
                'status' => 'pending',
                'denda' => 0,
            ],
            [
                'buku_id' => 2,
                'user_id' => 2,
                'tanggal_peminjaman' => Carbon::now()->subDays(5),
                'tanggal_pengembalian' => null,
                'kondisi_buku' => 'Normal',
                'status' => 'pending',
                'denda' => 0,
            ],
        ]);
    }
}
