{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Detail Peminjaman')

{{-- Tampilkan judul utama untuk bagian detail peminjaman alat. --}}
@section('page-title', 'Detail Peminjaman')

{{-- Isi utama tampilan: detail peminjaman alat. --}}
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-info-circle me-2"></i>Detail Peminjaman</span>
        <a href="{{ route('admin.peminjaman.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    <div class="card-body">

{{-- Tabel ringkasan data pada detail peminjaman alat. --}}
        <table class="table table-borderless">
            <tr>
                <th style="width: 200px;">Peminjam</th>
                <td>{{ $peminjaman->user->name ?? '-' }}</td>
            </tr>
            <tr>
                <th>Tanggal Pinjam</th>
                <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Jadwal Kembali</th>
                <td>{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>{{ $peminjaman->status }}</td>
            </tr>
            <tr>
                <th>Catatan</th>
                <td>{{ $peminjaman->catatan ?? '-' }}</td>
            </tr>
        </table>

        <h5 class="mt-4">Daftar Alat</h5>

{{-- Tabel ringkasan data pada detail peminjaman alat. --}}
        <table class="table table-striped">

{{-- Header kolom tabel. --}}
            <thead>
                <tr>
                    <th>Nama Alat</th>
                    <th>Jumlah</th>
                    <th>Kondisi Pinjam</th>
                </tr>
            </thead>

{{-- Baris tabel hasil data pada detail peminjaman alat. --}}
            <tbody>

{{-- Ulangi $detail dari $peminjaman->detailPeminjaman untuk membangun isi berulang pada detail peminjaman alat. --}}
                @foreach($peminjaman->detailPeminjaman as $detail)
                    <tr>
                        <td>{{ $detail->alat->nama_alat ?? '-' }}</td>
                        <td>{{ $detail->jumlah }}</td>
                        <td>{{ $detail->kondisi_pinjam }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
