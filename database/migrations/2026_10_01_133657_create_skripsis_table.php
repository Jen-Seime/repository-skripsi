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
        Schema::create('skripsi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mahasiswa_id')->constrained('mahasiswa')->cascadeOnDelete();
            $table->foreignId('program_studi_id')->constrained('program_studi')->restrictOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->string('judul', 255);
            $table->text('abstrak');
            $table->string('kata_kunci', 255);
            $table->year('tahun');
            $table->string('status', 30)->default('menunggu');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'tahun']);
            $table->index(['program_studi_id', 'status']);
            $table->index(['kategori_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skripsi');
    }
};
