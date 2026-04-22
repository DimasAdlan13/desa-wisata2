<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom province dan city ke tabel users.
     * Kolom nullable agar data user yang sudah ada tidak terpengaruh.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('province', 100)->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('province');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['province', 'city']);
        });
    }
};
