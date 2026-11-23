<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian ringkasan tugas yang ditampilkan pada dasbor. */
class DashboardSummaryTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan dasbor administrator menampilkan pekerjaan yang perlu ditindaklanjuti. */
    public function test_admin_dashboard_shows_work_that_needs_attention(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan sistem')
            ->assertSee('Menunggu persetujuan petugas')
            ->assertSee('Pengembalian menunggu petugas')
            ->assertSee('Tidak ada permohonan yang menunggu.')
            ->assertSee('Tidak ada pengembalian yang menunggu pemeriksaan.');
    }

    /** Memastikan dasbor petugas memfokuskan persetujuan dan pengembalian. */
    public function test_staff_dashboard_focuses_on_approval_and_return_tasks(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $petugas = User::factory()->create(['role' => 'petugas']);

        $this->actingAs($petugas)
            ->get(route('petugas.dashboard'))
            ->assertOk()
            ->assertSee('Tugas hari ini')
            ->assertSee('Permohonan baru')
            ->assertSee('Barang untuk diperiksa')
            ->assertDontSee('Peminjaman Terbaru')
            ->assertDontSee('Quick Actions');
    }

    /** Memastikan dasbor peminjam menampilkan langkah berikutnya. */
    public function test_borrower_dashboard_shows_a_clear_next_step(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $peminjam = User::factory()->create(['role' => 'peminjam']);

        $this->actingAs($peminjam)
            ->get(route('peminjam.dashboard'))
            ->assertOk()
            ->assertSee('Lihat status alat dan langkah yang perlu Anda lakukan.')
            ->assertSee('Ajukan Peminjaman')
            ->assertSee('Belum ada peminjaman')
            ->assertSee('Buka katalog alat');
    }
}
