{{-- resources/views/pages/compare-detail.blade.php --}}
@extends('layouts.master')

@section('title', 'Perbandingan Detail')

@section('content')
<div class="page-header">
    <h1 class="page-title">🔍 Perbandingan Detail Dua Dokumen</h1>
    <p class="page-subtitle">Analisis mendalam bagian-bagian yang terdeteksi mirip</p>
</div>

{{-- Side by Side Comparison --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">📊 Perbandingan Teks</h3>
        <span class="badge badge-warning">{{ $totalMatches }} segmen cocok</span>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <div>
            <h4 style="color: #1e40af; margin-bottom: 10px;">Dokumen 1</h4>
            <div class="comparison-text-box">
                {!! $highlighted1 !!}
            </div>
        </div>
        <div>
            <h4 style="color: #92400e; margin-bottom: 10px;">Dokumen 2</h4>
            <div class="comparison-text-box">
                {!! $highlighted2 !!}
            </div>
        </div>
    </div>
</div>

{{-- Matching Segments Table --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title">📋 Daftar Segmen yang Cocok</h3>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Segmen K-gram</th>
                    <th width="150">Hash Value</th>
                    <th width="100">Posisi Dok 1</th>
                    <th width="100">Posisi Dok 2</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matches as $index => $match)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <code style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px;">
                            {{ $match['text1_segment'] }}
                        </code>
                    </td>
                    <td style="font-family: monospace; font-size: 0.8rem; color: #64748b;">
                        {{ $match['hash'] }}
                    </td>
                    <td>{{ $match['text1_position']['start'] }}-{{ $match['text1_position']['end'] }}</td>
                    <td>{{ $match['text2_position']['start'] }}-{{ $match['text2_position']['end'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.comparison-text-box {
    background: #fff;
    padding: 20px;
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    min-height: 400px;
    max-height: 600px;
    overflow-y: auto;
    line-height: 2;
    font-size: 0.95rem;
}

mark.plagiarism-match {
    background: #fef3c7;
    padding: 3px 6px;
    border-radius: 4px;
    font-weight: 600;
    color: #92400e;
    border-bottom: 2px solid #f59e0b;
}
</style>
@endsection