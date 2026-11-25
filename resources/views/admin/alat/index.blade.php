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
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.alat.create') }}" class="btn btn-light">
                <i class="fas fa-plus me-2"></i>Tambah Alat
            </a>
        @endif
    </div>
    <div class="card-body">
        <form action="{{ auth()->user()->role === 'admin' ? route('admin.alat.index') : route('petugas.alat.index') }}" method="GET" id="alat-search-form" class="mb-4">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
                <input
                    type="search"
                    name="search"
                    id="alat-search"
                    class="form-control border-start-0"
                    placeholder="Cari nama alat, kategori, atau kode alat..."
                    value="{{ $search }}"
                    autocomplete="off"
                >
            </div>
        </form>

        <div class="table-responsive">

{{-- Tabel ringkasan data pada daftar alat beserta status ketersediaannya. --}}
            <table id="alat-table" class="table table-hover align-middle">

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
                                    <img src="{{ $alat->foto_url }}" alt="{{ $alat->nama_alat }}" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">

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
                            <td colspan="8" class="text-center text-muted">
                                {{ $search !== '' ? 'Data alat tidak ditemukan' : 'Belum ada data alat' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="alat-pagination">
            {{ $alats->links() }}
        </div>
    </div>
</div>
@push('scripts')
<script>
    (() => {
        const searchInput = document.getElementById('alat-search');
        const searchForm = document.getElementById('alat-search-form');
        const alatTable = document.getElementById('alat-table');
        const pagination = document.getElementById('alat-pagination');
        let searchTimer;
        let activeRequest;

        if (!searchInput || !searchForm || !alatTable || !pagination) {
            return;
        }

        const search = async () => {
            const url = new URL(searchForm.action, window.location.href);
            const params = new URLSearchParams(new FormData(searchForm));
            url.search = params.toString();

            if (activeRequest) {
                activeRequest.abort();
            }
            activeRequest = new AbortController();

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: activeRequest.signal,
                });
                if (!response.ok) {
                    throw new Error(`Pencarian gagal dengan status ${response.status}.`);
                }

                const html = await response.text();
                const document = new DOMParser().parseFromString(html, 'text/html');
                const nextTable = document.getElementById('alat-table');
                const nextPagination = document.getElementById('alat-pagination');
                const currentBody = alatTable.querySelector('tbody');
                const nextBody = nextTable && nextTable.querySelector('tbody');

                if (!currentBody || !nextBody || !nextPagination) {
                    throw new Error('Respons pencarian tidak berisi daftar alat yang valid.');
                }

                currentBody.replaceChildren(...nextBody.childNodes);
                pagination.replaceChildren(...nextPagination.childNodes);
                window.history.replaceState(null, '', url);
            } catch (error) {
                if (error.name === 'AbortError') {
                    return;
                }

                console.error(error);
                searchForm.submit();
            }
        };

        searchForm.addEventListener('submit', function (event) {
            event.preventDefault();
            clearTimeout(searchTimer);
            search();
        });

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(search, 600);
        });
    })();
</script>
@endpush
@endsection
