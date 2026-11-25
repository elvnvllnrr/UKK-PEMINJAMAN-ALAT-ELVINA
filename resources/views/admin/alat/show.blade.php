{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Detail Alat')

{{-- Tampilkan judul utama untuk bagian detail alat. --}}
@section('page-title', 'Detail Alat')

{{-- Isi utama tampilan: detail alat. --}}
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-info-circle me-2"></i>Detail Alat</span>
        <a href="{{ route('admin.alat.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 text-center">

{{-- Tampilkan blok ini hanya jika kondisi ($alat->foto) terpenuhi. --}}
                @if($alat->foto)
                    <img src="{{ $alat->foto_url }}" alt="{{ $alat->nama_alat }}" class="img-fluid rounded border" style="max-height: 280px; object-fit: cover;">

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                @else
                    <div class="border rounded p-5 text-muted">Tidak ada foto</div>
                @endif
            </div>
            <div class="col-md-8">

{{-- Tabel ringkasan data pada detail alat. --}}
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 180px;">Nama Alat</th>
                        <td>{{ $alat->nama_alat }}</td>
                    </tr>
                    <tr>
                        <th>Kategori</th>
                        <td>{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Kode Alat</th>
                        <td>{{ $alat->kode_alat }}</td>
                    </tr>
                    <tr>
                        <th>Stok</th>
                        <td>{{ $alat->stok }}</td>
                    </tr>
                    <tr>
                        <th>Kondisi</th>
                        <td>

{{-- Tampilkan blok ini hanya jika kondisi ($alat->kondisi === 'Baik') terpenuhi. --}}
                            @if($alat->kondisi === 'Baik')
                                <span class="badge bg-success">Baik</span>

{{-- Tangani cabang alternatif jika kondisi ($alat->kondisi === 'Rusak Ringan') terpenuhi. --}}
                            @elseif($alat->kondisi === 'Rusak Ringan')
                                <span class="badge bg-warning text-dark">Rusak Ringan</span>

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                            @else
                                <span class="badge bg-danger">Rusak Berat</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Deskripsi</th>
                        <td>{{ $alat->deskripsi ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
