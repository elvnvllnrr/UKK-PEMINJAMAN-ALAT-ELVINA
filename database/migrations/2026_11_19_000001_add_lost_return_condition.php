<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Migrasi anonim yang menerapkan perubahan skema database dan pembatalannya. */
return new class extends Migration
{
    /** Mengubah tabel detail_peminjaman sesuai kolom atau opsi yang ditambahkan migrasi. */
    public function up(): void
    {
        Schema::table('detail_peminjaman', function (Blueprint $table) {
            $table->enum('kondisi_kembali', ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'])
                ->nullable()
                ->change();
        });
    }

    /** Mengembalikan perubahan skema pada migrasi ke keadaan sebelumnya. */
    public function down(): void
    {
        Schema::table('detail_peminjaman', function (Blueprint $table) {
            $table->enum('kondisi_kembali', ['Baik', 'Rusak Ringan', 'Rusak Berat'])
                ->nullable()
                ->change();
        });
    }
};
