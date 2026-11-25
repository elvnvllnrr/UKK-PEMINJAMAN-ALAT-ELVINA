<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowingDateValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrow_start_date_cannot_be_in_the_past(): void
    {
        $borrower = User::factory()->create(['role' => 'peminjam']);

        $this->actingAs($borrower)
            ->post(route('peminjam.peminjaman.store'), [
                'tanggal_pinjam' => today()->subDay()->toDateString(),
                'tanggal_kembali_rencana' => today()->addDay()->toDateString(),
                'alat' => [1 => 1],
            ])
            ->assertSessionHasErrors('tanggal_pinjam');
    }

    public function test_return_date_cannot_be_in_the_past(): void
    {
        $borrower = User::factory()->create(['role' => 'peminjam']);

        $this->actingAs($borrower)
            ->post(route('peminjam.peminjaman.store'), [
                'tanggal_pinjam' => today()->toDateString(),
                'tanggal_kembali_rencana' => today()->subDay()->toDateString(),
                'alat' => [1 => 1],
            ])
            ->assertSessionHasErrors('tanggal_kembali_rencana');
    }

    public function test_recorded_return_date_cannot_be_in_the_past(): void
    {
        $staff = User::factory()->create(['role' => 'petugas']);

        $this->actingAs($staff)
            ->post(route('petugas.pengembalian.store'), [
                'peminjaman_id' => 999999,
                'tanggal_kembali' => today()->subDay()->toDateString(),
                'detail' => [],
            ])
            ->assertSessionHasErrors('tanggal_kembali');
    }
}
