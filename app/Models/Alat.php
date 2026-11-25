<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Menyimpan data alat, kategori, stok, dan relasi peminjaman. */
class Alat extends Model
{
    use HasFactory;

    protected $table = 'alat';

    protected $fillable = [
        'nama_alat',
        'kategori_id',
        'kode_alat',
        'deskripsi',
        'stok',
        'kondisi',
        'foto',
    ];

    /** Menghubungkan alat dengan kategorinya. */
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    /** Mengambil semua rincian peminjaman yang memakai alat. */
    public function detailPeminjaman()
    {
        return $this->hasMany(DetailPeminjaman::class, 'alat_id');
    }
    /** Menghasilkan URL foto alat yang aman diakses dari berbagai konfigurasi server. */
    public function getFotoUrlAttribute(): ?string
    {
        if (! $this->foto) {
            return null;
        }

        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }

        return route('media.alat', ['filename' => basename($this->foto)]);
    }

    /** Menyaring alat yang stoknya lebih dari nol. */
    public function scopeTersedia($query)
    {
        return $query->where('stok', '>', 0);
    }
}