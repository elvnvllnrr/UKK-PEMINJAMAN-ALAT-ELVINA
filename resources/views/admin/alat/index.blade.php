{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Manajemen Alat')

{{-- Tampilkan judul utama untuk bagian daftar alat beserta status ketersediaannya. --}}
@section('page-title', 'Manajemen Alat')

{{-- Isi utama tampilan: daftar alat beserta status ketersediaannya. --}}
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-boxes me-2"></i>Daftar Alat</span>
        <a href="{{ route('admin.alat.create') }}" class="btn btn-light">
            <i class="fas fa-plus me-2"></i>Tambah Alat
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">

{{-- Tabel ringkasan data pada daftar alat beserta status ketersediaannya. --}}
            <table class="table table-hover align-middle">

{{-- Header kolom tabel. --}}
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Kode</th>
                        <th>Stok</th>
                        <th>Kondisi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

{{-- Baris tabel hasil data pada daftar alat beserta status ketersediaannya. --}}
                <tbody>

{{-- Ulangi $index => $alat dari $alats untuk menampilkan daftar alat beserta status ketersediaannya; saat koleksi kosong, gunakan state kosong. --}}
                    @forelse($alats as $index => $alat)
                        <tr>
                            <td>{{ $alats->firstItem() + $index }}</td>
                            <td>

{{-- Tampilkan blok ini hanya jika kondisi ($alat->foto) terpenuhi. --}}
                                @if($alat->foto)
                                    <img src="{{ asset('storage/' . $alat->foto) }}" alt="{{ $alat->nama_alat }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $alat->nama_alat }}</td>
                            <td>{{ $alat->kategori->nama_kategori ?? '-' }}</td>
                            <td>{{ $alat->kode_alat }}</td>
                            <td>{{ $alat->stok }}</td>
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
                            <td>
                                <a href="{{ route('admin.alat.show', $alat) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.alat.edit', $alat) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>

{{-- Form untuk mengirim permintaan penghapusan pada daftar alat beserta status ketersediaannya. --}}
                                <form action="{{ route('admin.alat.destroy', $alat) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus alat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
{{-- Tampilkan status kosong ketika koleksi tidak memiliki data. --}}
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada data alat</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $alats->links() }}
    </div>
</div>
@endsection
