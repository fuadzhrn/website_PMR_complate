<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('org_members', function (Blueprint $table) {
            $table->id();
            $table->string('position_key')->unique(); // ketua-umum, sekretaris, dll.
            $table->string('title');                 // Ketua Umum, Sekretaris, dll.
            $table->string('name');                  // Nama pemegang jabatan
            $table->string('photo')->nullable();     // path ke foto
            $table->string('role_group');            // pengurus / staf
            $table->string('parent_key')->nullable(); // untuk staf, merujuk ke key jabatan induk
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('org_members');
    }
};
