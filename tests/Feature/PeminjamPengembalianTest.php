<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian fitur katalog dan peminjaman milik peminjam. */
class PeminjamPengembalianTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan halaman pengembalian lama peminjam sudah tidak tersedia. */
    public function test_peminjam_pengembalian_page_is_removed(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $peminjam = User::factory()->create(['role' => 'peminjam']);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($peminjam)->get('/peminjam/pengembalian');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertNotFound();
    }
}