<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // NIS harus unik dan ditempatkan setelah 'id'
            $table->string('nis')->unique()->after('id');
            // Kolom untuk menyimpan path foto, bisa kosong
            $table->string('photo')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['nis', 'photo']);
        });
    }
};
