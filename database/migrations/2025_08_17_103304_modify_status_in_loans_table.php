<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Mengubah kolom status untuk menambahkan pilihan 'terlambat'
            $table->enum('status', ['borrowed', 'returned', 'overdue'])->default('borrowed')->change();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // Definisi untuk mengembalikan jika migrasi di-rollback
            $table->enum('status', ['borrowed', 'returned'])->default('borrowed')->change();
        });
    }
};
