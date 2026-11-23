{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Dashboard Peminjam')

{{-- Tampilkan judul utama untuk bagian ringkasan peminjaman pengguna. --}}
@section('page-title', 'Ringkasan Peminjaman')

{{-- Isi utama tampilan: ringkasan peminjaman pengguna. --}}
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Halo, {{ auth()->user()->name }}</h1>
        <p class="text-muted mb-0">Lihat status alat dan langkah yang perlu Anda lakukan.</p>
    </div>
    <a href="{{ route('peminjam.katalog') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Ajukan Peminjaman
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="{{ route('peminjam.peminjaman') }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <small class="text-muted">Menunggu persetujuan</small>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="h2 fw-bold mb-0 text-dark">{{ $stats['menunggu_persetujuan'] }}</span>
                    <i class="fas fa-hourglass-half text-warning fs-3" aria-hidden="true"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('peminjam.peminjaman') }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <small class="text-muted">Sedang dipinjam</small>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="h2 fw-bold mb-0 text-dark">{{ $stats['peminjaman_aktif'] }}</span>
                    <i class="fas fa-box-open text-primary fs-3" aria-hidden="true"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-md-4">
        <a href="{{ route('peminjam.peminjaman') }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <small class="text-muted">Pengembalian diproses</small>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="h2 fw-bold mb-0 text-dark">{{ $stats['pengembalian_diproses'] }}</span>
                    <i class="fas fa-undo text-success fs-3" aria-hidden="true"></i>
                </div>
            </div>
        </a>
    </div>
</div>

{{-- Bagian konten khusus untuk ringkasan peminjaman pengguna. --}}
<section class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-list me-2"></i>Peminjaman terbaru</span>
        <a href="{{ route('peminjam.peminjaman') }}" class="btn btn-sm btn-light">Lihat semua</a>
    </div>
    <div class="card-body">

{{-- Ulangi $item dari $peminjamanTerbaru untuk menampilkan ringkasan peminjaman pengguna; saat koleksi kosong, gunakan state kosong. --}}
        @forelse($peminjamanTerbaru as $item)
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 border-bottom">
                <div>
                    <div class="fw-semibold">

{{-- Ulangi $detail dari $item->detailPeminjaman untuk membangun isi berulang pada ringkasan peminjaman pengguna. --}}
                        @foreach($item->detailPeminjaman as $detail)
                            {{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }} × {{ $detail->jumlah }}@if(!$loop->last), @endif
                        @endforeach
                    </div>
                    <small class="text-muted">
                        Kembali paling lambat {{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d-m-Y') }}
                    </small>
                </div>
                <div class="d-flex align-items-center gap-2">

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                    @if($item->status === 'Menunggu Persetujuan')
                        <span class="badge bg-warning text-dark">Menunggu persetujuan</span>

{{-- Tangani cabang alternatif jika kondisi ($item->status === 'Dipinjam') terpenuhi. --}}
                    @elseif($item->status === 'Dipinjam')
                        <span class="badge bg-primary">Sedang dipinjam</span>

{{-- Tangani cabang alternatif jika kondisi ($item->status === 'Terlambat') terpenuhi. --}}
                    @elseif($item->status === 'Terlambat')
                        <span class="badge bg-danger">Terlambat</span>

{{-- Tangani cabang alternatif jika kondisi ($item->status === 'Dikembalikan') terpenuhi. --}}
                    @elseif($item->status === 'Dikembalikan')
                        <span class="badge bg-success">Selesai</span>

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                    @else
                        <span class="badge bg-secondary">{{ $item->status }}</span>
                    @endif

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                    @if(in_array($item->status, ['Dipinjam', 'Terlambat'], true))

{{-- Tampilkan blok ini hanya jika kondisi ($item->pengembalian_diajukan_at) terpenuhi. --}}
                        @if($item->pengembalian_diajukan_at)
                            <span class="small text-muted">Menunggu pemeriksaan</span>

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                        @else

{{-- Form untuk menjalankan proses rute peminjam.peminjaman.ajukan-pengembalian. --}}
                            <form action="{{ route('peminjam.peminjaman.ajukan-pengembalian', $item) }}" method="POST" onsubmit="return confirm('Ajukan pengembalian untuk alat ini?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">Ajukan pengembalian</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
{{-- Tampilkan status kosong ketika koleksi tidak memiliki data. --}}
        @empty
            <div class="text-center py-4">
                <p class="fw-semibold mb-1">Belum ada peminjaman</p>
                <p class="text-muted mb-3">Pilih alat di katalog untuk mulai mengajukan.</p>
                <a href="{{ route('peminjam.katalog') }}" class="btn btn-primary">Buka katalog alat</a>
            </div>
        @endforelse
    </div>
</section>
@endsection
