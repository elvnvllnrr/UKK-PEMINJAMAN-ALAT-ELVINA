{{-- Gunakan kerangka halaman aplikasi yang menjadi pembungkus tampilan ini. --}}
@extends('layouts.app')

{{-- Tetapkan judul halaman pada tab atau jendela browser. --}}
@section('title', 'Edit Pengembalian')

{{-- Tampilkan judul utama untuk bagian perubahan data pengembalian. --}}
@section('page-title', 'Edit Pengembalian')

{{-- Isi utama tampilan: perubahan data pengembalian. --}}
@section('content')
@php
    $tanggalKembaliDefault = old('tanggal_kembali', $pengembalian->tanggal_kembali);
    $tarifDendaTerlambat = (int) config('peminjaman.denda_keterlambatan_per_hari');
    $hariTerlambatDefault = $pengembalian->peminjaman->hitungHariTerlambat($tanggalKembaliDefault);
    $dendaTerlambatDefault = $hariTerlambatDefault * $tarifDendaTerlambat;
    $dendaKerusakanDefault = (float) old('denda_kerusakan', $pengembalian->denda_kerusakan);
@endphp

<div class="card">
    <div class="card-header">
        <i class="fas fa-edit me-2"></i>Ubah data pengembalian
    </div>
    <div class="card-body">

{{-- Tampilkan ringkasan kesalahan setelah validasi formulir gagal. --}}
        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <ul class="mb-0">

{{-- Ulangi $error dari $errors->all() untuk membangun isi berulang pada perubahan data pengembalian. --}}
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

{{-- Form untuk menyimpan perubahan pada perubahan data pengembalian. --}}
        <form action="{{ route('admin.pengembalian.update', $pengembalian) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Peminjaman</label>
                    <input type="text" class="form-control" value="{{ $pengembalian->peminjaman->id }} - {{ $pengembalian->peminjaman->user->name ?? '-' }}" readonly>
                </div>
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
                </div>
            </div>

            <div
                class="alert alert-warning mt-3"
                id="late-fine-summary"
                data-due-date="{{ $pengembalian->peminjaman->tanggal_kembali_rencana }}"
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

            <div class="mb-3">
                <label for="denda-kerusakan" class="form-label fw-semibold">Denda kerusakan/kehilangan (Rp)</label>
                <input
                    id="denda-kerusakan"
                    type="number"
                    name="denda_kerusakan"
                    class="form-control"
                    value="{{ $dendaKerusakanDefault }}"
                    min="0"
                    max="9999999999.99"
                    step="1"
                >
                <small class="text-muted">Denda ini ditentukan manual dan ditambahkan ke denda keterlambatan otomatis.</small>
            </div>

            <div class="mb-3">
                <label for="catatan" class="form-label fw-semibold">Catatan <span class="text-muted fw-normal">(wajib jika ada denda kerusakan)</span></label>
                <textarea id="catatan" name="catatan" class="form-control" rows="3">{{ old('catatan', $pengembalian->catatan) }}</textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Simpan perubahan
                </button>
                <a href="{{ route('admin.pengembalian.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

{{-- Tambahkan aset atau skrip ke stack layout agar dimuat pada posisi yang ditentukan. --}}
@push('scripts')

{{-- Skrip klien memperbarui ringkasan denda dan jumlah hari keterlambatan saat tanggal kembali diubah. --}}
<script>
    (() => {
        const returnDateInput = document.getElementById('tanggal-kembali');
        const lateFineSummary = document.getElementById('late-fine-summary');
        const lateDaysLabel = document.getElementById('late-days-label');
        const lateFineAmount = document.getElementById('late-fine-amount');
        const totalFineAmount = document.getElementById('total-fine-amount');
        const damageFineInput = document.getElementById('denda-kerusakan');
        const dueDate = lateFineSummary.dataset.dueDate;
        const dailyRate = Number(lateFineSummary.dataset.dailyRate);
        const currency = new Intl.NumberFormat('id-ID');

        const updateFineEstimate = () => {
            const [dueYear, dueMonth, dueDay] = dueDate.split('-').map(Number);
            const [returnYear, returnMonth, returnDay] = returnDateInput.value.split('-').map(Number);
            const dueTime = Date.UTC(dueYear, dueMonth - 1, dueDay);
            const returnTime = Date.UTC(returnYear, returnMonth - 1, returnDay);
            const lateDays = Math.max(0, Math.floor((returnTime - dueTime) / 86400000));
            const lateFine = lateDays * dailyRate;

            lateDaysLabel.textContent = lateDays > 0 ? `${lateDays} hari terlambat` : 'Tidak terlambat';
            lateFineAmount.textContent = currency.format(lateFine);
            totalFineAmount.textContent = currency.format(lateFine + Number(damageFineInput.value || 0));
        };

        returnDateInput.addEventListener('change', updateFineEstimate);
        damageFineInput.addEventListener('input', updateFineEstimate);
        updateFineEstimate();
    })();
</script>
@endpush
