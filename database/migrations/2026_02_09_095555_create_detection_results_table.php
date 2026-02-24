<?php
// database/migrations/2025_01_01_000002_create_detection_results_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detection_results', function (Blueprint $table) {
            $table->id();
            $table->text('input_text');                        // Teks yang diuji
            $table->text('input_preprocessed')->nullable();    // Hasil preprocessing
            $table->json('input_fingerprints')->nullable();    // Fingerprint input
            $table->decimal('highest_similarity', 5, 2)->default(0);
            $table->json('comparison_results')->nullable();    // Detail hasil perbandingan
            $table->integer('k_gram')->default(5);             // Parameter k-gram
            $table->integer('window_size')->default(4);        // Parameter window
            $table->string('status', 50)->nullable();          // low/medium/high
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detection_results');
    }
};