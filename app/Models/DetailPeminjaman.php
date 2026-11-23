<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Menyimpan jumlah dan kondisi alat pada satu peminjaman. */
class DetailPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'detail_peminjaman';

    protected $fillable = [
        'peminjaman_id',
        'alat_id',
        'jumlah',
        'kondisi_pinjam',
        'kondisi_kembali',
    ];

    /** Menghubungkan data dengan peminjaman. */
    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'peminjaman_id');
    }

    /** Menghubungkan data dengan alat. */
    public function alat()
    {
        return $this->belongsTo(Alat::class, 'alat_id');
    }
}