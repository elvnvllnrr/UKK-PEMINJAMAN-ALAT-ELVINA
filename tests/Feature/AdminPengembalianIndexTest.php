<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Kelas pengujian tampilan serta pembatasan aksi pada area administrator. */
class AdminPengembalianIndexTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan administrator dapat memantau antrean pengembalian tanpa memprosesnya. */
    public function test_admin_can_monitor_return_queue_but_cannot_process_it(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $admin = User::factory()->create(['role' => 'admin']);
        $peminjam = User::factory()->create(['role' => 'peminjam']);
        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tanggal_pinjam' => today()->subDay()->toDateString(),
            'tanggal_kembali_rencana' => today()->addDay()->toDateString(),
            'pengembalian_diajukan_at' => now(),
            'status' => 'Dipinjam',
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($admin)->get(route('admin.pengembalian.index'));

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk()
            ->assertSee('Menunggu pemeriksaan')
            ->assertSee('Ditangani petugas')
            ->assertDontSee('/admin/pengembalian/create/'.$peminjaman->id);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $this->get('/admin/pengembalian/create/'.$peminjaman->id)
            ->assertNotFound();

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $this->post('/admin/pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'tanggal_kembali' => today()->toDateString(),
        ])->assertStatus(405);

        $this->assertDatabaseMissing('pengembalian', ['peminjaman_id' => $peminjaman->id]);
    }

    public function test_petugas_return_history_orders_recently_updated_records_first(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $peminjam = User::factory()->create(['role' => 'peminjam']);
        $lama = $this->buatPengembalian($peminjam);
        $baru = $this->buatPengembalian($peminjam);

        DB::table('pengembalian')->where('id', $lama->id)->update([
            'updated_at' => now()->subDay(),
        ]);
        DB::table('pengembalian')->where('id', $baru->id)->update([
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($petugas)->get(route('petugas.pengembalian.index'));

        $response->assertOk();
        $this->assertSame(
            [$baru->id, $lama->id],
            $response->viewData('pengembalian')->getCollection()->pluck('id')->all()
        );
    }

    private function buatPengembalian(User $peminjam): Pengembalian
    {
        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tanggal_pinjam' => today()->toDateString(),
            'tanggal_kembali_rencana' => today()->addDay()->toDateString(),
            'status' => 'Dikembalikan',
        ]);

        return Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tanggal_kembali' => today()->toDateString(),
            'status' => 'Tepat Waktu',
            'denda' => 0,
            'denda_kerusakan' => 0,
        ]);
    }
}
