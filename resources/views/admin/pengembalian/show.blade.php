{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Detail Pengembalian')

{{-- Tampilkan judul utama untuk bagian detail pengembalian alat. --}}
@section('page-title', 'Detail Pengembalian')

{{-- Isi utama tampilan: detail pengembalian alat. --}}
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-info-circle me-2"></i>Detail Pengembalian</span>
        <a href="{{ auth()->user()->role === 'petugas' ? route('petugas.pengembalian.index') : route('admin.pengembalian.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    <div class="card-body">

{{-- Tabel ringkasan data pada detail pengembalian alat. --}}
        <table class="table table-borderless">
            <tr>
                <th style="width: 200px;">Peminjam</th>
                <td>{{ $pengembalian->peminjaman->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Kembali</th>
                <td>{{ \Carbon\Carbon::parse($pengembalian->tanggal_kembali)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $pengembalian->status }}</td>
            </tr>
            <tr>
                <th>Denda keterlambatan</th>
                <td>
                    {{ $pengembalian->peminjaman->hitungHariTerlambat($pengembalian->tanggal_kembali) }} hari
                    · Rp {{ number_format($pengembalian->peminjaman->hitungHariTerlambat($pengembalian->tanggal_kembali) * config('peminjaman.denda_keterlambatan_per_hari'), 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <th>Denda kerusakan/kehilangan</th>
                <td>Rp {{ number_format($pengembalian->denda_kerusakan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Total denda</th>
                <td class="fw-bold">Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <th>Catatan</th>
                <td>{{ $pengembalian->catatan ?? '-' }}</td>
            </tr>
        </table>

        <h2 class="h5 mt-4">Kondisi alat saat kembali</h2>
        <div class="table-responsive">

{{-- Tabel ringkasan data pada detail pengembalian alat. --}}
            <table class="table table-sm">

{{-- Header kolom tabel. --}}
                <thead>
                    <tr>
                        <th>Alat</th>
                        <th>Jumlah</th>
                        <th>Kondisi</th>
                    </tr>
                </thead>

{{-- Baris tabel hasil data pada detail pengembalian alat. --}}
                <tbody>

{{-- Ulangi $detail dari $pengembalian->peminjaman->detailPeminjaman untuk membangun isi berulang pada detail pengembalian alat. --}}
                    @foreach($pengembalian->peminjaman->detailPeminjaman as $detail)
                        <tr>
                            <td>{{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>{{ $detail->kondisi_kembali ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
