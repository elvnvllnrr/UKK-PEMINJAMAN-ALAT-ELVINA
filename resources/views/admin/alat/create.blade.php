{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Tambah Alat')

{{-- Tampilkan judul utama untuk bagian penambahan alat baru. --}}
@section('page-title', 'Tambah Alat Baru')

{{-- Isi utama tampilan: penambahan alat baru. --}}
@section('content')
<div class="card">
    <div class="card-header">
        <i class="fas fa-box me-2"></i>Form Tambah Alat
    </div>
    <div class="card-body">

{{-- Form untuk menyimpan data baru pada penambahan alat baru. --}}
        <form action="{{ route('admin.alat.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Nama Alat <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('nama_alat') is-invalid @enderror" name="nama_alat" value="{{ old('nama_alat') }}" required>
                    @error('nama_alat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                    <select class="form-select @error('kategori_id') is-invalid @enderror" name="kategori_id" required>
                        <option value="">Pilih Kategori</option>

{{-- Ulangi $kategori dari $kategoris untuk membangun isi berulang pada penambahan alat baru. --}}
                        @foreach($kategoris as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_id') == $kategori->id ? 'selected' : '' }}>{{ $kategori->nama_kategori }}</option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Kode Alat <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('kode_alat') is-invalid @enderror" name="kode_alat" value="{{ old('kode_alat') }}" required>
                    @error('kode_alat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('stok') is-invalid @enderror" name="stok" value="{{ old('stok', 0) }}" min="0" required>
                    @error('stok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror" name="deskripsi" rows="4">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Kondisi <span class="text-danger">*</span></label>
                    <select class="form-select @error('kondisi') is-invalid @enderror" name="kondisi" required>
                        <option value="Baik" {{ old('kondisi') === 'Baik' ? 'selected' : '' }}>Baik</option>
                        <option value="Rusak Ringan" {{ old('kondisi') === 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="Rusak Berat" {{ old('kondisi') === 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                    @error('kondisi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Foto</label>
                    <input type="file" class="form-control @error('foto') is-invalid @enderror" name="foto" accept="image/*">
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan
                </button>
                <a href="{{ route('admin.alat.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
