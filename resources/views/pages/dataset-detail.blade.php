{{-- resources/views/pages/dataset-detail.blade.php --}}
@extends('layouts.master')

@section('title', 'Detail Dataset')

@section('content')
<div class="page-header">
    <a href="{{ route('dataset.index') }}" class="btn btn-secondary btn-sm" style="margin-bottom: 15px;">
        ← Kembali ke Daftar
    </a>
    <h1 class="page-title">📄 Detail Data Uji</h1>
</div>

{{-- Dataset Info --}}
<div class="card">
    <h2 style="color: #1e293b; margin-bottom: 15px;">{{ $dataset->judul }}</h2>
    
    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 20px;">
        @if($dataset->penulis)
        <span style="background: #f1f5f9; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
            👤 {{ $dataset->penulis }}
        </span>
        @endif
        @if($dataset->tahun)
        <span style="background: #f1f5f9; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
            📅 {{ $dataset->tahun }}
        </span>
        @endif
        @if($dataset->prodi)
        <span style="background: #f1f5f9; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem;">
            🎓 {{ $dataset->prodi }}
        </span>
        @endif
        <span style="background: #dbeafe; padding: 6px 12px; border-radius: 6px; font-size: 0.85rem; color: #1e40af;">
            🔢 {{ $dataset->fingerprint_count }} Fingerprints
        </span>
    </div>

    <div style="background: #f8fafc; padding: 20px; border-radius: 10px; border: 1px solid #e2e8f0;">
        <h4 style="color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 10px;">
            Abstrak
        </h4>
        <p style="line-height: 1.8; color: #334155; white-space: pre-line;">{{ $dataset->abstrak }}</p>
    </div>

    <div style="margin-top: 15px; font-size: 0.85rem; color: #64748b;">
        Ditambahkan pada {{ $dataset->created_at->format('d F Y, H:i') }}
    </div>
</div>

