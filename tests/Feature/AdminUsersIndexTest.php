<?php

namespace Tests\Feature;

use App\Models\Alat;
use App\Models\DetailPeminjaman;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Kelas pengujian tampilan serta pembatasan aksi pada area administrator. */
class AdminUsersIndexTest extends TestCase
{
    use RefreshDatabase;

    /** Memastikan halaman indeks pengguna dapat dibuka administrator. */
    public function test_admin_users_index_is_accessible_for_admin(): void
    {
        // Menyiapkan pengguna, data, atau keadaan awal kasus pengujian.
        $user = User::factory()->create([
            'username' => 'adminuser2',
            'role' => 'admin',
        ]);

        // Mengirim permintaan ke rute yang menjadi sasaran pengujian.
        $response = $this->actingAs($user)->get('/admin/users');

        // Memverifikasi respons atau keadaan yang menjadi ketentuan kasus ini.
        $response->assertOk()
            ->assertSee('Akun admin tidak dapat dihapus');
    }

    /** Memastikan administrator dapat membuat, mengubah, dan menghapus pengguna biasa. */
    public function test_admin_can_create_update_and_delete_a_non_admin_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'Pengguna Tes',
                'email' => 'pengguna@example.com',
                'username' => 'penggunates',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'role' => 'peminjam',
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::where('username', 'penggunates')->firstOrFail();

        $this->put(route('admin.users.update', $user), [
            'name' => 'Pengguna Diperbarui',
            'email' => 'pengguna@example.com',
            'username' => 'penggunates',
            'role' => 'petugas',
        ])->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Pengguna Diperbarui',
            'role' => 'petugas',
        ]);

        $this->delete(route('admin.users.destroy', $user))
            ->assertRedirect(route('admin.users.index'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** Memastikan akun admin tidak dapat dihapus atau admin terakhir diturunkan rolenya. */
    public function test_admin_cannot_be_deleted_and_last_admin_cannot_be_demoted(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'username' => $admin->username,
            'role' => 'peminjam',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);

        $otherAdmin = User::factory()->create(['role' => 'admin']);
        $this->put(route('admin.users.update', $otherAdmin), [
            'name' => $otherAdmin->name,
            'email' => $otherAdmin->email,
            'username' => $otherAdmin->username,
            'role' => 'petugas',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id, 'role' => 'admin']);

        $this->delete(route('admin.users.destroy', $otherAdmin))
            ->assertSessionHas('error', 'Akun admin tidak dapat dihapus.');

        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id, 'role' => 'admin']);
    }

    public function test_default_borrower_role_cannot_be_changed_to_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $borrower = User::factory()->create([
            'username' => 'peminjam',
            'role' => 'peminjam',
        ]);

        $this->actingAs($admin)->put(route('admin.users.update', $borrower), [
            'name' => $borrower->name,
            'email' => $borrower->email,
            'username' => $borrower->username,
            'role' => 'admin',
        ])->assertSessionHasErrors('role');

        $this->assertDatabaseHas('users', [
            'id' => $borrower->id,
            'role' => 'peminjam',
        ]);
    }

    public function test_account_with_admin_username_cannot_be_deleted_even_if_role_is_corrupted(): void
    {
        $actor = User::factory()->create(['role' => 'admin']);
        $corruptedAdmin = User::factory()->create([
            'username' => 'admin',
            'role' => 'peminjam',
        ]);

        $this->actingAs($actor)
            ->delete(route('admin.users.destroy', $corruptedAdmin))
            ->assertSessionHas('error', 'Akun admin tidak dapat dihapus.');

        $this->assertDatabaseHas('users', [
            'id' => $corruptedAdmin->id,
            'username' => 'admin',
        ]);
    }

    public function test_non_admin_with_active_borrowing_history_can_be_deleted_and_stock_is_restored(): void
    {
        $actor = User::factory()->create(['role' => 'admin']);
        $borrower = User::factory()->create(['role' => 'peminjam']);
        $kategori = Kategori::create([
            'nama_kategori' => 'Elektronik',
            'deskripsi' => null,
        ]);
        $alat = Alat::create([
            'nama_alat' => 'Laptop',
            'kategori_id' => $kategori->id,
            'kode_alat' => 'LPT-01',
            'stok' => 0,
            'kondisi' => 'Baik',
        ]);
        $peminjaman = Peminjaman::create([
            'user_id' => $borrower->id,
            'tanggal_pinjam' => today()->toDateString(),
            'tanggal_kembali_rencana' => today()->addDay()->toDateString(),
            'status' => 'Dipinjam',
        ]);
        DetailPeminjaman::create([
            'peminjaman_id' => $peminjaman->id,
            'alat_id' => $alat->id,
            'jumlah' => 2,
            'kondisi_pinjam' => 'Baik',
        ]);

        $this->actingAs($actor)
            ->delete(route('admin.users.destroy', $borrower))
            ->assertRedirect(route('admin.users.index'))
            ->assertSessionHas('success', 'User berhasil dihapus!');

        $this->assertDatabaseMissing('users', ['id' => $borrower->id]);
        $this->assertDatabaseMissing('peminjaman', ['id' => $peminjaman->id]);
        $this->assertSame(2, $alat->fresh()->stok);
    }
}
