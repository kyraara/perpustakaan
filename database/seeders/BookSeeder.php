<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Book::create([
            'title' => 'Laskar Pelangi',
            'author' => 'Andrea Hirata',
            'publisher' => 'Bentang Pustaka',
            'published_year' => 2005,
            'isbn' => '979-3062-79-7',
            'stock' => 5,
        ]);

        Book::create([
            'title' => 'Bumi Manusia',
            'author' => 'Pramoedya Ananta Toer',
            'publisher' => 'Hasta Mitra',
            'published_year' => 1980,
            'isbn' => '979-97312-3-2',
            'stock' => 3,
        ]);

        Book::create([
            'title' => 'Cantik Itu Luka',
            'author' => 'Eka Kurniawan',
            'publisher' => 'Gramedia Pustaka Utama',
            'published_year' => 2002,
            'isbn' => '978-602-03-1258-3',
            'stock' => 4,
        ]);
    }
}
