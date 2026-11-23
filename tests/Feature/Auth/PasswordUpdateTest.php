<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/** Kelas pengujian pembaruan kata sandi beserta validasi kata sandi lama. */
class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan pengguna dapat menyimpan kata sandi baru. */
    public function test_password_can_be_updated(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    /** Memastikan kata sandi lama yang benar diperlukan untuk pembaruan. */
    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/profile');
    }
}
