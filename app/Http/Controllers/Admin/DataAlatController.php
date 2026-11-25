<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/** Mengelola katalog, CRUD, stok awal, dan foto alat. */
class DataAlatController extends Controller
{
    /** Menampilkan alat beserta kategori dengan paginasi. */
    public function index(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $alats = Alat::with('kategori')
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%'.$search.'%';

                $query->where(function ($query) use ($keyword) {
                    $query->where('nama_alat', 'like', $keyword)
                        ->orWhere('kode_alat', 'like', $keyword)
                        ->orWhereHas('kategori', function ($query) use ($keyword) {
                            $query->where('nama_kategori', 'like', $keyword);
                        });
                });
            })
            ->orderBy('nama_alat')
            ->paginate(12)
            ->withQueryString();

        return view('admin.alat.index', compact('alats', 'search'));
    }

    /** Menyiapkan pilihan kategori pada formulir alat baru. */
    public function create()
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('admin.alat.create', compact('kategoris'));
    }

    /** Memvalidasi unggahan dan menyimpan alat beserta stok awal. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'kode_alat' => 'required|string|unique:alat,kode_alat|max:50',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('foto_alat', 'public');
        }

        Alat::create($validated);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Create Alat',
            'deskripsi' => 'Menambah alat: '.$validated['nama_alat'],
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Alat berhasil ditambahkan!');
    }

    /** Memuat kategori lalu menampilkan detail alat. */
    public function show(Alat $alat)
    {
        $alat->load('kategori');

        return view('admin.alat.show', compact('alat'));
    }

    /** Menyiapkan alat dan daftar kategori pada formulir perubahan. */
    public function edit(Alat $alat)
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('admin.alat.edit', compact('alat', 'kategoris'));
    }

    /** Memperbarui data atau foto alat dan menghapus foto lama bila diganti. */
    public function update(Request $request, Alat $alat)
    {
        $validated = $request->validate([
            'nama_alat' => 'required|string|max:255',
            'kategori_id' => 'required|exists:kategori,id',
            'kode_alat' => 'required|string|unique:alat,kode_alat,'.$alat->id.'|max:50',
            'deskripsi' => 'nullable|string',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|in:Baik,Rusak Ringan,Rusak Berat',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            if ($alat->foto) {
                Storage::disk('public')->delete($alat->foto);
            }
            $validated['foto'] = $request->file('foto')->store('foto_alat', 'public');
        }

        $alat->update($validated);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Update Alat',
            'deskripsi' => 'Mengupdate alat: '.$validated['nama_alat'],
        ]);

        return redirect()->route('admin.alat.index')->with('success', 'Alat berhasil diupdate!');
    }

    /** Menghapus data dan berkas foto alat. */
    public function destroy(Alat $alat)
    {
        DB::transaction(function () use ($alat) {
            $peminjamanIds = $alat->detailPeminjaman()
                ->pluck('peminjaman_id')
                ->unique()
                ->values();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Delete Alat',
                'deskripsi' => 'Menghapus alat beserta riwayat detail: '.$alat->nama_alat,
            ]);

            $alat->delete();

            if ($peminjamanIds->isNotEmpty()) {
                Peminjaman::whereKey($peminjamanIds)
                    ->whereDoesntHave('detailPeminjaman')
                    ->delete();
            }

            if ($alat->foto) {
                Storage::disk('public')->delete($alat->foto);
            }
        });

        return redirect()->route('admin.alat.index')->with('success', 'Alat berhasil dihapus!');
    }

    /** Menampilkan alat yang stoknya tersedia. */
    public function katalog(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $alats = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->when($search !== '', function ($query) use ($search) {
                $keyword = '%'.$search.'%';

                $query->where(function ($query) use ($keyword) {
                    $query->where('nama_alat', 'like', $keyword)
                        ->orWhere('kode_alat', 'like', $keyword)
                        ->orWhereHas('kategori', function ($query) use ($keyword) {
                            $query->where('nama_kategori', 'like', $keyword);
                        });
                });
            })
            ->orderBy('nama_alat')
            ->get();

        return view('peminjam.katalog', compact('alats', 'search'));
    }
}









