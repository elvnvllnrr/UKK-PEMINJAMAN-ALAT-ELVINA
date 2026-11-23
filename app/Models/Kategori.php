<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Menyimpan nama kategori dan relasi alatnya. */
class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama_kategori',
        'deskripsi',
    ];

    /** Menghubungkan data dengan alat. */
    public function alat()
    {
        return $this->hasMany(Alat::class, 'kategori_id');
    }
}