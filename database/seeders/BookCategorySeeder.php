<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('book_category')->insert([
            ['buku_id' => 1, 'kategori_id' => 1],
            ['buku_id' => 2, 'kategori_id' => 2],
        ]);
    }
}
