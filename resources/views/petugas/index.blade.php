{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Daftar Peminjaman')

{{-- Tampilkan judul utama untuk bagian daftar peminjaman yang perlu diproses. --}}
@section('page-title', 'Kelola Peminjaman')

{{-- Isi utama tampilan: daftar peminjaman yang perlu diproses. --}}
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Kelola Peminjaman</h1>
        <p class="text-muted mb-0">Setujui permohonan dan konfirmasi barang yang sudah diterima.</p>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap gap-2">

            @foreach([
                'Semua' => null,
                'Menunggu' => 'Menunggu Persetujuan',
                'Dipinjam' => 'Dipinjam',
                'Terlambat' => 'Terlambat',
                'Pengembalian diajukan' => 'Pengembalian Diajukan',
            ] as $label => $status)
                <a
                    href="{{ $status ? route('petugas.peminjaman.index', ['status' => $status]) : route('petugas.peminjaman.index') }}"
                    class="btn btn-sm {{ request('status') === $status || (!$status && !request('status')) ? 'btn-primary' : 'btn-outline-primary' }}"
                >{{ $label }}</a>
            @endforeach
        </div>
    </div>
    <div class="table-responsive">

{{-- Tabel ringkasan data pada daftar peminjaman yang perlu diproses. --}}
        <table class="table table-hover align-middle mb-0">

{{-- Header kolom tabel. --}}
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Alat</th>
                    <th>Tanggal</th>
                    <th>Rencana kembali</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>

{{-- Baris tabel hasil data pada daftar peminjaman yang perlu diproses. --}}
            <tbody>

{{-- Ulangi $p dari $peminjaman untuk menampilkan daftar peminjaman yang perlu diproses; saat koleksi kosong, gunakan state kosong. --}}
                @forelse($peminjaman as $p)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $p->user->name ?? '-' }}</div>
                            <small class="text-muted">#{{ $p->id }}</small>
                        </td>
                        <td>

{{-- Ulangi $detail dari $p->detailPeminjaman untuk membangun isi berulang pada daftar peminjaman yang perlu diproses. --}}
                            @foreach($p->detailPeminjaman as $detail)
                                <div>{{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }} × {{ $detail->jumlah }}</div>
                            @endforeach
                        </td>
                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d-m-Y') }}</td>
                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($p->tanggal_kembali_rencana)->format('d-m-Y') }}</td>
                        <td class="text-nowrap">

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                            @if($p->status === 'Menunggu Persetujuan')
                                <span class="badge bg-warning text-dark">Menunggu persetujuan</span>

{{-- Tangani cabang alternatif jika kondisi ($p->status === 'Dipinjam') terpenuhi. --}}
                            @elseif($p->status === 'Dipinjam')
                                <span class="badge bg-primary">Dipinjam</span>

{{-- Tangani cabang alternatif jika kondisi ($p->status === 'Terlambat') terpenuhi. --}}
                            @elseif($p->status === 'Terlambat')
                                <span class="badge bg-danger">Terlambat</span>

{{-- Tangani cabang alternatif jika kondisi ($p->status === 'Dikembalikan') terpenuhi. --}}
                            @elseif($p->status === 'Dikembalikan')
                                <span class="badge bg-success">Dikembalikan</span>

{{-- Tangani cabang alternatif jika kondisi ($p->status === 'Ditolak') terpenuhi. --}}
                            @elseif($p->status === 'Ditolak')
                                <span class="badge bg-secondary">Ditolak</span>
                            @endif

{{-- Tampilkan blok ini hanya jika kondisi ($p->pengembalian_diajukan_at && !$p->pengembalian) terpenuhi. --}}
                            @if($p->pengembalian_diajukan_at && !$p->pengembalian)
                                <span class="badge bg-info text-dark mt-1">Pengembalian diajukan</span>
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <a href="{{ route('petugas.peminjaman.show', $p) }}" class="btn btn-sm btn-outline-secondary">Detail</a>

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                            @if($p->status === 'Menunggu Persetujuan')

{{-- Form untuk menjalankan proses rute petugas.peminjaman.approve. --}}
                                <form action="{{ route('petugas.peminjaman.approve', $p) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Setujui permohonan dan serahkan alat ini?')">Setujui &amp; serahkan</button>
                                </form>

{{-- Form untuk menjalankan proses rute petugas.peminjaman.reject. --}}
                                <form action="{{ route('petugas.peminjaman.reject', $p) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Tolak permohonan ini?')">Tolak</button>
                                </form>

{{-- Tangani cabang alternatif jika kondisi (in_array($p->status, ['Dipinjam', 'Terlambat'], true) && $p->pengembalian_diajukan_at && !$p->pengembalian) terpenuhi. --}}
                            @elseif(in_array($p->status, ['Dipinjam', 'Terlambat'], true) && $p->pengembalian_diajukan_at && !$p->pengembalian)
                                <a href="{{ route('petugas.pengembalian.create', $p->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-undo me-1"></i>Periksa pengembalian
                                </a>
                            @endif
                        </td>
                    </tr>
{{-- Tampilkan status kosong ketika koleksi tidak memiliki data. --}}
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">Belum ada data untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-body">
        {{ $peminjaman->links() }}
    </div>
</div>
@endsection
