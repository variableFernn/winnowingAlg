{{-- resources/views/pages/home.blade.php --}}
@extends('layouts.master')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Sistem Deteksi Plagiarisme dengan Algoritma Winnowing</p>
</div>

{{-- Statistics Cards --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: #dbeafe;">📚</div>
        <div class="stat-value">{{ number_format($stats['total_dataset']) }}</div>
        <div class="stat-label">Total Data Uji</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #d1fae5;">🔍</div>
        <div class="stat-value">{{ number_format($stats['total_deteksi']) }}</div>
        <div class="stat-label">Total Deteksi</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #fef3c7;">📅</div>
        <div class="stat-value">{{ number_format($stats['deteksi_hari_ini']) }}</div>
        <div class="stat-label">Deteksi Hari Ini</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon" style="background: #fee2e2;">📊</div>
        <div class="stat-value">{{ number_format($stats['rata_rata_similarity'], 1) }}%</div>
        <div class="stat-label">Rata-rata Similarity</div>
    </div>
</div>

{{-- Quick Actions --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Aksi Cepat</h3>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
        <a href="{{ route('deteksi.index') }}" class="btn btn-primary" style="padding: 20px;">
            🔎 Mulai Deteksi Plagiarisme
        </a>
        <a href="{{ route('dataset.create') }}" class="btn btn-secondary" style="padding: 20px;">
            ➕ Tambah Data Uji Baru
        </a>
        <a href="{{ route('riwayat.index') }}" class="btn btn-secondary" style="padding: 20px;">
            📋 Lihat Riwayat Deteksi
        </a>
    </div>
</div>

{{-- Status Distribution --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Distribusi Status Deteksi</h3>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
        <div style="text-align: center; padding: 20px; background: #d1fae5; border-radius: 12px;">
            <div style="font-size: 2rem; font-weight: 700; color: #065f46;">{{ $statusStats['low'] }}</div>
            <div style="color: #065f46;">✅ Aman</div>
        </div>
        <div style="text-align: center; padding: 20px; background: #fef3c7; border-radius: 12px;">
            <div style="font-size: 2rem; font-weight: 700; color: #92400e;">{{ $statusStats['medium'] }}</div>
            <div style="color: #92400e;">⚠️ Sedang</div>
        </div>
        <div style="text-align: center; padding: 20px; background: #fee2e2; border-radius: 12px;">
            <div style="font-size: 2rem; font-weight: 700; color: #991b1b;">{{ $statusStats['high'] }}</div>
            <div style="color: #991b1b;">🚨 Tinggi</div>
        </div>
    </div>
</div>

{{-- Recent Detections --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Deteksi Terbaru</h3>
        <a href="{{ route('riwayat.index') }}" class="btn btn-sm btn-secondary">Lihat Semua</a>
    </div>
    
    @if($recentDetections->count() > 0)
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Similarity</th>
                    <th>Status</th>
                    <th>Parameter</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentDetections as $detection)
                <tr>
                    <td>{{ $detection->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <strong>{{ number_format($detection->highest_similarity, 2) }}%</strong>
                    </td>
                    <td>
                        <span class="badge badge-{{ $detection->status_color }}">
                            {{ $detection->status_label }}
                        </span>
                    </td>
                    <td>
                        k={{ $detection->k_gram }}, w={{ $detection->window_size }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📋</div>
        <h3>Belum ada deteksi</h3>
        <p>Mulai deteksi plagiarisme pertama Anda</p>
        <a href="{{ route('deteksi.index') }}" class="btn btn-primary" style="margin-top: 15px;">
            Mulai Deteksi
        </a>
    </div>
    @endif
</div>

{{-- Info Algoritma --}}
<!-- <div class="card" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: #fff;">
    <h3 style="margin-bottom: 15px;">📘 Tentang Algoritma Winnowing</h3>
    <p style="opacity: 0.9; line-height: 1.7;">
        Sistem ini menggunakan <strong>Algoritma Winnowing</strong> untuk mendeteksi kemiripan teks. 
        Algoritma ini bekerja dengan menghasilkan <em>fingerprint</em> dokumen melalui teknik 
        <strong>K-gram</strong>, <strong>Rolling Hash</strong>, dan <strong>Window Selection</strong>. 
        Kemiripan antar dokumen dihitung menggunakan <strong>Jaccard Coefficient</strong>.
    </p>
    <div style="margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
        <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-size: 0.85rem;">
            Preprocessing
        </span>
        <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-size: 0.85rem;">
            K-gram
        </span>
        <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-size: 0.85rem;">
            Rolling Hash
        </span>
        <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-size: 0.85rem;">
            Windowing
        </span>
        <span style="background: rgba(255,255,255,0.2); padding: 8px 16px; border-radius: 20px; font-size: 0.85rem;">
            Jaccard Similarity
        </span>
    </div>
</div> -->
@endsection