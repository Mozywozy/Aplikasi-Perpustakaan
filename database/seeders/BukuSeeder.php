<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BukuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('buku')->insert([
            [
                'judul' => 'Buku Satu',
                'penerbit' => 'Penerbit A',
                'status' => 'In Stock',
                'stock' => 10,
                'cover' => null,
            ],
            [
                'judul' => 'Buku Dua',
                'penerbit' => 'Penerbit B',
                'status' => 'Out Stock',
                'stock' => 0,
                'cover' => null,
            ],
        ]);
    }
}
