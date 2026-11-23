<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/** Seeder utama yang menjalankan seluruh data awal aplikasi. */
class DatabaseSeeder extends Seeder
{
    /** Menjalankan UserSeeder sebagai satu-satunya langkah pembentukan data awal. */
    public function run(): void
    {
        // Memanggil seeder akun agar peran bawaan tersedia saat aplikasi disiapkan.
        $this->call([
            UserSeeder::class,
        ]);
    }
}