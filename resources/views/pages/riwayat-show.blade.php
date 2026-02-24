{{-- resources/views/pages/riwayat-show.blade.php --}}
@extends('layouts.master')

@section('title', 'Detail Riwayat Deteksi')

@section('content')
<div class="page-header">
    <h1 class="page-title">📄 Detail Riwayat Deteksi</h1>
    <p class="page-subtitle">Teks yang diuji dan ringkasan hasil deteksi</p>
</div>

{{-- Ringkasan Hasil --}}
<div class="card">
    <div class="card-header">
        <strong>📊 Ringkasan Hasil</strong>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
            <div>
                <label style="font-size: 0.85rem; color: #64748b;">Waktu Deteksi</label>
                <p style="font-weight: 600; margin: 5px 0 0 0;">{{ $result->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div>
                <label style="font-size: 0.85rem; color: #64748b;">Similarity Tertinggi</label>
                <p style="font-weight: 600; margin: 5px 0 0 0; font-size: 1.2rem; color: {{ $result->highest_similarity >= 50 ? '#dc3545' : '#198754' }};">
                    {{ number_format($result->highest_similarity, 2) }}%
                </p>
            </div>
            <div>
                <label style="font-size: 0.85rem; color: #64748b;">Status</label>
                <p style="margin: 5px 0 0 0;">
                    <span class="badge badge-{{ $result->status_color }}">
                        {{ $result->status_label }}
                    </span>
                </p>
            </div>
            <div>
                <label style="font-size: 0.85rem; color: #64748b;">Parameter</label>
                <p style="font-weight: 600; margin: 5px 0 0 0;">k={{ $result->k_gram }}, w={{ $result->window_size }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Teks yang Diuji --}}
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <strong>📝 Teks yang Diuji</strong>
    </div>
    <div class="card-body">
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
            <pre style="white-space: pre-wrap; font-family: inherit; font-size: 0.95rem; line-height: 1.6; margin: 0; color: #212529;">{{ $result->input_text }}</pre>
        </div>
    </div>
</div>

{{-- Hasil Perbandingan (jika ada) --}}
@if($result->comparison_results && count($result->comparison_results) > 0)
<div class="card" style="margin-top: 20px;">
    <div class="card-header">
        <strong>🔍 Hasil Perbandingan</strong>
    </div>
    <div class="card-body">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Judul Dokumen</th>
                        <th>Penulis</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($result->comparison_results as $idx => $comparison)
                    <tr>
                        <td>{{ $idx + 1 }}</td>
                        <td>{{ $comparison['judul'] ?? '-' }}</td>
                        <td>{{ $comparison['penulis'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Tombol Aksi --}}
<div style="margin-top: 20px; display: flex; gap: 10px;">
    <a href="{{ route('riwayat.index') }}" class="btn btn-secondary">
        ← Kembali ke Riwayat
    </a>
    <form action="{{ route('riwayat.destroy', $result->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">
            🗑️ Hapus Riwayat
        </button>
    </form>
</div>
@endsection