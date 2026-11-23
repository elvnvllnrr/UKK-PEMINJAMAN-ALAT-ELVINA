@props(['messages'])

{{-- Tampilkan blok ini hanya jika kondisi ($messages) terpenuhi. --}}
@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>

{{-- Ulangi $message dari (array) $messages untuk membangun isi berulang pada komponen antarmuka yang dapat digunakan kembali. --}}
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
