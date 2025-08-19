<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Hapus kolom 'class' yang lama
            $table->dropColumn('class');
            // Tambahkan kolom foreign key baru, bisa null
            $table->foreignId('school_class_id')->nullable()->constrained()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['school_class_id']);
            $table->dropColumn('school_class_id');
            $table->string('class')->nullable()->after('name');
        });
    }
};
