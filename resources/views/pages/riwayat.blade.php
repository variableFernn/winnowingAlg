{{-- resources/views/pages/riwayat.blade.php --}}
@extends('layouts.master')

@section('title', 'Riwayat Deteksi')

@section('content')
    <div class="page-header">
        <h1 class="page-title">📋 Riwayat Deteksi Plagiarisme</h1>
        <p class="page-subtitle">Daftar semua hasil deteksi yang telah dilakukan</p>
    </div>

    {{-- Notifikasi Success --}}
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter --}}
    <div class="card">
        <form method="GET" action="{{ route('riwayat.index') }}"
            style="display: flex; gap: 15px; flex-wrap: wrap; align-items: end;">
            <div style="min-width: 150px;">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="low" {{ request('status') == 'low' ? 'selected' : '' }}>✅ Aman</option>
                    <option value="medium" {{ request('status') == 'medium' ? 'selected' : '' }}>⚠️ Sedang</option>
                    <option value="high" {{ request('status') == 'high' ? 'selected' : '' }}>🚨 Tinggi</option>
                </select>
            </div>
            <div>
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-input" value="{{ request('date_from') }}">
            </div>
            <div>
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-input" value="{{ request('date_to') }}">
            </div>
            <button type="submit" class="btn btn-primary">🔍 Filter</button>
            <a href="{{ route('riwayat.index') }}" class="btn btn-secondary">Reset</a>
        </form>
    </div>

    {{-- Results Table --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Hasil Deteksi ({{ $results->total() }} data)</h3>
        </div>

        @if($results->count() > 0)
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Waktu</th>
                            <th width="120">Similarity</th>
                            <th width="150">Status</th>
                            <th width="100">Parameter</th>
                            <th width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($results as $index => $result)
                            <tr onclick="window.location='{{ route('riwayat.show', $result->id) }}'" style="cursor: pointer;">
                                <td>{{ $results->firstItem() + $index }}</td>
                                <td>{{ $result->created_at->format('d M Y, H:i') }}</td>
                                <td>
                                    <strong style="font-size: 1.1rem;">{{ number_format($result->highest_similarity, 2) }}%</strong>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $result->status_color }}">
                                        {{ $result->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <span style="font-size: 0.8rem; color: #64748b;">
                                        k={{ $result->k_gram }}, w={{ $result->window_size }}
                                    </span>
                                </td>
                                <td onclick="event.stopPropagation();">
                                    <div style="display: flex; gap: 5px;">
                                        <form action="{{ route('riwayat.destroy', $result->id) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus riwayat ini?')">
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

            <div style="margin-top: 20px;">
                {{ $results->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <h3>Belum ada riwayat deteksi</h3>
                <p>Mulai deteksi plagiarisme untuk melihat riwayat</p>
                <a href="{{ route('deteksi.index') }}" class="btn btn-primary" style="margin-top: 15px;">
                    🔎 Mulai Deteksi
                </a>
            </div>
        @endif
    </div>
@endsection