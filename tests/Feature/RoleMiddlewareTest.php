<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian pembatasan halaman berdasarkan peran pengguna. */
class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan administrator dapat membuka dasbor administrasi. */
    public function test_admin_dashboard_accessible_for_admin(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create([
            'username' => 'adminuser',
            'role' => 'admin',
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->get('/admin/dashboard');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk();
    }

    /** Memastikan petugas dialihkan dari halaman admin ke dasbornya. */
    public function test_petugas_is_redirected_to_own_dashboard_from_admin_page(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create([
            'username' => 'petugasuser',
            'role' => 'petugas',
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->get('/admin/peminjaman');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertRedirect('/petugas/dashboard');
        $response->assertSessionHas('error');
    }
}
