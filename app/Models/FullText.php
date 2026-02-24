<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FullText extends Model
{
    use HasFactory;

    protected $table = 'detection_results';

    protected $fillable = [
        'input_text',
        'input_preprocessed',
        'input_fingerprints',
        'highest_similarity',
        'comparison_results',
        'k_gram',
        'window_size',
        'status',
        'ip_address',
    ];

    protected $casts = [
        'input_fingerprints' => 'array',
        'comparison_results' => 'array',
        'highest_similarity' => 'decimal:2',
    ];

    // Helper untuk status label
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'low' => 'Aman',
            'medium' => 'Sedang',
            'high' => 'Tinggi',
            default => $this->status,
        };
    }

    // Helper untuk status color (badge)
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            default => 'secondary',
        };
    }
}