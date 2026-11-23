<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** Menyimpan deskripsi aktivitas beserta pengguna pelakunya. */
class LogAktivitas extends Model
{
    use HasFactory;

    protected $table = 'log_aktivitas';

    protected $fillable = [
        'user_id',
        'aktivitas',
        'deskripsi',
    ];

    /** Menghubungkan data dengan pengguna. */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}