{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Pengembalian')

{{-- Tampilkan judul utama untuk bagian daftar pengembalian alat. --}}
@section('page-title', 'Manajemen Pengembalian')

{{-- Isi utama tampilan: daftar pengembalian alat. --}}
@section('content')

{{-- Tampilkan blok ini hanya jika kondisi ($pengembalianMenunggu->isNotEmpty()) terpenuhi. --}}
@if($pengembalianMenunggu->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-clipboard-check me-2"></i>Menunggu pemeriksaan</span>
            <span class="badge bg-light text-dark">{{ $pengembalianMenunggu->count() }}</span>
        </div>
        <div class="table-responsive">

{{-- Tabel ringkasan data pada daftar pengembalian alat. --}}
            <table class="table table-hover align-middle mb-0">

{{-- Header kolom tabel. --}}
                <thead>
                    <tr>
                        <th>Peminjam</th>
                        <th>Alat</th>
                        <th>Rencana kembali</th>
                        <th>Diajukan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

{{-- Baris tabel hasil data pada daftar pengembalian alat. --}}
                <tbody>

{{-- Ulangi $peminjaman dari $pengembalianMenunggu untuk membangun isi berulang pada daftar pengembalian alat. --}}
                    @foreach($pengembalianMenunggu as $peminjaman)
                        <tr>
                            <td>{{ $peminjaman->user->name ?? '-' }} <small class="text-muted">#{{ $peminjaman->id }}</small></td>
                            <td>

{{-- Ulangi $detail dari $peminjaman->detailPeminjaman untuk membangun isi berulang pada daftar pengembalian alat. --}}
                                @foreach($peminjaman->detailPeminjaman as $detail)
                                    <div>{{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }} × {{ $detail->jumlah }}</div>
                                @endforeach
                            </td>
                            <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d-m-Y') }}</td>
                            <td>{{ $peminjaman->pengembalian_diajukan_at->format('d-m-Y H:i') }}</td>
                            <td>

{{-- Tampilkan blok ini hanya bagi pengguna yang sudah masuk. --}}
                                @if(auth()->user()->role === 'petugas')
                                    <a href="{{ route('petugas.pengembalian.create', $peminjaman->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-undo me-1"></i>Periksa
                                    </a>

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                                @else
                                    <span class="text-muted small">Ditangani petugas</span>
                                @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

{{-- Tampilkan blok ini hanya jika kondisi ($peminjamanBelumTercatat->isNotEmpty()) terpenuhi. --}}
@if($peminjamanBelumTercatat->isNotEmpty())
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-exclamation-circle me-2"></i>Pengembalian belum dicatat
            <span class="badge bg-light text-dark ms-2">{{ $peminjamanBelumTercatat->count() }}</span>
        </div>
        <div class="card-body">
            <p class="text-muted">
                {{ auth()->user()->role === 'petugas' ? 'Catat pengembalian yang belum memiliki rekam pemeriksaan.' : 'Ada pengembalian lama yang belum memiliki catatan pemeriksaan.' }}
            </p>
            <div class="table-responsive">

{{-- Tabel ringkasan data pada daftar pengembalian alat. --}}
                <table class="table table-hover align-middle mb-0">

{{-- Header kolom tabel. --}}
                    <thead>
                        <tr>
                            <th>No. Peminjaman</th>
                            <th>Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

{{-- Baris tabel hasil data pada daftar pengembalian alat. --}}
                    <tbody>

{{-- Ulangi $peminjaman dari $peminjamanBelumTercatat untuk membangun isi berulang pada daftar pengembalian alat. --}}
                        @foreach($peminjamanBelumTercatat as $peminjaman)
                            <tr>
                                <td>{{ $peminjaman->id }}</td>
                                <td>{{ $peminjaman->user->name ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d-m-Y') }}</td>
                                <td>

{{-- Tampilkan blok ini hanya bagi pengguna yang sudah masuk. --}}
                                    @if(auth()->user()->role === 'petugas')
                                        <a href="{{ route('petugas.pengembalian.create', $peminjaman->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-undo me-1"></i>Catat Pengembalian
                                        </a>

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                                    @else
                                        <span class="text-muted small">Ditangani petugas</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-undo me-2"></i>Riwayat Pengembalian
            <span class="badge bg-light text-dark ms-2">{{ $pengembalian->total() }}</span>
        </span>
    </div>
    <div class="card-body">
        <p class="text-muted">Bagian ini hanya menampilkan pengembalian yang sudah dicatat.</p>
        <div class="table-responsive">

{{-- Tabel ringkasan data pada daftar pengembalian alat. --}}
            <table class="table table-hover align-middle">

{{-- Header kolom tabel. --}}
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Peminjam</th>
                        <th>Tanggal Kembali</th>
                        <th>Status</th>
                        <th>Denda</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

{{-- Baris tabel hasil data pada daftar pengembalian alat. --}}
                <tbody>

{{-- Ulangi $index => $item dari $pengembalian untuk menampilkan daftar pengembalian alat; saat koleksi kosong, gunakan state kosong. --}}
                    @forelse($pengembalian as $index => $item)
                        <tr>
                            <td>{{ $pengembalian->firstItem() + $index }}</td>
                            <td>{{ $item->peminjaman->user->name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y') }}</td>
                            <td>

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                                @if($item->status === 'Tepat Waktu')
                                    <span class="badge bg-success">Tepat Waktu</span>

{{-- Tangani cabang alternatif jika kondisi ($item->status === 'Terlambat') terpenuhi. --}}
                                @elseif($item->status === 'Terlambat')
                                    <span class="badge bg-warning text-dark">Terlambat</span>

{{-- Tangani cabang alternatif jika kondisi ($item->status === 'Rusak') terpenuhi. --}}
                                @elseif($item->status === 'Rusak')
                                    <span class="badge bg-danger">Rusak</span>

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                                @else
                                    <span class="badge bg-secondary">Hilang</span>
                                @endif

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                                @if(in_array($item->status, ['Rusak', 'Hilang'], true)
                                    && $item->tanggal_kembali > $item->peminjaman->tanggal_kembali_rencana)
                                    <small class="d-block text-danger mt-1">Juga terlambat</small>
                                @endif
                            </td>
                            <td>
                                Rp {{ number_format($item->denda, 0, ',', '.') }}

{{-- Tampilkan blok ini hanya jika kondisi ($item->denda > 0) terpenuhi. --}}
                                @if($item->denda > 0)
                                    <small class="d-block text-muted">
                                        Terlambat: Rp {{ number_format($item->denda - $item->denda_kerusakan, 0, ',', '.') }}
                                        · Kerusakan: Rp {{ number_format($item->denda_kerusakan, 0, ',', '.') }}
                                    </small>
                                @endif
                            </td>
                            <td>{{ $item->catatan ?? '-' }}</td>
                            <td>
                                <a href="{{ auth()->user()->role === 'petugas' ? route('petugas.pengembalian.show', $item) : route('admin.pengembalian.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                    Lihat detail
                                </a>
                            </td>
                        </tr>
{{-- Tampilkan status kosong ketika koleksi tidak memiliki data. --}}
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada catatan pengembalian tersimpan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pengembalian->links() }}
    </div>
</div>
@endsection
