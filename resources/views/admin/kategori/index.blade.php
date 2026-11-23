{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Manajemen Kategori')

{{-- Tampilkan judul utama untuk bagian daftar kategori alat. --}}
@section('page-title', 'Manajemen Kategori')

{{-- Isi utama tampilan: daftar kategori alat. --}}
@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-tags me-2"></i>Daftar Kategori</span>
        <a href="{{ route('admin.kategori.create') }}" class="btn btn-light">
            <i class="fas fa-plus me-2"></i>Tambah Kategori
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">

{{-- Tabel ringkasan data pada daftar kategori alat. --}}
            <table class="table table-hover">

{{-- Header kolom tabel. --}}
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Alat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

{{-- Baris tabel hasil data pada daftar kategori alat. --}}
                <tbody>

{{-- Ulangi $index => $kategori dari $kategoris untuk menampilkan daftar kategori alat; saat koleksi kosong, gunakan state kosong. --}}
                    @forelse($kategoris as $index => $kategori)
                        <tr>
                            <td>{{ $kategoris->firstItem() + $index }}</td>
                            <td>{{ $kategori->nama_kategori }}</td>
                            <td>{{ $kategori->deskripsi ?? '-' }}</td>
                            <td>{{ $kategori->alat_count }}</td>
                            <td>
                                <a href="{{ route('admin.kategori.edit', $kategori) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>

{{-- Form untuk mengirim permintaan penghapusan pada daftar kategori alat. --}}
                                <form action="{{ route('admin.kategori.destroy', $kategori) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus kategori ini?')">
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
                            <td colspan="5" class="text-center text-muted">Belum ada data kategori</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $kategoris->links() }}
    </div>
</div>
@endsection
