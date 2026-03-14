<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('image')->nullable();       // thumbnail utama
            $table->string('date')->nullable();        // tanggal tampil (string bebas)
            $table->string('location')->nullable();
            $table->string('author')->nullable();
            $table->string('status')->default('selesai'); // selesai | berlangsung | akan-datang
            $table->integer('month')->nullable();
            $table->integer('year')->nullable();
            $table->text('intro')->nullable();
            $table->json('paragraphs')->nullable();    // array paragraf konten
            $table->boolean('has_report')->default(false);
            $table->string('report_file')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
