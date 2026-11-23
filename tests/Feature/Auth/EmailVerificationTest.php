<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/** Kelas pengujian status dan proses verifikasi alamat surel. */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan halaman pemberitahuan verifikasi surel dapat ditampilkan. */
    public function test_email_verification_screen_can_be_rendered(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->unverified()->create();

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->get('/verify-email');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertStatus(200);
    }

    /** Memastikan tautan verifikasi sah mengubah surel menjadi terverifikasi. */
    public function test_email_can_be_verified(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('dashboard', absolute: false).'?verified=1');
    }

    /** Memastikan hash tidak sah tidak memverifikasi surel. */
    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $this->actingAs($user)->get($verificationUrl);

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
