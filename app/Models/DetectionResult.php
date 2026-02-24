<?php
// app/Models/DetectionResult.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetectionResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'input_text',
        'input_preprocessed',
        'input_fingerprints',
        'highest_similarity',
        'comparison_results',
        'k_gram',
        'window_size',
        'status',
        'ip_address'
    ];

    protected $casts = [
        'input_fingerprints' => 'array',
        'comparison_results' => 'array',
        'highest_similarity' => 'decimal:2',
    ];

    /**
     * Tentukan status berdasarkan similarity
     */
    public static function determineStatus(float $similarity): string
    {
        if ($similarity < 25) {
            return 'low';
        } elseif ($similarity < 50) {
            return 'medium';
        } else {
            return 'high';
        }
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'low' => 'Aman (Kemiripan Rendah)',
            'medium' => 'Perlu Tinjauan (Sedang)',
            'high' => 'Terindikasi Plagiarisme (Tinggi)',
            default => 'Tidak Diketahui'
        };
    }

    /**
     * Get status color class
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'low' => 'green',
            'medium' => 'orange',
            'high' => 'red',
            default => 'gray'
        };
    }
}