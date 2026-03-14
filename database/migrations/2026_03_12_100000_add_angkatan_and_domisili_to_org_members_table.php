<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('org_members', function (Blueprint $table) {
            $table->unsignedInteger('angkatan')->default(24)->after('period');
            $table->string('domisili')->nullable()->after('name');
        });

        $members = DB::table('org_members')->select('id', 'period')->get();
        foreach ($members as $member) {
            $angkatan = 1;
            if (preg_match('/^(\d{4})-(\d{4})$/', (string) $member->period, $m)) {
                $angkatan = max(1, ((int) $m[1]) - 2002 + 1);
            }

            DB::table('org_members')
                ->where('id', $member->id)
                ->update(['angkatan' => $angkatan]);
        }
    }

    public function down(): void
    {
        Schema::table('org_members', function (Blueprint $table) {
            $table->dropColumn(['angkatan', 'domisili']);
        });
    }
};
