<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kategori;
use App\Models\Alat;
use App\Models\DetailPeminjaman;
use App\Models\Peminjaman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Kelas pengujian tampilan serta pembatasan aksi pada area administrator. */
class AdminAlatIndexTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan halaman indeks alat dapat dibuka administrator. */
    public function test_admin_alat_index_is_accessible_for_admin(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create([
            'username' => 'adminuser4',
            'role' => 'admin',
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->get('/admin/alat');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk()
            ->assertSee('Tambah Alat')
            ->assertSee(route('admin.alat.create'), false);
    }

    public function test_petugas_cannot_add_alat_but_can_view_existing_alat(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        $alat = Alat::create([
            'nama_alat' => 'Laptop',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-STAFF-001',
            'stok' => 3,
        ]);

        $this->actingAs($petugas)
            ->get(route('petugas.alat.index'))
            ->assertOk()
            ->assertDontSee('Tambah Alat')
            ->assertSee('Laptop')
            ->assertSee(route('petugas.alat.index'), false);

        $this->get(route('petugas.alat.show', $alat))
            ->assertOk()
            ->assertSee('Laptop');

        $this->get(route('admin.alat.create'))->assertForbidden();

        $this->post(route('admin.alat.store'), [
            'nama_alat' => 'Alat Tidak Sah',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-UNAUTHORIZED',
            'stok' => 1,
            'kondisi' => 'Baik',
        ])->assertForbidden();

        $this->assertDatabaseMissing('alat', ['kode_alat' => 'ALT-UNAUTHORIZED']);
    }

    /** Memastikan administrator dapat menambah alat lalu dialihkan ke indeks alat. */
    public function test_admin_can_create_alat_and_is_redirected_to_index(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create([
            'username' => 'adminuser5',
            'role' => 'admin',
        ]);
        $kategori = Kategori::create([
            'nama_kategori' => 'Elektronik',
            'deskripsi' => 'Peralatan elektronik',
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->post('/admin/alat', [
            'nama_alat' => 'Laptop',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-TEST-001',
            'deskripsi' => 'Laptop untuk pengujian',
            'stok' => 6,
            'kondisi' => 'Baik',
        ]);

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertRedirect(route('admin.alat.index'));
        $this->assertDatabaseHas('alat', [
            'kode_alat' => 'ALT-TEST-001',
            'nama_alat' => 'Laptop',
        ]);
    }

    /** Memastikan rute media foto alat dapat melayani berkas dari disk public. */
    public function test_alat_foto_media_route_serves_stored_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('foto_alat/sample-alat.jpg', 'fake-image-content');

        $this->get(route('media.alat', ['filename' => 'sample-alat.jpg']))
            ->assertOk();
    }

    public function test_admin_can_search_alat_by_name_category_or_code_case_insensitively(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $kategori = Kategori::create([
            'nama_kategori' => 'Elektronik',
            'deskripsi' => null,
        ]);
        Alat::create([
            'nama_alat' => 'Laptop ASUS',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-LPT-001',
            'stok' => 2,
            'kondisi' => 'Baik',
        ]);
        Alat::create([
            'nama_alat' => 'Kamera Canon',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-KMR-002',
            'stok' => 1,
            'kondisi' => 'Baik',
        ]);

        $this->actingAs($admin)->get(route('admin.alat.index', ['search' => 'laptop']))
            ->assertOk()
            ->assertSee('value="laptop"', false)
            ->assertSee('Laptop ASUS')
            ->assertDontSee('Kamera Canon');

        $this->get(route('admin.alat.index', ['search' => 'elektronik']))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertSee('Kamera Canon');

        $this->get(route('admin.alat.index', ['search' => 'kmr-002']))
            ->assertOk()
            ->assertSee('Kamera Canon')
            ->assertDontSee('Laptop ASUS');

        $this->get(route('admin.alat.index', ['search' => 'tidak-ada']))
            ->assertOk()
            ->assertSee('Data alat tidak ditemukan');

        $this->get(route('admin.alat.index', ['search' => '']))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertSee('Kamera Canon');
    }

    public function test_admin_can_delete_alat_with_transaction_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $borrower = User::factory()->create(['role' => 'peminjam']);
        $kategori = Kategori::create([
            'nama_kategori' => 'Elektronik',
            'deskripsi' => null,
        ]);
        $alat = Alat::create([
            'nama_alat' => 'Laptop',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-HISTORY-001',
            'stok' => 3,
            'kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $borrower->id,
            'tanggal_pinjam' => today()->toDateString(),
            'tanggal_kembali_rencana' => today()->addDay()->toDateString(),
            'status' => 'Menunggu Persetujuan',
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 1,
            'kondisi_pinjam' => 'Baik',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.alat.destroy', $alat))
            ->assertRedirect(route('admin.alat.index'))
            ->assertSessionHas('success', 'Alat berhasil dihapus!');

        $this->assertDatabaseMissing('alat', ['id' => $alat->id]);
        $this->assertDatabaseMissing('detail_peminjaman', ['alat_id' => $alat->id]);
        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
    }
}
