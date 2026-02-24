{{-- resources/views/pages/dataset.blade.php --}}
@extends('layouts.master')

@section('title', 'Dataset')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <div>
        <h1 class="page-title">📚 Data Uji Abstrak Tugas Akhir</h1>
        <p class="page-subtitle">Kelola data uji abstrak untuk perbandingan plagiarisme</p>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('dataset.create') }}" class="btn btn-primary">
            ➕ Tambah Data Uji
        </a>
        <form action="{{ route('dataset.regenerate') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-secondary" onclick="return confirm('Regenerate fingerprint untuk semua dataset?')">
                🔄 Regenerate Fingerprints
            </button>
        </form>
    </div>
</div>

{{-- Alert Messages --}}
@if(session('success'))
<div class="alert alert-success">
    ✅ {{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error">
    ❌ {{ session('error') }}
</div>
@endif

{{-- Filter & Search --}}
<div class="card">
    <form method="GET" action="{{ route('dataset.index') }}" style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
        <div style="flex: 1; min-width: 200px;">
            <label class="form-label">Cari</label>
            <input type="text" name="search" class="form-input" placeholder="Cari judul atau penulis..." value="{{ request('search') }}">
        </div>
        <div style="min-width: 150px;">
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-select">
                <option value="">Semua Tahun</option>
                @foreach($years as $year)
                <option value="{{ $year }}" {{ request('tahun') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">🔍 Filter</button>
        <a href="{{ route('dataset.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

{{-- Dataset Table --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Data Uji ({{ $datasets->total() }} data)</h3>
    </div>

    @if($datasets->count() > 0)
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th width="50">#</th>
                    <th>Judul</th>
                    <th width="120">Penulis</th>
                    <th width="80">Tahun</th>
                    <th width="100">Fingerprints</th>
                    <th width="120">Tanggal</th>
                    <th width="100">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datasets as $index => $dataset)
                <tr>
                    <td>{{ $datasets->firstItem() + $index }}</td>
                    <td>
                        <a href="{{ route('dataset.show', $dataset->id) }}" style="color: #2563eb; text-decoration: none; font-weight: 500;">
                            {{ Str::limit($dataset->judul, 60) }}
                        </a>
                    </td>
                    <td>{{ $dataset->penulis ?? '-' }}</td>
                    <td>{{ $dataset->tahun ?? '-' }}</td>
                    <td>
                        @if($dataset->hasFingerprints())
                        <span class="badge badge-success">{{ $dataset->fingerprint_count }} ✓</span>
                        @else
                        <span class="badge badge-warning">Belum ada</span>
                        @endif
                    </td>
                    <td>{{ $dataset->created_at->format('d M Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('dataset.show', $dataset->id) }}" class="btn btn-sm btn-secondary" title="Detail">
                                👁️
                            </a>
                            <form action="{{ route('dataset.destroy', $dataset->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus dataset ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="margin-top: 20px;">
        {{ $datasets->appends(request()->query())->links() }}
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-icon">📚</div>
        <h3>Belum ada data uji</h3>
        <p>Tambahkan data uji abstrak untuk memulai deteksi plagiarisme</p>
        <a href="{{ route('dataset.create') }}" class="btn btn-primary" style="margin-top: 15px;">
            ➕ Tambah Data Uji Pertama
        </a>
    </div>
    @endif
</div>
@endsection