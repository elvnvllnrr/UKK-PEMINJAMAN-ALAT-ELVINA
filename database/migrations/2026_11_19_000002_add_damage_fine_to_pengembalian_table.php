<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Migrasi anonim yang menerapkan perubahan skema database dan pembatalannya. */
return new class extends Migration
{
    /** Mengubah tabel pengembalian sesuai kolom atau opsi yang ditambahkan migrasi. */
    public function up(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->decimal('denda_kerusakan', 12, 2)->default(0)->after('denda');
        });

        DB::table('pengembalian')->update([
            'denda_kerusakan' => DB::raw('denda'),
        ]);
    }

    /** Mengembalikan perubahan skema pada migrasi ke keadaan sebelumnya. */
    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn('denda_kerusakan');
        });
    }
};
