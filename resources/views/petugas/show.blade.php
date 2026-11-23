{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Detail Peminjaman')

{{-- Tampilkan judul utama untuk bagian detail peminjaman untuk petugas. --}}
@section('page-title', 'Detail Peminjaman #' . $peminjaman->id)

{{-- Isi utama tampilan: detail peminjaman untuk petugas. --}}
@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-alt me-2"></i>Informasi Peminjaman
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Nomor Peminjaman</label>
                        <div class="fw-bold">#{{ $peminjaman->id }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Status</label>
                        <div>

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                            @if($peminjaman->status === 'Menunggu Persetujuan')
                                <span class="badge bg-warning text-dark">Menunggu Persetujuan</span>

{{-- Tangani cabang alternatif jika kondisi ($peminjaman->status === 'Dipinjam') terpenuhi. --}}
                            @elseif($peminjaman->status === 'Dipinjam')
                                <span class="badge bg-success">Sedang Dipinjam</span>

{{-- Tangani cabang alternatif jika kondisi ($peminjaman->status === 'Terlambat') terpenuhi. --}}
                            @elseif($peminjaman->status === 'Terlambat')
                                <span class="badge bg-danger">Terlambat</span>

{{-- Tangani cabang alternatif jika kondisi ($peminjaman->status === 'Dikembalikan') terpenuhi. --}}
                            @elseif($peminjaman->status === 'Dikembalikan')
                                <span class="badge bg-secondary">Dikembalikan</span>

{{-- Tangani cabang alternatif jika kondisi ($peminjaman->status === 'Ditolak') terpenuhi. --}}
                            @elseif($peminjaman->status === 'Ditolak')
                                <span class="badge bg-secondary">Ditolak</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal Pinjam</label>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d M Y') }}</div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-muted small">Tanggal Kembali Rencana</label>
                        <div class="fw-bold">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d M Y') }}</div>
                    </div>
                </div>

{{-- Tampilkan blok ini hanya jika kondisi ($peminjaman->catatan) terpenuhi. --}}
                @if($peminjaman->catatan)
                    <div class="mb-3">
                        <label class="text-muted small">Catatan</label>
                        <p class="mb-0">{{ $peminjaman->catatan }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-box me-2"></i>Daftar Alat yang Dipinjam
            </div>
            <div class="card-body">
                <div class="table-responsive">

{{-- Tabel ringkasan data pada detail peminjaman untuk petugas. --}}
                    <table class="table table-sm">

{{-- Header kolom tabel. --}}
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Alat</th>
                                <th>Kode</th>
                                <th>Jumlah</th>
                                <th>Kondisi Pinjam</th>
                            </tr>
                        </thead>

{{-- Baris tabel hasil data pada detail peminjaman untuk petugas. --}}
                        <tbody>

{{-- Ulangi $index => $detail dari $peminjaman->detailPeminjaman untuk membangun isi berulang pada detail peminjaman untuk petugas. --}}
                            @foreach($peminjaman->detailPeminjaman as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->alat->nama_alat }}</td>
                                    <td><code>{{ $detail->alat->kode_alat }}</code></td>
                                    <td>{{ $detail->jumlah }}</td>
                                    <td><span class="badge bg-info">{{ $detail->kondisi_pinjam }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user me-2"></i>Informasi Peminjam
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="user-avatar mx-auto mb-2" style="width: 80px; height: 80px; font-size: 32px;">
                        {{ strtoupper(substr($peminjaman->user->name, 0, 1)) }}
                    </div>
                    <h5 class="mb-1">{{ $peminjaman->user->name }}</h5>
                    <small class="text-muted">{{ $peminjaman->user->role }}</small>
                </div>
                <hr>
                <div class="mb-2">
                    <small class="text-muted">Username</small>
                    <div class="fw-semibold">{{ $peminjaman->user->username }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Email</small>
                    <div class="fw-semibold">{{ $peminjaman->user->email }}</div>
                </div>
            </div>
        </div>

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
        @if($peminjaman->status === 'Menunggu Persetujuan')
            <div class="card mt-3">
                <div class="card-header bg-warning text-dark">
                    <i class="fas fa-tasks me-2"></i>Aksi
                </div>
                <div class="card-body">

{{-- Form untuk menjalankan proses rute petugas.peminjaman.approve. --}}
                    <form action="{{ route('petugas.peminjaman.approve', $peminjaman->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-check me-2"></i>Setujui &amp; serahkan alat
                        </button>
                    </form>

{{-- Form untuk menjalankan proses rute petugas.peminjaman.reject. --}}
                    <form action="{{ route('petugas.peminjaman.reject', $peminjaman->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Yakin menolak peminjaman ini?')">
                            <i class="fas fa-times me-2"></i>Tolak Peminjaman
                        </button>
                    </form>
                </div>
            </div>

{{-- Tangani cabang alternatif jika kondisi (in_array($peminjaman->status, ['Dipinjam', 'Terlambat'], true)) terpenuhi. --}}
        @elseif(in_array($peminjaman->status, ['Dipinjam', 'Terlambat'], true))
            <div class="card mt-3">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-undo me-2"></i>Pengembalian
                </div>
                <div class="card-body">

{{-- Tampilkan blok ini hanya jika kondisi ($peminjaman->pengembalian_diajukan_at) terpenuhi. --}}
                    @if($peminjaman->pengembalian_diajukan_at)
                        <a href="{{ route('petugas.pengembalian.create', $peminjaman->id) }}" class="btn btn-primary w-100">
                            <i class="fas fa-undo me-2"></i>Periksa dan konfirmasi
                        </a>

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                    @else
                        <p class="text-muted small">Peminjam belum mengajukan pengembalian. Minta peminjam mengirim pengajuan dari halaman Peminjaman Saya.</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('petugas.peminjaman.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Kembali
    </a>
</div>
@endsection