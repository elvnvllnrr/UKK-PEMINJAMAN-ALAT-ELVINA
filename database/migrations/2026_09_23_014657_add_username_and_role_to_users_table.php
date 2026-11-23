<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Migrasi anonim yang menerapkan perubahan skema database dan pembatalannya. */
return new class extends Migration
{
    /** Mengubah tabel users sesuai kolom atau opsi yang ditambahkan migrasi. */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('email');
            $table->string('role')->default('peminjam')->after('username');
        });
    }

    /** Mengembalikan perubahan skema pada migrasi ke keadaan sebelumnya. */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role']);
        });
    }
};