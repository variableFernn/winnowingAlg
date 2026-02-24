<?php
// database/migrations/2025_01_01_000001_create_datasets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datasets', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 500);
            $table->text('abstrak');
            $table->text('abstrak_preprocessed')->nullable(); // Hasil preprocessing
            $table->json('fingerprints')->nullable();          // Fingerprint hasil winnowing
            $table->integer('fingerprint_count')->default(0);  // Jumlah fingerprint
            $table->string('tahun', 4)->nullable();
            $table->string('penulis', 255)->nullable();
            $table->string('prodi', 100)->nullable();
            $table->timestamps();
            
            // Index untuk pencarian
            $table->index('judul');
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datasets');
    }
};