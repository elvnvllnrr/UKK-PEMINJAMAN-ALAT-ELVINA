<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\Request;

/** Menampilkan katalog dan detail alat bagi peminjam. */
class AlatController extends Controller
{
    /** Menampilkan alat beserta kategori dalam daftar katalog. */
    public function index()
    {
        $alats = Alat::with('kategori')->orderBy('nama_alat')->paginate(12);
        return view('petugas.alat.index', compact('alats'));
    }

    /** Memuat kategori dan peminjaman alat sebelum menampilkan detailnya. */
    public function show($id)
    {
        $alat = Alat::with(['kategori', 'detailPeminjaman.peminjaman.user'])->findOrFail($id);
        return view('petugas.alat.show', compact('alat'));
    }
}