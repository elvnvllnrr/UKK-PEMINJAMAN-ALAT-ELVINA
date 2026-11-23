<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Migrasi anonim yang menerapkan perubahan skema database dan pembatalannya. */
return new class extends Migration
{
    /** Mengubah tabel peminjaman sesuai kolom atau opsi yang ditambahkan migrasi. */
    public function up(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->timestamp('pengembalian_diajukan_at')->nullable();
        });
    }

    /** Mengembalikan perubahan skema pada migrasi ke keadaan sebelumnya. */
    public function down(): void
    {
        Schema::table('peminjaman', function (Blueprint $table) {
            $table->dropColumn('pengembalian_diajukan_at');
        });
    }
};
