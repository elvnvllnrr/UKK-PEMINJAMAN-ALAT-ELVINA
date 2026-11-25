<?php

namespace Tests\Feature;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PetugasCetakLaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_petugas_report_shows_latest_matching_transaction_and_filters_by_return_date(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $peminjamTerpilih = User::factory()->create(['name' => 'Peminjam Terpilih']);
        $peminjamLain = User::factory()->create(['name' => 'Peminjam Lain']);

        $this->buatPengembalian($peminjamTerpilih, '2026-11-20');
        $this->buatPengembalian($peminjamLain, '2026-11-21');

        $this->actingAs($petugas)
            ->get(route('petugas.laporan.pengembalian'))
            ->assertOk()
            ->assertSee('Peminjam Lain')
            ->assertDontSee('Peminjam Terpilih')
            ->assertSee('Tanggal cetak:')
            ->assertSee('Laporan Transaksi Peminjaman Alat');

        $this->get(route('petugas.laporan.pengembalian', [
            'status' => 'Dikembalikan',
            'tanggal_mulai' => '2026-11-20',
            'tanggal_selesai' => '2026-11-20',
        ]))
            ->assertOk()
            ->assertSee('Peminjam Terpilih')
            ->assertDontSee('Peminjam Lain')
            ->assertSee('Dikembalikan')
            ->assertSee('Nama petugas: '.$petugas->name);
    }

    public function test_report_orders_by_update_time_and_only_returns_latest_record_per_list(): void
    {
        $petugas = User::factory()->create(['role' => 'petugas']);
        $olderBorrower = User::factory()->create(['name' => 'Transaksi Lama']);
        $newerBorrower = User::factory()->create(['name' => 'Transaksi Terbaru']);
        $older = $this->buatPengembalian($olderBorrower, '2026-11-20');
        $newer = $this->buatPengembalian($newerBorrower, '2026-11-20');

        DB::table('peminjaman')->where('id', $older->id)->update([
            'updated_at' => now()->subDay(),
        ]);
        DB::table('pengembalian')->where('peminjaman_id', $older->id)->update([
            'updated_at' => now()->subDay(),
        ]);
        DB::table('peminjaman')->where('id', $newer->id)->update([
            'updated_at' => now(),
        ]);
        DB::table('pengembalian')->where('peminjaman_id', $newer->id)->update([
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($petugas)->get(route('petugas.laporan.pengembalian', [
            'status' => 'Dikembalikan',
            'tanggal_mulai' => '2026-11-20',
            'tanggal_selesai' => '2026-11-20',
        ]));

        $response->assertOk()
            ->assertSee('Transaksi Terbaru')
            ->assertDontSee('Transaksi Lama');
        $this->assertSame(
            [$newer->id],
            $response->viewData('peminjaman')->pluck('id')->all()
        );
        $this->assertSame(
            [$newer->id],
            $response->viewData('pengembalian')->pluck('peminjaman_id')->all()
        );
    }

    public function test_only_petugas_can_access_the_return_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('petugas.laporan.pengembalian'))
            ->assertForbidden();
    }

    private function buatPengembalian(User $peminjam, string $tanggal): Peminjaman
    {
        $peminjaman = Peminjaman::create([
            'user_id' => $peminjam->id,
            'tanggal_pinjam' => $tanggal,
            'tanggal_kembali_rencana' => $tanggal,
            'status' => 'Dikembalikan',
        ]);

        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'tanggal_kembali' => $tanggal,
            'status' => 'Tepat Waktu',
            'denda' => 0,
        ]);

        $this->assertDatabaseHas('pengembalian', [
            'peminjaman_id' => $peminjaman->id,
            'tanggal_dikembalikan' => $tanggal,
        ]);

        return $peminjaman;
    }
}
