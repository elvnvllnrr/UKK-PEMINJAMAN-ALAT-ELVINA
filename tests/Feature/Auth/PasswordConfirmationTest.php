<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian konfirmasi ulang kata sandi pengguna. */
class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan halaman konfirmasi kata sandi dapat ditampilkan. */
    public function test_confirm_password_screen_can_be_rendered(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->get('/confirm-password');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertStatus(200);
    }

    /** Memastikan kata sandi yang benar lolos konfirmasi. */
    public function test_password_can_be_confirmed(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'password',
        ]);

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    /** Memastikan kata sandi salah tidak lolos konfirmasi. */
    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'wrong-password',
        ]);

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertSessionHasErrors();
    }
}
