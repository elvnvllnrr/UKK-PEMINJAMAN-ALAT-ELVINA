<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian ringkasan tugas yang ditampilkan pada dasbor. */
class PetugasDashboardTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan dasbor petugas menampilkan statistik dan daftar tugas. */
    public function test_petugas_dashboard_renders_its_statistics_and_lists(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $petugas = User::factory()->create(['role' => 'petugas']);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($petugas)->get(route('petugas.dashboard'));

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk()
            ->assertSee('Permohonan menunggu')
            ->assertSee('Pengembalian menunggu')
            ->assertSee('Peminjaman terlambat')
            ->assertSee('Permohonan baru')
            ->assertSee('Barang untuk diperiksa');
    }
}
