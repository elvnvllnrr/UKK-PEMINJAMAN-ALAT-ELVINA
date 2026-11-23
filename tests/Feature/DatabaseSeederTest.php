<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian hasil seeding akun pengguna bawaan. */
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan seeder membuat akun bawaan beserta username dan peran. */
    public function test_default_users_are_seeded_with_username_and_role(): void
    {
        $this->artisan('db:seed')->assertOk();

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $this->assertDatabaseHas('users', [
            'username' => 'admin',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'petugas',
            'role' => 'petugas',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'peminjam',
            'role' => 'peminjam',
        ]);

        $this->assertTrue(User::where('username', 'admin')->exists());
    }
}