{{-- Analisis Winnowing Step by Step --}}
@isset($analysis)
<div class="card">
    <div class="card-header">
        <h3 class="card-title">🔬 Analisis Algoritma Winnowing (Step by Step)</h3>
    </div>

    {{-- Summary Stats --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
        <div style="background: #f0fdf4; padding: 15px; border-radius: 10px; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #166534;">{{ $analysis['summary']['original_length'] }}</div>
            <div style="color: #166534; font-size: 0.8rem;">Karakter Asli</div>
        </div>
        <div style="background: #eff6ff; padding: 15px; border-radius: 10px; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #1e40af;">{{ $analysis['summary']['preprocessed_length'] }}</div>
            <div style="color: #1e40af; font-size: 0.8rem;">Setelah Preprocessing</div>
        </div>
        <div style="background: #fef3c7; padding: 15px; border-radius: 10px; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #92400e;">{{ $analysis['summary']['kgrams_count'] }}</div>
            <div style="color: #92400e; font-size: 0.8rem;">K-gram</div>
        </div>
        <div style="background: #fce7f3; padding: 15px; border-radius: 10px; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #9d174d;">{{ $analysis['summary']['windows_count'] }}</div>
            <div style="color: #9d174d; font-size: 0.8rem;">Windows</div>
        </div>
        <div style="background: #f3e8ff; padding: 15px; border-radius: 10px; text-align: center;">
            <div style="font-size: 1.5rem; font-weight: 700; color: #7c3aed;">{{ $analysis['summary']['fingerprints_count'] }}</div>
            <div style="color: #7c3aed; font-size: 0.8rem;">Fingerprints</div>
        </div>
    </div>

    {{-- Step 1: Preprocessing --}}
    <div style="margin-bottom: 25px;">
        <h4 style="color: #1e293b; margin-bottom: 10px;">
            <span style="background: #3b82f6; color: #fff; padding: 4px 10px; border-radius: 15px; font-size: 0.8rem; margin-right: 10px;">1</span>
            Hasil Preprocessing
        </h4>
        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; font-family: monospace; font-size: 0.85rem; word-break: break-all; max-height: 150px; overflow-y: auto;">
            {{ $analysis['steps']['step2_preprocessing']['data'] ?? 'N/A' }}
        </div>
        <p style="color: #64748b; font-size: 0.8rem; margin-top: 5px;">
            {{ $analysis['steps']['step2_preprocessing']['description'] ?? '' }}
        </p>
    </div>

    {{-- Step 2: K-grams --}}
    <div style="margin-bottom: 25px;">
        <h4 style="color: #1e293b; margin-bottom: 10px;">
            <span style="background: #10b981; color: #fff; padding: 4px 10px; border-radius: 15px; font-size: 0.8rem; margin-right: 10px;">2</span>
            K-gram ({{ $analysis['steps']['step3_kgrams']['parameter'] ?? '' }})
        </h4>
        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; display: flex; flex-wrap: wrap; gap: 8px; max-height: 150px; overflow-y: auto;">
            @foreach($analysis['steps']['step3_kgrams']['data'] ?? [] as $kgram)
            <span style="background: #d1fae5; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 0.8rem;">
                {{ $kgram }}
            </span>
            @endforeach
        </div>
        <p style="color: #64748b; font-size: 0.8rem; margin-top: 5px;">
            Total: {{ $analysis['steps']['step3_kgrams']['total'] ?? 0 }} k-gram
            {{ $analysis['steps']['step3_kgrams']['sample'] ?? '' }}
        </p>
    </div>

    {{-- Step 3: Hashes --}}
    <div style="margin-bottom: 25px;">
        <h4 style="color: #1e293b; margin-bottom: 10px;">
            <span style="background: #f59e0b; color: #fff; padding: 4px 10px; border-radius: 15px; font-size: 0.8rem; margin-right: 10px;">3</span>
            Rolling Hash Values
        </h4>
        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; display: flex; flex-wrap: wrap; gap: 8px; max-height: 150px; overflow-y: auto;">
            @foreach($analysis['steps']['step4_hashes']['data'] ?? [] as $hash)
            <span style="background: #fef3c7; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 0.75rem;">
                {{ $hash }}
            </span>
            @endforeach
        </div>
        <p style="color: #64748b; font-size: 0.8rem; margin-top: 5px;">
            Formula: {{ $analysis['steps']['step4_hashes']['formula'] ?? 'H = Σ(char × base^position) mod M' }}
        </p>
    </div>

    {{-- Step 4: Fingerprints --}}
    <div>
        <h4 style="color: #1e293b; margin-bottom: 10px;">
            <span style="background: #8b5cf6; color: #fff; padding: 4px 10px; border-radius: 15px; font-size: 0.8rem; margin-right: 10px;">4</span>
            Fingerprints (Nilai Hash Terkecil per Window)
        </h4>
        <div style="background: #f8fafc; padding: 15px; border-radius: 8px; display: flex; flex-wrap: wrap; gap: 8px;">
            @foreach($analysis['steps']['step6_fingerprints']['data'] ?? [] as $fp)
            <span style="background: #f3e8ff; padding: 6px 12px; border-radius: 6px; font-family: monospace; font-size: 0.85rem; font-weight: 600;">
                {{ $fp }}
            </span>
            @endforeach
        </div>
        <p style="color: #64748b; font-size: 0.8rem; margin-top: 5px;">
            {{ $analysis['steps']['step6_fingerprints']['description'] ?? '' }}
        </p>
    </div>
</div>
@endisset

{{-- Actions --}}
<div class="card">
    <h4 style="margin-bottom: 15px;">Aksi</h4>
    <div style="display: flex; gap: 15px; flex-wrap: wrap;">
        <a href="{{ route('dataset.index') }}" class="btn btn-secondary">
            ← Kembali ke Daftar
        </a>
        <form action="{{ route('dataset.destroy', $dataset->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus dataset ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                🗑️ Hapus Dataset
            </button>
        </form>
    </div>
</div>
@endsection