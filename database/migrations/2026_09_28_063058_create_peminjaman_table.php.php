<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Migrasi anonim yang menerapkan perubahan skema database dan pembatalannya. */
return new class extends Migration
{
    /** Membuat tabel peminjaman beserta kolom utama yang digunakan aplikasi. */
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali_rencana');
            $table->enum('status', ['Menunggu Persetujuan', 'Dipinjam', 'Dikembalikan', 'Ditolak', 'Terlambat'])->default('Menunggu Persetujuan');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->onDelete('cascade');
            $table->foreignId('alat_id')->constrained('alat')->onDelete('cascade');
            $table->integer('jumlah')->default(1);
            $table->enum('kondisi_pinjam', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('kondisi_kembali', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->nullable();
            $table->timestamps();
        });

        Schema::create('pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjaman')->onDelete('cascade');
            $table->date('tanggal_kembali');
            $table->enum('status', ['Tepat Waktu', 'Terlambat', 'Rusak', 'Hilang'])->default('Tepat Waktu');
            $table->decimal('denda', 12, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /** Mengembalikan perubahan skema pada migrasi ke keadaan sebelumnya. */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
        Schema::dropIfExists('detail_peminjaman');
        Schema::dropIfExists('peminjaman');
    }
};
