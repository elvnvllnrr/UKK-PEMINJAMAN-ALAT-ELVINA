{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Peminjaman Saya')

{{-- Tampilkan judul utama untuk bagian riwayat peminjaman pengguna. --}}
@section('page-title', 'Peminjaman Saya')

{{-- Isi utama tampilan: riwayat peminjaman pengguna. --}}
@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Peminjaman Saya</h1>
        <p class="text-muted mb-0">Denda keterlambatan dihitung otomatis Rp {{ number_format(config('peminjaman.denda_keterlambatan_per_hari'), 0, ',', '.') }} per hari per peminjaman. Denda kerusakan atau kehilangan ditentukan petugas.</p>
    </div>
    <a href="{{ route('peminjam.katalog') }}" class="btn btn-primary">
        <i class="fas fa-plus me-2"></i>Ajukan Peminjaman
    </a>
</div>

<div class="card">
    <div class="table-responsive">

{{-- Tabel ringkasan data pada riwayat peminjaman pengguna. --}}
        <table class="table table-hover align-middle mb-0">

{{-- Header kolom tabel. --}}
            <thead>
                <tr>
                    <th scope="col">Alat</th>
                    <th scope="col">Tanggal Pinjam</th>
                    <th scope="col">Rencana Kembali</th>
                    <th scope="col">Pengembalian</th>
                    <th scope="col">Denda</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>

{{-- Baris tabel hasil data pada riwayat peminjaman pengguna. --}}
            <tbody>

{{-- Ulangi $item dari $peminjaman untuk menampilkan riwayat peminjaman pengguna; saat koleksi kosong, gunakan state kosong. --}}
                @forelse($peminjaman as $item)
                    @php
                        $statusClass = match ($item->status) {
                            'Menunggu Persetujuan' => 'bg-warning text-dark',
                            'Dipinjam' => 'bg-primary',
                            'Dikembalikan' => 'bg-success',
                            'Terlambat' => 'bg-danger',
                            'Ditolak' => 'bg-secondary',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">

{{-- Ulangi $detail dari $item->detailPeminjaman untuk membangun isi berulang pada riwayat peminjaman pengguna. --}}
                                @foreach($item->detailPeminjaman as $detail)
                                    <div>{{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }} <span class="text-muted">× {{ $detail->jumlah }}</span></div>
                                @endforeach
                            </div>

{{-- Tampilkan blok ini hanya jika kondisi ($item->catatan) terpenuhi. --}}
                            @if($item->catatan)
                                <small class="text-muted">{{ $item->catatan }}</small>
                            @endif
                        </td>
                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y') }}</td>
                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d-m-Y') }}</td>
                        <td class="text-nowrap">
                            {{ $item->pengembalian ? \Carbon\Carbon::parse($item->pengembalian->tanggal_kembali)->format('d-m-Y') : 'Belum kembali' }}
                        </td>
                        <td class="text-nowrap">

{{-- Tampilkan blok ini hanya jika kondisi ($item->pengembalian && (float) $item->pengembalian->denda > 0) terpenuhi. --}}
                            @if($item->pengembalian && (float) $item->pengembalian->denda > 0)
                                Rp {{ number_format((float) $item->pengembalian->denda, 0, ',', '.') }}

{{-- Tangani cabang alternatif jika kondisi ($item->pengembalian) terpenuhi. --}}
                            @elseif($item->pengembalian)
                                -

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                            @else
                                Belum ditentukan
                            @endif
                        </td>
                        <td class="text-nowrap">
                            <span class="badge {{ $statusClass }}">{{ $item->status }}</span>

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                            @if($item->pengembalian && in_array($item->pengembalian->status, ['Rusak', 'Hilang'], true))
                                <span class="badge bg-warning text-dark mt-1">{{ $item->pengembalian->status }}</span>
                            @endif

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                            @if($item->pengembalian && $item->pengembalian->tanggal_kembali > $item->tanggal_kembali_rencana && $item->pengembalian->status !== 'Terlambat')
                                <span class="text-danger small d-block mt-1">Juga terlambat</span>
                            @endif

{{-- Tampilkan blok ini hanya jika kondisi ($item->pengembalian_diajukan_at && !$item->pengembalian) terpenuhi. --}}
                            @if($item->pengembalian_diajukan_at && !$item->pengembalian)
                                <span class="badge bg-warning text-dark mt-1">Menunggu pemeriksaan</span>
                            @endif
                        </td>
                        <td class="text-nowrap">

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
                            @if(in_array($item->status, ['Dipinjam', 'Terlambat'], true))

{{-- Tampilkan blok ini hanya jika kondisi ($item->pengembalian_diajukan_at) terpenuhi. --}}
                                @if($item->pengembalian_diajukan_at)
                                    <span class="text-muted small">Serahkan alat ke petugas</span>

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                                @else

{{-- Form untuk menjalankan proses rute peminjam.peminjaman.ajukan-pengembalian. --}}
                                    <form action="{{ route('peminjam.peminjaman.ajukan-pengembalian', $item) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa Anda akan menyerahkan alat ini kepada petugas?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-undo me-1"></i>Ajukan Pengembalian
                                        </button>
                                    </form>
                                @endif

{{-- Tangani cabang alternatif jika kondisi ($item->status === 'Dikembalikan') terpenuhi. --}}
                            @elseif($item->status === 'Dikembalikan')
                                <span class="text-success"><i class="fas fa-check-circle me-1"></i>Selesai</span>

{{-- Tampilkan fallback bila seluruh cabang kondisi sebelumnya tidak terpenuhi. --}}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
{{-- Tampilkan status kosong ketika koleksi tidak memiliki data. --}}
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-2x text-muted mb-3" aria-hidden="true"></i>
                            <p class="fw-semibold mb-1">Belum ada peminjaman</p>
                            <p class="text-muted mb-3">Pilih alat di katalog untuk membuat permohonan.</p>
                            <a href="{{ route('peminjam.katalog') }}" class="btn btn-primary btn-sm">Buka Katalog</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $peminjaman->links() }}
</div>
@endsection
