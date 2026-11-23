{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Pemantauan Peminjaman')

{{-- Tampilkan judul utama untuk bagian daftar peminjaman alat. --}}
@section('page-title', 'Pemantauan Peminjaman')

{{-- Isi utama tampilan: daftar peminjaman alat. --}}
@section('content')
<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Pemantauan peminjaman</h1>
    <p class="text-muted mb-0">Status keterlambatan diperbarui otomatis saat halaman dibuka. Persetujuan dan pemeriksaan barang ditangani petugas.</p>
</div>

<div class="row g-3 mb-4">

    @foreach([
        ['Semua peminjaman', $ringkasan['total'], null, 'text-primary'],
        ['Menunggu petugas', $ringkasan['menunggu'], 'Menunggu Persetujuan', 'text-warning'],
        ['Sedang dipinjam', $ringkasan['aktif'], 'Aktif', 'text-success'],
        ['Terlambat', $ringkasan['terlambat'], 'Terlambat', 'text-danger'],
        ['Pengembalian diajukan', $ringkasan['pengembalian_diajukan'], 'Pengembalian Diajukan', 'text-info'],
    ] as [$label, $jumlah, $filter, $warna])
        <div class="col-sm-6 col-xl">
            <a href="{{ $filter ? route('admin.peminjaman.index', ['status' => $filter]) : route('admin.peminjaman.index') }}" class="text-decoration-none">
                <div class="stat-card h-100">
                    <small class="text-muted">{{ $label }}</small>
                    <div class="h3 fw-bold mb-0 mt-2 {{ $warna }}">{{ $jumlah }}</div>
                </div>
            </a>
        </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <div class="d-flex flex-wrap gap-2">

            @foreach([
                'Semua' => null,
                'Menunggu petugas' => 'Menunggu Persetujuan',
                'Sedang dipinjam' => 'Aktif',
                'Terlambat' => 'Terlambat',
                'Pengembalian diajukan' => 'Pengembalian Diajukan',
                'Selesai' => 'Dikembalikan',
            ] as $label => $status)
                <a
                    href="{{ $status ? route('admin.peminjaman.index', ['status' => $status]) : route('admin.peminjaman.index') }}"
                    class="btn btn-sm {{ request('status') === $status || (!$status && !request('status')) ? 'btn-primary' : 'btn-outline-primary' }}"
                >{{ $label }}</a>
            @endforeach
        </div>
    </div>
    <div class="table-responsive">

{{-- Tabel ringkasan data pada daftar peminjaman alat. --}}
        <table class="table table-hover align-middle mb-0">

{{-- Header kolom tabel. --}}
            <thead>
                <tr>
                    <th>Peminjam</th>
                    <th>Alat</th>
                    <th>Tanggal pinjam</th>
                    <th>Batas kembali</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>

{{-- Baris tabel hasil data pada daftar peminjaman alat. --}}
            <tbody>

{{-- Ulangi $item dari $peminjaman untuk menampilkan daftar peminjaman alat; saat koleksi kosong, gunakan state kosong. --}}
                @forelse($peminjaman as $item)
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $item->user->name ?? '-' }}</div>
                            <small class="text-muted">#{{ $item->id }}</small>
                        </td>
                        <td>

{{-- Ulangi $detail dari $item->detailPeminjaman untuk membangun isi berulang pada daftar peminjaman alat. --}}
                            @foreach($item->detailPeminjaman as $detail)
                                <div>{{ $detail->alat->nama_alat ?? '-' }} × {{ $detail->jumlah }}</div>
                            @endforeach
                        </td>
                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->format('d-m-Y') }}</td>
                        <td class="text-nowrap">{{ \Carbon\Carbon::parse($item->tanggal_kembali_rencana)->format('d-m-Y') }}</td>
                        <td>

{{-- Pilih cabang tampilan berdasarkan nilai $item->status. --}}
                            @switch($item->status)

{{-- Tampilkan cabang untuk nilai 'Menunggu Persetujuan'. --}}
                                @case('Menunggu Persetujuan')
                                    <span class="badge bg-warning text-dark">Menunggu petugas</span>
                                    @break

{{-- Tampilkan cabang untuk nilai 'Dipinjam'. --}}
                                @case('Dipinjam')
                                    <span class="badge bg-primary">Dipinjam</span>
                                    @break

{{-- Tampilkan cabang untuk nilai 'Terlambat'. --}}
                                @case('Terlambat')
                                    <span class="badge bg-danger">Terlambat</span>
                                    @break

{{-- Tampilkan cabang untuk nilai 'Dikembalikan'. --}}
                                @case('Dikembalikan')
                                    <span class="badge bg-success">Dikembalikan</span>
                                    @break

{{-- Tampilkan cabang untuk nilai 'Ditolak'. --}}
                                @case('Ditolak')
                                    <span class="badge bg-secondary">Ditolak</span>
                                    @break
                            @endswitch

{{-- Tampilkan blok ini hanya jika kondisi ($item->pengembalian_diajukan_at && !$item->pengembalian) terpenuhi. --}}
                            @if($item->pengembalian_diajukan_at && !$item->pengembalian)
                                <small class="d-block text-info mt-1">Menunggu pemeriksaan petugas</small>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.peminjaman.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                Lihat detail
                            </a>
                        </td>
                    </tr>
{{-- Tampilkan status kosong ketika koleksi tidak memiliki data. --}}
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">Tidak ada peminjaman pada filter ini.</td>
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
