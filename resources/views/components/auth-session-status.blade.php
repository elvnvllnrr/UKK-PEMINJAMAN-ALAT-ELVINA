@props(['status'])

{{-- Tampilkan tampilan berdasarkan status peminjaman atau pengembalian. --}}
@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
        {{ $status }}
    </div>
@endif
