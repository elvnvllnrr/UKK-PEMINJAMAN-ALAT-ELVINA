<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian pembukaan halaman dan proses registrasi pengguna. */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan halaman registrasi dapat ditampilkan. */
    public function test_registration_screen_can_be_rendered(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->get('/register');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertStatus(200)
            ->assertSee('action="/register"', false)
            ->assertSee('href="/login"', false);
    }

    /** Memastikan tautan registrasi pada halaman publik tetap memakai host yang sedang diakses. */
    public function test_landing_and_login_registration_links_use_relative_urls(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="/register"', false);

        $this->get('/login')
            ->assertOk()
            ->assertSee('href="/register"', false);
    }

    /** Memastikan pengguna baru dapat membuat akun melalui registrasi. */
    public function test_new_users_can_register(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->post('/register', [
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        // Memverifikasi respons, role akun, dan tujuan dashboard peminjam.
        $this->assertAuthenticated();
        $response->assertRedirect(route('peminjam.dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'role' => 'peminjam',
        ]);
        $registeredUser = User::where('username', 'testuser')->firstOrFail();
        $this->assertSame('peminjam', $registeredUser->role);
        $this->assertNotContains($registeredUser->role, ['admin', 'petugas']);
    }

    /** Memastikan username dan email registrasi tidak boleh duplikat. */
    public function test_registration_requires_unique_username_and_email(): void
    {
        User::factory()->create([
            'username' => 'existing',
            'email' => 'existing@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Other User',
            'username' => 'existing',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'petugas',
        ]);

        $response->assertSessionHasErrors(['username', 'email']);
        $this->assertGuest();
    }
}
