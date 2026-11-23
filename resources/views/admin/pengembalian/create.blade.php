{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Konfirmasi Pengembalian')

{{-- Tampilkan judul utama untuk bagian pencatatan pengembalian alat. --}}
@section('page-title', 'Konfirmasi Pengembalian')

{{-- Isi utama tampilan: pencatatan pengembalian alat. --}}
@section('content')
@php
    $tanggalKembaliDefault = old('tanggal_kembali', today()->toDateString());
    $tarifDendaTerlambat = (int) config('peminjaman.denda_keterlambatan_per_hari');
    $hariTerlambatDefault = $peminjaman->hitungHariTerlambat($tanggalKembaliDefault);
    $dendaTerlambatDefault = $hariTerlambatDefault * $tarifDendaTerlambat;
    $adaKerusakan = $peminjaman->detailPeminjaman->contains(
        fn ($detail) => old("detail.{$detail->id}.kondisi_kembali", $detail->kondisi_kembali ?? 'Baik') !== 'Baik'
    );
    $dendaKerusakanDefault = $adaKerusakan ? (float) old('denda_kerusakan', 0) : 0;
@endphp

<div class="mb-4">
    <h1 class="h3 fw-bold mb-1">Konfirmasi pengembalian</h1>
    <p class="text-muted mb-0">Periksa kondisi barang, lalu simpan. Keterlambatan dihitung otomatis.</p>
</div>

{{-- Tampilkan ringkasan kesalahan setelah validasi formulir gagal. --}}
@if($errors->any())
    <div class="alert alert-danger" role="alert">
        <div class="fw-semibold mb-1">Periksa kembali isian berikut:</div>
        <ul class="mb-0">

{{-- Ulangi $error dari $errors->all() untuk membangun isi berulang pada pencatatan pengembalian alat. --}}
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card mb-3">
    <div class="card-body py-3">
        <div class="row g-3">
            <div class="col-sm-4">
                <small class="text-muted d-block">Peminjaman</small>
                <span class="fw-semibold">#{{ $peminjaman->id }} · {{ $peminjaman->user->name ?? '-' }}</span>
            </div>
            <div class="col-sm-4">
                <small class="text-muted d-block">Tanggal pinjam</small>
                <span class="fw-semibold">{{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d-m-Y') }}</span>
            </div>
            <div class="col-sm-4">
                <small class="text-muted d-block">Batas pengembalian</small>
                <span class="fw-semibold">{{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali_rencana)->format('d-m-Y') }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Form untuk menyimpan data baru pada pencatatan pengembalian alat. --}}
<form action="{{ route('petugas.pengembalian.store') }}" method="POST">
    @csrf
    <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">

    <div class="card">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                <div>
                    <h2 class="h5 fw-bold mb-1">Barang yang dikembalikan</h2>
                    <p class="small text-muted mb-0">Pilih kondisi setiap jenis alat.</p>
                </div>
                <span class="badge bg-light text-dark">{{ $peminjaman->detailPeminjaman->count() }} jenis alat</span>
            </div>

            <div class="alert alert-info py-2 mb-3">
                Hanya barang berkondisi <strong>Baik</strong> yang kembali menjadi stok tersedia.
            </div>

            <div class="table-responsive">

{{-- Tabel ringkasan data pada pencatatan pengembalian alat. --}}
                <table class="table align-middle mb-0">

{{-- Header kolom tabel. --}}
                    <thead>
                        <tr>
                            <th scope="col">Alat</th>
                            <th scope="col" class="text-center">Jumlah</th>
                            <th scope="col">Kondisi saat kembali</th>
                        </tr>
                    </thead>

{{-- Baris tabel hasil data pada pencatatan pengembalian alat. --}}
                    <tbody>

{{-- Ulangi $detail dari $peminjaman->detailPeminjaman untuk membangun isi berulang pada pencatatan pengembalian alat. --}}
                        @foreach($peminjaman->detailPeminjaman as $detail)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $detail->alat->nama_alat ?? 'Alat tidak tersedia' }}</div>
                                    <small class="text-muted">{{ $detail->alat->kode_alat ?? '' }}</small>
                                </td>
                                <td class="text-center">{{ $detail->jumlah }}</td>
                                <td>
                                    <label for="kondisi-{{ $detail->id }}" class="visually-hidden">Kondisi {{ $detail->alat->nama_alat ?? 'alat' }}</label>
                                    <select
                                        id="kondisi-{{ $detail->id }}"
                                        name="detail[{{ $detail->id }}][kondisi_kembali]"
                                        class="form-select"
                                        data-return-condition
                                        required
                                    >
                                        <option value="Baik" {{ old("detail.{$detail->id}.kondisi_kembali", $detail->kondisi_kembali ?? 'Baik') === 'Baik' ? 'selected' : '' }}>Baik</option>
                                        <option value="Rusak Ringan" {{ old("detail.{$detail->id}.kondisi_kembali", $detail->kondisi_kembali) === 'Rusak Ringan' ? 'selected' : '' }}>Rusak ringan</option>
                                        <option value="Rusak Berat" {{ old("detail.{$detail->id}.kondisi_kembali", $detail->kondisi_kembali) === 'Rusak Berat' ? 'selected' : '' }}>Rusak berat</option>
                                        <option value="Hilang" {{ old("detail.{$detail->id}.kondisi_kembali", $detail->kondisi_kembali) === 'Hilang' ? 'selected' : '' }}>Hilang</option>
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <hr class="my-4">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="tanggal-kembali" class="form-label fw-semibold">Tanggal barang diterima</label>
                    <input
                        id="tanggal-kembali"
                        type="date"
                        name="tanggal_kembali"
                        class="form-control"
                        value="{{ $tanggalKembaliDefault }}"
                        max="{{ today()->toDateString() }}"
                        required
                    >
                    <small class="text-muted">Pilih tanggal penerimaan sebenarnya jika baru dicatat hari ini.</small>
                </div>

                <div class="col-12">
                    <div
                        class="alert alert-warning mb-0"
                        id="late-fine-summary"
                        data-due-date="{{ $peminjaman->tanggal_kembali_rencana }}"
                        data-daily-rate="{{ $tarifDendaTerlambat }}"
                    >
                        <div class="d-flex flex-wrap justify-content-between gap-2">
                            <div>
                                <strong id="late-days-label">
                                    {{ $hariTerlambatDefault > 0 ? $hariTerlambatDefault.' hari terlambat' : 'Tidak terlambat' }}
                                </strong>
                                <div class="small">Tarif Rp {{ number_format($tarifDendaTerlambat, 0, ',', '.') }} per hari per peminjaman</div>
                            </div>
                            <div class="fw-bold">Denda keterlambatan: Rp <span id="late-fine-amount">{{ number_format($dendaTerlambatDefault, 0, ',', '.') }}</span></div>
                        </div>
                        <div class="border-top mt-2 pt-2 fw-semibold">
                            Total estimasi denda: Rp <span id="total-fine-amount">{{ number_format($dendaTerlambatDefault + $dendaKerusakanDefault, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="col-12" id="kerusakan-fields" @if(!$adaKerusakan) hidden @endif>
                    <div class="rounded-3 border bg-light p-3">
                        <h3 class="h6 fw-bold mb-1">Catatan kerusakan atau kehilangan</h3>
                        <p class="small text-muted mb-3">Denda kerusakan/kehilangan ditentukan petugas dan ditambahkan ke denda keterlambatan otomatis.</p>

                        <div class="row g-3">
                            <div class="col-md-5">
                                <label for="denda-kerusakan" class="form-label">Denda kerusakan/kehilangan (Rp)</label>
                                <input
                                    id="denda-kerusakan"
                                    type="number"
                                    name="denda_kerusakan"
                                    class="form-control"
                                    value="{{ old('denda_kerusakan', 0) }}"
                                    min="0"
                                    max="9999999999.99"
                                    step="1"
                                    inputmode="numeric"

{{-- Tampilkan blok ini hanya jika kondisi (!$adaKerusakan) terpenuhi. --}}
                                    @if(!$adaKerusakan) disabled @endif
                                >
                            </div>
                            <div class="col-md-7">
                                <label for="catatan" class="form-label">Keterangan <span class="text-muted">(wajib jika ada denda kerusakan)</span></label>
                                <textarea
                                    id="catatan"
                                    name="catatan"
                                    class="form-control"
                                    rows="2"
                                    maxlength="2000"
                                    placeholder="Jelaskan kondisi barang atau alasan denda kerusakan"
                                >{{ old('catatan') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-check me-2"></i>Konfirmasi pengembalian
                </button>
                <a href="{{ auth()->user()->role === 'petugas' ? route('petugas.peminjaman.index') : route('admin.peminjaman.index') }}" class="btn btn-outline-secondary">
                    Batal
                </a>
            </div>
        </div>
    </div>
</form>
@endsection

{{-- Tambahkan aset atau skrip ke stack layout agar dimuat pada posisi yang ditentukan. --}}
@push('scripts')

{{-- Skrip klien menghitung denda dan menampilkan bidang kerusakan sesuai kondisi pengembalian. --}}
<script>
    (() => {
        const conditions = document.querySelectorAll('[data-return-condition]');
        const damageFields = document.getElementById('kerusakan-fields');
        const returnDateInput = document.getElementById('tanggal-kembali');
        const lateFineSummary = document.getElementById('late-fine-summary');
        const lateDaysLabel = document.getElementById('late-days-label');
        const lateFineAmount = document.getElementById('late-fine-amount');
        const totalFineAmount = document.getElementById('total-fine-amount');
        const damageFineInput = document.getElementById('denda-kerusakan');
        const noteInput = document.getElementById('catatan');
        const dueDate = lateFineSummary.dataset.dueDate;
        const dailyRate = Number(lateFineSummary.dataset.dailyRate);
        const currency = new Intl.NumberFormat('id-ID');

        const updateDamageFields = () => {
            const hasDamage = Array.from(conditions).some((condition) => condition.value !== 'Baik');
            damageFields.hidden = !hasDamage;
            damageFineInput.disabled = !hasDamage;
            noteInput.disabled = !hasDamage;
            noteInput.required = hasDamage && Number(damageFineInput.value) > 0;

            const [dueYear, dueMonth, dueDay] = dueDate.split('-').map(Number);
            const [returnYear, returnMonth, returnDay] = returnDateInput.value.split('-').map(Number);
            const dueTime = Date.UTC(dueYear, dueMonth - 1, dueDay);
            const returnTime = Date.UTC(returnYear, returnMonth - 1, returnDay);
            const lateDays = Math.max(0, Math.floor((returnTime - dueTime) / 86400000));
            const lateFine = lateDays * dailyRate;
            const damageFine = hasDamage ? Number(damageFineInput.value || 0) : 0;

            lateDaysLabel.textContent = lateDays > 0 ? `${lateDays} hari terlambat` : 'Tidak terlambat';
            lateFineAmount.textContent = currency.format(lateFine);
            totalFineAmount.textContent = currency.format(lateFine + damageFine);
        };

        conditions.forEach((condition) => condition.addEventListener('change', updateDamageFields));
        returnDateInput.addEventListener('change', updateDamageFields);
        damageFineInput.addEventListener('input', updateDamageFields);
        updateDamageFields();
    })();
</script>
@endpush
