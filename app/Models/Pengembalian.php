<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Menyimpan tanggal, status, denda, dan catatan pengembalian. */
class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';

    protected $fillable = [
        'peminjaman_id',
        'tanggal_kembali',
        'status',
        'denda',
        'denda_kerusakan',
        'catatan',
    ];

    /** Menghubungkan data dengan peminjaman. */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }
}