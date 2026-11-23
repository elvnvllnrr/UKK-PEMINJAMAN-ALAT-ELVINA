<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian fitur katalog dan peminjaman milik peminjam. */
class AdminPeminjamanMonitoringTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan administrator dapat memantau peminjaman tanpa kontrol perubahan data. */
    public function test_admin_can_monitor_borrowing_without_mutation_controls(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $admin = User::factory()->create(['role' => 'admin']);
        $peminjam = User::factory()->create(['role' => 'peminjam']);
        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tanggal_pinjam' => today()->subDays(3)->toDateString(),
            'tanggal_kembali_rencana' => today()->subDay()->toDateString(),
            'status' => 'Dipinjam',
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($admin)->get(route('admin.peminjaman.index'));

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk()
            ->assertSee('Pemantauan peminjaman')
            ->assertSee('Status keterlambatan diperbarui otomatis')
            ->assertSee('Terlambat')
            ->assertSee(route('admin.peminjaman.show', $peminjaman), false)
            ->assertDontSee('Tambah Peminjaman')
            ->assertDontSee('Setujui')
            ->assertDontSee('Proses Pengembalian')
            ->assertDontSee('fa-edit')
            ->assertDontSee('fa-trash');

        $this->assertSame('Terlambat', $peminjaman->fresh()->status);
    }

    /** Memastikan administrator tidak dapat membuat atau mengubah peminjaman. */
    public function test_admin_cannot_create_or_mutate_a_borrowing(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $admin = User::factory()->create(['role' => 'admin']);
        $peminjam = User::factory()->create(['role' => 'peminjam']);
        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tanggal_pinjam' => today()->toDateString(),
            'tanggal_kembali_rencana' => today()->addDays(2)->toDateString(),
            'status' => 'Dipinjam',
        ]);

        $this->actingAs($admin)
            ->get('/admin/peminjaman/create')
            ->assertNotFound();

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $this->post('/admin/peminjaman', [
            'user_id' => $peminjam->id,
            'tanggal_pinjam' => today()->toDateString(),
            'tanggal_kembali_rencana' => today()->addDay()->toDateString(),
        ])->assertStatus(405);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $this->put('/admin/peminjaman/'.$peminjaman->id, [
            'tanggal_kembali_rencana' => today()->addDays(3)->toDateString(),
        ])->assertStatus(405);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $this->delete('/admin/peminjaman/'.$peminjaman->id)
            ->assertStatus(405);

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $this->assertSame(today()->addDays(2)->toDateString(), $peminjaman->fresh()->tanggal_kembali_rencana);
        $this->assertSame('Dipinjam', $peminjaman->fresh()->status);
    }

    /** Memastikan peminjam dapat mengajukan permintaan meski catatan opsional kosong. */
    public function test_peminjam_can_submit_request_without_optional_note(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $peminjam = User::factory()->create(['role' => 'peminjam']);
        $kategori = \App\Models\Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = \App\Models\Alat::create([
            'nama_alat' => 'Laptop',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-001',
            'stok' => 3,
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($peminjam)->post(route('peminjam.peminjaman.store'), [
            'tanggal_pinjam' => today()->toDateString(),
            'tanggal_kembali_rencana' => today()->addDay()->toDateString(),
            'alat' => [
                $alat->id => 1,
            ],
        ]);

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertRedirect(route('peminjam.peminjaman'));
        $this->assertDatabaseHas('peminjaman', [
            'user_id' => $peminjam->id,
            'status' => 'Menunggu Persetujuan',
            'catatan' => null,
        ]);
        $this->assertDatabaseHas('detail_peminjaman', [
            'alat_id' => $alat->id,
            'jumlah' => 1,
        ]);
        $this->assertDatabaseHas('alat', [
            'id' => $alat->id,
            'stok' => 3,
        ]);
    }
}
