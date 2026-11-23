<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\Alat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LogAktivitas;

/** Mengelola peminjaman melalui antarmuka peminjam. */
class PeminjamanController extends Controller
{
    /** Menampilkan peminjaman beserta pengguna dan detail alat. */
    public function index()
    {
        $peminjaman = Peminjaman::with(['user', 'detailPeminjaman.alat'])
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(10);
        return view('admin.peminjaman.index', compact('peminjaman'));
    }

    /** Menyiapkan pilihan pengguna peminjam pada formulir. */
    public function create()
    {
        $users = User::where('role', 'peminjam')->orderBy('name')->get();
        $alats = Alat::where('stok', '>', 0)->with('kategori')->get();
        return view('admin.peminjaman.create', compact('users', 'alats'));
    }

    /** Memvalidasi data lalu menyimpan peminjaman baru. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'catatan' => 'nullable|string',
            'alat' => 'required|array|min:1',
            'alat.*.alat_id' => 'required|exists:alat,id',
            'alat.*.jumlah' => 'required|integer|min:1',
        ]);
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::create([
                'user_id' => $validated['user_id'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'status' => 'Dipinjam',
                'catatan' => $validated['catatan'],
            ]);

            foreach ($validated['alat'] as $item) {
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $item['alat_id'],
                    'jumlah' => $item['jumlah'],
                    'kondisi_pinjam' => 'Baik',
                ]);

                // Pengurangan stok mengikuti jumlah pada tiap rincian peminjaman.
                Alat::where('id', $item['alat_id'])->decrement('stok', $item['jumlah']);
            }

            DB::commit();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Create Peminjaman',
                'deskripsi' => 'Membuat peminjaman baru ID: ' . $peminjaman->id,
            ]);

            return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat peminjaman: ' . $e->getMessage());
        }
    }

    /** Memuat pengguna dan rincian alat untuk halaman detail. */
    public function show(Peminjaman $peminjaman)
    {
        $peminjaman->load(['user', 'detailPeminjaman.alat', 'pengembalian']);
        return view('admin.peminjaman.show', compact('peminjaman'));
    }

    /** Memuat data peminjaman pada formulir perubahan. */
    public function edit(Peminjaman $peminjaman)
    {
        $peminjaman->load('detailPeminjaman.alat');
        $users = User::where('role', 'peminjam')->orderBy('name')->get();
        $alats = Alat::where('stok', '>', 0)->with('kategori')->get();
        return view('admin.peminjaman.edit', compact('peminjaman', 'users', 'alats'));
    }

    /** Memperbarui tanggal, status, atau rincian peminjaman yang dipilih. */
    public function update(Request $request, Peminjaman $peminjaman)
    {
        $validated = $request->validate([
            'tanggal_kembali_rencana' => 'required|date',
            'status' => 'required|in:Dipinjam,Dikembalikan,Terlambat',
            'catatan' => 'nullable|string',
        ]);

        $peminjaman->update($validated);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Update Peminjaman',
            'deskripsi' => 'Mengupdate peminjaman ID: ' . $peminjaman->id,
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil diupdate!');
    }

    /** Menghapus peminjaman dalam transaksi dan mengembalikan stok. */
    public function destroy(Peminjaman $peminjaman)
    {
        DB::beginTransaction();
        try {
            foreach ($peminjaman->detailPeminjaman as $detail) {
                // Kembalikan jumlah yang tercatat pada rincian alat yang dihapus.
                Alat::where('id', $detail->alat_id)->increment('stok', $detail->jumlah);
            }

            $peminjaman->detailPeminjaman()->delete();
            $peminjaman->delete();

            DB::commit();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Delete Peminjaman',
                'deskripsi' => 'Menghapus peminjaman ID: ' . $peminjaman->id,
            ]);

            return redirect()->route('peminjaman.index')->with('success', 'Peminjaman berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
        }
    }

    /** Menampilkan peminjaman milik pengguna yang masuk. */
    public function myPeminjaman()
    {
        $peminjaman = Peminjaman::where('user_id', auth()->id())
            ->with(['detailPeminjaman.alat', 'pengembalian'])
            ->orderBy('tanggal_pinjam', 'desc')
            ->paginate(10);
        return view('peminjam.peminjaman', compact('peminjaman'));
    }

    /** Memvalidasi stok lalu membuat pengajuan peminjaman. */
    public function ajukanPeminjaman(Request $request)
    {
        $validated = $request->validate([
            'tanggal_pinjam' => 'required|date',
            'tanggal_kembali_rencana' => 'required|date|after:tanggal_pinjam',
            'catatan' => 'nullable|string',
            'alat' => 'required|array|min:1',
            'alat.*.alat_id' => 'required|exists:alat,id',
            'alat.*.jumlah' => 'required|integer|min:1',
        ]);
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'status' => 'Menunggu Persetujuan',
                'catatan' => $validated['catatan'],
            ]);

            foreach ($validated['alat'] as $item) {
                DetailPeminjaman::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id' => $item['alat_id'],
                    'jumlah' => $item['jumlah'],
                    'kondisi_pinjam' => 'Baik',
                ]);
            }

            DB::commit();

            return redirect()->route('peminjam.peminjaman')->with('success', 'Peminjaman berhasil diajukan! Menunggu persetujuan petugas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    /** Menandai peminjaman disetujui dan mengurangi stok. */
    public function approve(Peminjaman $peminjaman)
    {
        DB::beginTransaction();
        try {
            $peminjaman->update(['status' => 'Dipinjam']);

            foreach ($peminjaman->detailPeminjaman as $detail) {
                // Kurangi stok sesuai jumlah unit pada rincian yang disetujui.
                Alat::where('id', $detail->alat_id)->decrement('stok', $detail->jumlah);
            }

            DB::commit();

            LogAktivitas::create([
                'user_id' => auth()->id(),
                'aktivitas' => 'Approve Peminjaman',
                'deskripsi' => 'Menyetujui peminjaman ID: ' . $peminjaman->id,
            ]);

            return back()->with('success', 'Peminjaman disetujui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    /** Mengubah peminjaman menjadi ditolak. */
    public function reject(Peminjaman $peminjaman)
    {
        $peminjaman->update(['status' => 'Ditolak']);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'aktivitas' => 'Reject Peminjaman',
            'deskripsi' => 'Menolak peminjaman ID: ' . $peminjaman->id,
        ]);

        return back()->with('success', 'Peminjaman ditolak!');
    }
}