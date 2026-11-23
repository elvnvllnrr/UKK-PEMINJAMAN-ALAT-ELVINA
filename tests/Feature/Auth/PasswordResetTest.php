<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/** Kelas pengujian permintaan tautan dan pemulihan kata sandi. */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan halaman permintaan tautan pemulihan kata sandi ditampilkan. */
    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->get('/forgot-password');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertStatus(200);
    }

    /** Memastikan pengguna dapat meminta tautan pemulihan kata sandi. */
    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /** Memastikan halaman pengaturan ulang kata sandi ditampilkan. */
    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
            $response = $this->get('/reset-password/'.$notification->token);

            // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
            $response->assertStatus(200);

            return true;
        });
    }

    /** Memastikan kata sandi dapat diubah dengan token pemulihan yang sah. */
    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create();

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
