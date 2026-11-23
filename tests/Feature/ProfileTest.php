<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian tampilan profil serta perubahan data dan penghapusan akun. */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan pengguna dapat melihat halaman profil. */
    public function test_profile_page_is_displayed(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk();
    }

    /** Memastikan perubahan nama atau surel profil dapat disimpan. */
    public function test_profile_information_can_be_updated(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    /** Memastikan verifikasi surel tetap aktif saat alamat tidak berubah. */
    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    /** Memastikan pengguna dapat menghapus akun miliknya. */
    public function test_user_can_delete_their_account(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    /** Memastikan kata sandi yang benar diwajibkan sebelum penghapusan akun. */
    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $this->assertNotNull($user->fresh());
    }
}
