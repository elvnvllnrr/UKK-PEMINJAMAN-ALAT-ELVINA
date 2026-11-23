<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Kategori;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $response->assertOk();
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
}
