<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berita', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('image')->nullable();      // thumbnail berita
            $table->string('date')->nullable();
            $table->string('location')->nullable();
            $table->string('author')->nullable();
            $table->json('paragraphs')->nullable();   // array paragraf konten
            $table->boolean('is_featured')->default(false); // tampil di hero
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('likes')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('berita');
    }
};
