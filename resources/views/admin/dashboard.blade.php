{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Dashboard Admin')

{{-- Tampilkan judul utama untuk bagian ringkasan aktivitas dan statistik pengelolaan. --}}
@section('page-title', 'Ringkasan')

{{-- Isi utama tampilan: ringkasan aktivitas dan statistik pengelolaan. --}}
@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Ringkasan sistem</h1>
    <p class="text-muted mb-0">Pantau status peminjaman. Persetujuan dan pemeriksaan barang menjadi tugas petugas.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.peminjaman.index', ['status' => 'Menunggu Persetujuan']) }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <small class="text-muted">Menunggu persetujuan petugas</small>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="h2 fw-bold mb-0 text-dark">{{ $stats['permohonan_menunggu'] }}</span>
                    <i class="fas fa-clipboard-list text-warning fs-3" aria-hidden="true"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.pengembalian.index') }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <small class="text-muted">Menunggu pemeriksaan petugas</small>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="h2 fw-bold mb-0 text-dark">{{ $stats['pengembalian_menunggu'] }}</span>
                    <i class="fas fa-undo text-primary fs-3" aria-hidden="true"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.peminjaman.index', ['status' => 'Aktif']) }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <small class="text-muted">Sedang dipinjam</small>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="h2 fw-bold mb-0 text-dark">{{ $stats['peminjaman_aktif'] }}</span>
                    <i class="fas fa-box-open text-success fs-3" aria-hidden="true"></i>
                </div>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="{{ route('admin.alat.index') }}" class="text-decoration-none">
            <div class="stat-card h-100">
                <small class="text-muted">Jenis alat tersedia</small>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <span class="h2 fw-bold mb-0 text-dark">{{ $stats['alat_tersedia'] }}</span>
                    <i class="fas fa-toolbox text-info fs-3" aria-hidden="true"></i>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-6">
{{-- Bagian konten khusus untuk ringkasan aktivitas dan statistik pengelolaan. --}}
        <section class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-clipboard-list me-2"></i>Permohonan menunggu petugas</span>
                <a href="{{ route('admin.peminjaman.index', ['status' => 'Menunggu Persetujuan']) }}" class="btn btn-sm btn-light">Lihat semua</a>
            </div>
            <div class="card-body">

{{-- Ulangi $item dari $peminjamanMenunggu untuk menampilkan ringkasan aktivitas dan statistik pengelolaan; saat koleksi kosong, gunakan state kosong. --}}
                @forelse($peminjamanMenunggu as $item)
                    <div class="d-flex justify-content-between align-items-start gap-3 py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $item->user->name ?? 'Peminjam tidak tersedia' }}</div>
                            <div class="small text-muted">

{{-- Ulangi $detail dari $item->detailPeminjaman untuk membangun isi berulang pada ringkasan aktivitas dan statistik pengelolaan. --}}
                                @foreach($item->detailPeminjaman as $detail)
                                    {{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }} × {{ $detail->jumlah }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                            <small class="text-muted">Mulai {{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y') }}</small>
                        </div>
                        <a href="{{ route('admin.peminjaman.show', $item) }}" class="btn btn-sm btn-outline-primary">Pantau</a>
                    </div>
{{-- Tampilkan status kosong ketika koleksi tidak memiliki data. --}}
                @empty
                    <p class="text-muted mb-0">Tidak ada permohonan yang menunggu.</p>
                @endforelse
            </div>
        </section>
    </div>

    <div class="col-lg-6">
{{-- Bagian konten khusus untuk ringkasan aktivitas dan statistik pengelolaan. --}}
        <section class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-undo me-2"></i>Pengembalian menunggu petugas</span>
                <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-sm btn-light">Lihat semua</a>
            </div>
            <div class="card-body">

{{-- Ulangi $item dari $pengembalianMenunggu untuk menampilkan ringkasan aktivitas dan statistik pengelolaan; saat koleksi kosong, gunakan state kosong. --}}
                @forelse($pengembalianMenunggu as $item)
                    <div class="d-flex justify-content-between align-items-start gap-3 py-3 border-bottom">
                        <div>
                            <div class="fw-semibold">{{ $item->user->name ?? 'Peminjam tidak tersedia' }}</div>
                            <div class="small text-muted">

{{-- Ulangi $detail dari $item->detailPeminjaman untuk membangun isi berulang pada ringkasan aktivitas dan statistik pengelolaan. --}}
                                @foreach($item->detailPeminjaman as $detail)
                                    {{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }} × {{ $detail->jumlah }}@if(!$loop->last), @endif
                                @endforeach
                            </div>
                            <small class="text-muted">Diajukan {{ $item->pengembalian_diajukan_at->format('d-m-Y H:i') }}</small>
                        </div>
                        <a href="{{ route('admin.peminjaman.show', $item) }}" class="btn btn-sm btn-outline-primary">Pantau</a>
                    </div>
{{-- Tampilkan status kosong ketika koleksi tidak memiliki data. --}}
                @empty
                    <p class="text-muted mb-0">Tidak ada pengembalian yang menunggu pemeriksaan.</p>
                @endforelse
            </div>
        </section>
    </div>
</div>

<div class="d-flex flex-wrap gap-2 mt-4">
    <a href="{{ route('admin.alat.index') }}" class="btn btn-outline-primary"><i class="fas fa-toolbox me-2"></i>Kelola alat</a>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-users me-2"></i>Kelola pengguna</a>
    <a href="{{ route('admin.kategori.index') }}" class="btn btn-outline-secondary"><i class="fas fa-tags me-2"></i>Kelola kategori</a>
</div>
@endsection
