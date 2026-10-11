<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasal', function (Blueprint $table) {
            $table->id();
            $table->string('jenis', 20)->index();
            $table->string('kode', 20)->unique();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('kategori')->index();
            $table->unsignedSmallInteger('bulan')->default(0);
            $table->unsignedSmallInteger('hari')->default(0);
            $table->unsignedInteger('denda')->default(0);
            $table->unsignedInteger('urutan')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasal');
    }
};
