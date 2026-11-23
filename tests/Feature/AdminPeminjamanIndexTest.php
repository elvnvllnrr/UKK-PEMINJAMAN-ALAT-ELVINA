<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian fitur katalog dan peminjaman milik peminjam. */
class AdminPeminjamanIndexTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan halaman pemantauan peminjaman dapat dibuka administrator. */
    public function test_admin_peminjaman_index_is_accessible_for_admin(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create([
            'username' => 'adminuser5',
            'role' => 'admin',
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->get('/admin/peminjaman');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk();
    }
}
