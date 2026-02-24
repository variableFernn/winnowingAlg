<?php
// app/Models/Dataset.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dataset extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'abstrak',
        'abstrak_preprocessed',
        'fingerprints',
        'fingerprint_count',
        'tahun',
        'penulis',
        'prodi'
    ];

    protected $casts = [
        'fingerprints' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope untuk pencarian
     */
    public function scopeSearch($query, $keyword)
    {
        return $query->where('judul', 'like', "%{$keyword}%")
                     ->orWhere('penulis', 'like', "%{$keyword}%");
    }

    /**
     * Scope filter berdasarkan tahun
     */
    public function scopeByYear($query, $year)
    {
        if ($year) {
            return $query->where('tahun', $year);
        }
        return $query;
    }

    /**
     * Accessor untuk mendapatkan abstrak yang dipotong
     */
    public function getAbstrakShortAttribute()
    {
        return strlen($this->abstrak) > 200 
            ? substr($this->abstrak, 0, 200) . '...' 
            : $this->abstrak;
    }

    /**
     * Check apakah sudah memiliki fingerprint
     */
    public function hasFingerprints(): bool
    {
        return !empty($this->fingerprints) && is_array($this->fingerprints);
    }
}