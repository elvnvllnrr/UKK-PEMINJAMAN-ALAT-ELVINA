{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Admin Dashboard')

{{-- Tampilkan judul utama untuk bagian dasbor pengguna. --}}
@section('page-title', 'Dashboard Admin')

{{-- Isi utama tampilan: dasbor pengguna. --}}
@section('content')
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="icon bg-gradient-1 me-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ \App\Models\User::count() }}</h3>
                    <small class="text-muted">Total Users</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="icon bg-gradient-2 me-3">
                    <i class="fas fa-box"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ \App\Models\Alat::count() }}</h3>
                    <small class="text-muted">Total Alat</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="icon bg-gradient-3 me-3">
                    <i class="fas fa-hand-holding"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ \App\Models\Peminjaman::where('status', 'Dipinjam')->count() }}</h3>
                    <small class="text-muted">Sedang Dipinjam</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="icon bg-gradient-4 me-3">
                    <i class="fas fa-undo"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold">{{ \App\Models\Pengembalian::count() }}</h3>
                    <small class="text-muted">Total Pengembalian</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line me-2"></i>Aktivitas Terbaru
            </div>
            <div class="card-body">
                @php
                    $logs = \App\Models\LogAktivitas::with('user')->latest()->take(10)->get();
                @endphp

{{-- Ulangi $log dari $logs untuk menampilkan dasbor pengguna; saat koleksi kosong, gunakan state kosong. --}}
                @forelse($logs as $log)
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <div class="user-avatar me-3" style="width: 40px; height: 40px;">
                            {{ strtoupper(substr($log->user->name ?? 'U', 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $log->aktivitas }}</div>
                            <small class="text-muted">{{ $log->deskripsi }}</small>
                        </div>
                        <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                    </div>
{{-- Tampilkan status kosong ketika koleksi tidak memiliki data. --}}
                @empty
                    <p class="text-muted text-center">Belum ada aktivitas</p>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bell me-2"></i>Notifikasi
            </div>
            <div class="card-body">
                @php
                    $pendingPeminjaman = \App\Models\Peminjaman::where('status', 'Menunggu Persetujuan')->count();
                    $overdue = \App\Models\Peminjaman::where('status', 'Dipinjam')
                        ->where('tanggal_kembali_rencana', '<', now())
                        ->count();
                @endphp

{{-- Tampilkan blok ini hanya jika kondisi ($pendingPeminjaman > 0) terpenuhi. --}}
                @if($pendingPeminjaman > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-clock me-2"></i>
                        <strong>{{ $pendingPeminjaman }}</strong> peminjaman menunggu persetujuan
                    </div>
                @endif

{{-- Tampilkan blok ini hanya jika kondisi ($overdue > 0) terpenuhi. --}}
                @if($overdue > 0)
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>{{ $overdue }}</strong> peminjaman terlambat
                    </div>
                @endif

{{-- Tampilkan blok ini hanya jika kondisi ($pendingPeminjaman == 0 && $overdue == 0) terpenuhi. --}}
                @if($pendingPeminjaman == 0 && $overdue == 0)
                    <p class="text-muted text-center">Tidak ada notifikasi</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection