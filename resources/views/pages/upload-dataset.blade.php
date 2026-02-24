{{-- resources/views/pages/upload-dataset.blade.php --}}
@extends('layouts.master')

@section('title', 'Tambah Dataset')

@section('content')
<div class="page-header">
    <h1 class="page-title">➕ Tambah Data Uji Baru</h1>
    <p class="page-subtitle">Tambahkan abstrak tugas akhir sebagai data pembanding</p>
</div>

<div class="card">
    <form method="POST" action="{{ route('dataset.store') }}">
        @csrf

        <div class="form-group">
            <label class="form-label">📌 Judul Penelitian *</label>
            <input 
                type="text" 
                name="judul" 
                class="form-input" 
                placeholder="Masukkan judul tugas akhir..."
                value="{{ old('judul') }}"
                required
            >
            @error('judul')
            <p style="color: #dc2626; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <div class="form-group">
                <label class="form-label">👤 Penulis</label>
                <input 
                    type="text" 
                    name="penulis" 
                    class="form-input" 
                    placeholder="Nama penulis"
                    value="{{ old('penulis') }}"
                >
            </div>
            <div class="form-group">
                <label class="form-label">📅 Tahun</label>
                <input 
                    type="text" 
                    name="tahun" 
                    class="form-input" 
                    placeholder="2024"
                    maxlength="4"
                    value="{{ old('tahun') }}"
                >
            </div>
            <div class="form-group">
                <label class="form-label">🎓 Program Studi</label>
                <input 
                    type="text" 
                    name="prodi" 
                    class="form-input" 
                    placeholder="TRPL / MI / TK"
                    value="{{ old('prodi') }}"
                >
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">📝 Abstrak *</label>
            <textarea 
                name="abstrak" 
                class="form-textarea" 
                rows="10"
                placeholder="Masukkan teks abstrak tugas akhir di sini..."
                required
            >{{ old('abstrak') }}</textarea>
            <p class="form-hint">Minimal 50 karakter. Abstrak akan diproses untuk menghasilkan fingerprint.</p>
            @error('abstrak')
            <p style="color: #dc2626; font-size: 0.85rem; margin-top: 5px;">{{ $message }}</p>
            @enderror
        </div>

        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <button type="submit" class="btn btn-primary">
                💾 Simpan Data Uji
            </button>
            <a href="{{ route('dataset.index') }}" class="btn btn-secondary">
                ← Kembali
            </a>
        </div>
    </form>
</div>

{{-- Info --}}
<div class="card" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
    <h4 style="color: #166534; margin-bottom: 10px;">💡 Tips</h4>
    <ul style="color: #166534; font-size: 0.9rem; line-height: 1.8; padding-left: 20px;">
        <li>Pastikan abstrak yang dimasukkan adalah teks asli, bukan hasil scan/gambar</li>
        <li>Fingerprint akan otomatis digenerate menggunakan algoritma Winnowing</li>
        <li>Semakin banyak data uji, semakin akurat hasil deteksi plagiarisme</li>
    </ul>
</div>
@endsection