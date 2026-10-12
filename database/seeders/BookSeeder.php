<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        Book::create([
            'kode_buku' => 'BK001',
            'judul' => 'Belajar Laravel',
            'pengarang' => 'Budi',
            'stok' => 5
        ]);
    }
}