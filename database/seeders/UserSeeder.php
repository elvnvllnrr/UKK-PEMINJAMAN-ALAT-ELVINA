<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/** Seeder untuk membuat akun bawaan beserta peran dan kredensialnya. */
class UserSeeder extends Seeder
{
    /** Membuat atau memperbarui akun admin, petugas, dan peminjam beserta perannya. */
    public function run(): void
    {
        // Menyimpan data akun default beserta kredensial dan peran yang dituju.
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Menyimpan data akun default beserta kredensial dan peran yang dituju.
        User::updateOrCreate(
            ['username' => 'petugas'],
            [
                'name' => 'Petugas',
                'email' => 'petugas@example.com',
                'password' => Hash::make('password'),
                'role' => 'petugas',
            ]
        );

        // Menyimpan data akun default beserta kredensial dan peran yang dituju.
        User::updateOrCreate(
            ['username' => 'peminjam'],
            [
                'name' => 'Peminjam',
                'email' => 'peminjam@example.com',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
            ]
        );
    }
}