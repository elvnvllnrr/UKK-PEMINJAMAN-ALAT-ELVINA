<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian fitur katalog dan peminjaman milik peminjam. */
class PeminjamKatalogTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan katalog peminjam menampilkan alat yang tersedia. */
    public function test_peminjam_katalog_renders_available_tools(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $peminjam = User::factory()->create(['role' => 'peminjam']);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($peminjam)->get(route('peminjam.katalog'));

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk()
            ->assertSee('Ajukan peminjaman')
            ->assertSee('Belum ada alat tersedia');
    }

    /** Memastikan satu formulir digunakan untuk banyak alat dan satu jadwal bersama. */
    public function test_catalog_uses_one_form_for_multiple_tools_and_one_shared_schedule(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $peminjam = User::factory()->create(['role' => 'peminjam']);
        $kategori = Kategori::create(['nama_kategori' => 'Peralatan']);
        $laptop = Alat::create([
            'nama_alat' => 'Laptop',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-001',
            'stok' => 3,
        ]);
        $kamera = Alat::create([
            'nama_alat' => 'Kamera',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-002',
            'stok' => 2,
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($peminjam)->get(route('peminjam.katalog'));

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk()
            ->assertSee('name="alat['.$laptop->id.']"', false)
            ->assertSee('name="alat['.$kamera->id.']"', false)
            ->assertSee('name="tanggal_pinjam"', false)
            ->assertSee('name="tanggal_kembali_rencana"', false)
            ->assertSee('min="'.today()->toDateString().'"', false)
            ->assertSee('Kirim Permohonan');
    }

    public function test_peminjam_can_search_catalog_by_name_category_or_code(): void
    {
        $peminjam = User::factory()->create(['role' => 'peminjam']);
        $kategori = Kategori::create(['nama_kategori' => 'Elektronik']);
        Alat::create([
            'nama_alat' => 'Laptop ASUS',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-LPT-001',
            'stok' => 3,
        ]);
        Alat::create([
            'nama_alat' => 'Kamera Canon',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'ALT-KMR-002',
            'stok' => 2,
        ]);

        $this->actingAs($peminjam)
            ->get(route('peminjam.katalog', ['search' => 'laptop']))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertDontSee('Kamera Canon');

        $this->get(route('peminjam.katalog', ['search' => 'elektronik']))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertSee('Kamera Canon');

        $this->get(route('peminjam.katalog', ['search' => 'kmr-002']))
            ->assertOk()
            ->assertSee('Kamera Canon')
            ->assertDontSee('Laptop ASUS');

        $this->get(route('peminjam.katalog', ['search' => 'tidak-ada']))
            ->assertOk()
            ->assertSee('Data alat tidak ditemukan');

        $this->get(route('peminjam.katalog', ['search' => '']))
            ->assertOk()
            ->assertSee('Laptop ASUS')
            ->assertSee('Kamera Canon');
    }
}
