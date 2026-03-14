<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom period (nullable dulu)
        Schema::table('org_members', function (Blueprint $table) {
            $table->string('period')->nullable()->after('sort_order');
        });

        // 2. Isi kolom period untuk data lama
        DB::table('org_members')->update(['period' => '2025-2026']);

        // 3. Hapus unique lama pada position_key
        Schema::table('org_members', function (Blueprint $table) {
            $table->dropUnique(['position_key']);
        });

        // 4. Ubah period jadi not-null + tambah composite unique
        Schema::table('org_members', function (Blueprint $table) {
            $table->string('period')->default('2025-2026')->nullable(false)->change();
            $table->unique(['position_key', 'period'], 'org_position_period_unique');
        });
    }

    public function down(): void
    {
        Schema::table('org_members', function (Blueprint $table) {
            $table->dropUnique('org_position_period_unique');
            $table->dropColumn('period');
            $table->unique('position_key');
        });
    }
};
