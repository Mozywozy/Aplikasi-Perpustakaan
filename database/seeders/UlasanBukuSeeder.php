<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UlasanBukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('ulasanbuku')->insert([
            [
                'buku_id' => 1,
                'user_id' => 1,
                'ulasan' => 'Buku yang sangat menarik!',
                'rating' => 5,
            ],
            [
                'buku_id' => 2,
                'user_id' => 2,
                'ulasan' => 'Ceritanya kurang memuaskan.',
                'rating' => 3,
            ],
        ]);
    }
}
