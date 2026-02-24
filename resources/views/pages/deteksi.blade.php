{{-- resources/views/pages/deteksi.blade.php --}}
@extends('layouts.master')

@section('title', 'Deteksi Plagiarisme')

@push('styles')
    <style>
        /* ====== SELURUH CSS DARI VERSI ANDA ====== */
        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.75);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            max-width: 900px;
            width: 90%;
            max-height: 90vh;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-close {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.5);
            color: white;
            width: 34px;
            height: 34px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 25px;
            max-height: calc(90vh - 80px);
            overflow-y: auto;
        }

        /* Document Info Card */
        .doc-info-card {
            background: linear-gradient(135deg, #f8fafc, #e2e8f0);
            border: 2px solid #cbd5e1;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .doc-info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
        }

        .doc-info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .doc-info-label {
            font-size: 0.85rem;
            color: #64748b;
            font-weight: 500;
        }

        .doc-info-value {
            font-size: 1.1rem;
            color: #1e293b;
            font-weight: 700;
        }

        /* K-gram Statistics */
        .kgram-stats {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            border: 2px solid #93c5fd;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .stat-box {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #3b82f6;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 8px;
            font-weight: 500;
        }

        /* Similar Words Section */
        .similar-words-section {
            margin-top: 25px;
        }

        /* Abstract Compare Section */
        .abstract-compare {
            margin-top: 25px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }

        .abstract-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .abstract-box {
            background: #f9fafb;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            padding: 12px 14px;
            font-size: 0.95rem;
            color: #111827;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        mark.highlight {
            background-color: #fef08a;
            padding: 0 2px;
            border-radius: 3px;
        }

        /* Section Header & Words */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .word-count-badge {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .words-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            padding: 20px;
            background: #fffbeb;
            border-radius: 10px;
            border: 2px solid #fde68a;
            min-height: 120px;
        }

        /* Kata mirip */
        .similar-word {
            display: inline-flex;
            align-items: center;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            color: #92400e;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            border: 2px solid #f59e0b;
            box-shadow: 0 2px 6px rgba(245, 158, 11, 0.25);
            transition: all 0.2s ease;
            cursor: default;
        }

        .similar-word:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4);
            background: linear-gradient(135deg, #fde68a, #fcd34d);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: #94a3b8;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        .empty-title {
            font-size: 1.2rem;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .empty-text {
            color: #94a3b8;
            font-size: 0.95rem;
        }

        /* Similarity Badge */
        .similarity-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1rem;
        }

        .similarity-badge.high {
            background: linear-gradient(135deg, #f87171, #ef4444);
            color: white;
        }

        .similarity-badge.medium {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
        }

        .similarity-badge.low {
            background: linear-gradient(135deg, #34d399, #10b981);
            color: white;
        }

        /* Action Buttons */
        .modal-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
        }

        .btn-primary-action {
            flex: 1;
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }

        .btn-download {
            flex: 1;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
        }

        .btn-secondary-action {
            background: #f1f5f9;
            color: #475569;
            border: 2px solid #cbd5e1;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .btn-secondary-action:hover {
            background: #e2e8f0;
            border-color: #94a3b8;
        }

        /* View Detail Button */
        .btn-view-detail {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-view-detail:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        /* Loading */
        .modal-loading {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            gap: 20px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #e2e8f0;
            border-top-color: #667eea;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Info Notice */
        .info-notice {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            border-left: 4px solid #3b82f6;
            padding: 15px 18px;
            border-radius: 6px;
            margin-top: 20px;
        }

        .info-notice-text {
            font-size: 0.9rem;
            color: #1e40af;
            line-height: 1.6;
        }

        /* PRINT STYLES */
        @media print {
            body * {
                visibility: hidden;
            }

            #printArea,
            #printArea * {
                visibility: visible;
            }

            #printArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .modal-overlay {
                display: block !important;
                position: static !important;
                background: white !important;
            }

            .modal-content {
                box-shadow: none !important;
                max-width: 100% !important;
                max-height: none !important;
                border-radius: 0 !important;
            }

            .modal-header {
                background: #667eea !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                page-break-after: avoid;
            }

            .modal-close {
                display: none !important;
            }

            .modal-body {
                max-height: none !important;
                overflow: visible !important;
                padding: 20px !important;
            }

            .modal-actions {
                display: none !important;
            }

            .doc-info-card,
            .kgram-stats,
            .stat-box,
            .similar-word,
            .similarity-badge,
            .info-notice,
            .words-container,
            .abstract-box,
            mark.highlight {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .stats-grid {
                page-break-inside: avoid;
            }

            .stat-box {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            .words-container {
                page-break-inside: auto;
            }

            .similar-word {
                break-inside: avoid;
                page-break-inside: avoid;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2) !important;
            }

            .info-notice {
                page-break-inside: avoid;
            }

            #printArea::before {
                content: "Laporan Analisis Plagiarisme";
                display: block;
                font-size: 24px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 3px solid #667eea;
            }

            #printArea::after {
                content: "Dicetak pada: " attr(data-print-date);
                display: block;
                margin-top: 30px;
                padding-top: 15px;
                border-top: 2px solid #e2e8f0;
                text-align: center;
                font-size: 12px;
                color: #64748b;
            }
        }

        .btn-download.printing {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .btn-download.printing::after {
            content: " Menyiapkan...";
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">🔎 Deteksi Plagiarisme</h1>
        <p class="page-subtitle">
            Bandingkan teks abstrak dengan {{ $datasetCount ?? 0 }} dokumen di database menggunakan Algoritma Winnowing
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            ✅ {{ session('success') }}
        </div>
    @endif

    @isset($error)
        <div class="alert alert-error">
            ❌ {{ $error }}
        </div>
    @endisset

    {{-- Form Input --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📝 Input Abstrak (File)</h3>
        </div>

        <form method="POST" action="{{ route('deteksi.process') }}" id="deteksiForm" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">File Abstrak yang Akan Diuji</label>
                <input type="file" name="abstrak_file" id="abstrakFile" class="form-input" accept=".pdf,.doc,.docx,.txt"
                    required>
                <p class="form-hint">
                    Upload file yang berisi teks abstrak (PDF, DOC, DOCX, atau TXT).
                    Minimal 50 karakter setelah diekstrak.
                </p>
                @error('abstrak_file')
                    <p style="color:#dc2626; font-size:0.85rem; margin-top:4px;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teks hasil ekstraksi (opsional) --}}
            @if(!empty($inputText ?? ''))
                <div class="form-group">
                    <label class="form-label">Teks Abstrak Hasil Ekstraksi</label>
                    <textarea class="form-textarea" rows="6" readonly>{{ $inputText }}</textarea>
                    <p class="form-hint">Teks ini diekstrak otomatis dari file yang Anda upload.</p>
                </div>
            @endif

            {{-- Parameter Settings --}}
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label class="form-label">Ukuran K-gram (k)</label>
                    <select name="k_gram" class="form-select">
                        <option value="3">3 (Lebih sensitif)</option>
                        <option value="4">4</option>
                        <option value="5" selected>5 (Default)</option>
                        <option value="6">6</option>
                        <option value="7">7 (Lebih spesifik)</option>
                    </select>
                    <p class="form-hint">Panjang karakter untuk setiap segmen teks</p>
                </div>
                <div class="form-group">
                    <label class="form-label">Ukuran Window (w)</label>
                    <select name="window_size" class="form-select">
                        <option value="3">3</option>
                        <option value="4" selected>4 (Default)</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                    </select>
                    <p class="form-hint">Jumlah hash dalam satu window</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px;">
                🔍 Mulai Analisis Plagiarisme
            </button>
        </form>
    </div>

    {{-- Loading Overlay --}}
    <div id="loadingOverlay" class="modal-overlay" style="display: none;">
        <div class="spinner"></div>
        <div style="font-weight: 600; color: #1e293b;">Sedang memproses algoritma Winnowing...</div>
        <div style="color: #64748b; font-size: 0.9rem;">Mohon tunggu sebentar</div>
    </div>

    {{-- Hasil Deteksi --}}
    @isset($hasil)
        <div class="card" style="margin-top: 30px;">
            <div class="card-header">
                <h3 class="card-title">📊 Hasil Analisis</h3>
                @isset($detectionId)
                    <span class="badge badge-info">ID: #{{ $detectionId }}</span>
                @endisset
            </div>

            @php
                $colorClass = match ($status['level']) {
                    'low' => 'background: linear-gradient(135deg, #10b981, #059669);',
                    'medium' => 'background: linear-gradient(135deg, #f59e0b, #d97706);',
                    'high' => 'background: linear-gradient(135deg, #ef4444, #b91c1c);',
                    default => 'background: #64748b;'
                };
            @endphp

            <div style="{{ $colorClass }} color: #fff; padding: 30px; border-radius: 12px; margin-bottom: 25px;">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
                    <div>
                        <div style="font-size: 0.85rem; opacity: 0.9; text-transform: uppercase; letter-spacing: 1px;">
                            Tingkat Kemiripan Tertinggi
                        </div>
                        <div style="font-size: 3.5rem; font-weight: 800; line-height: 1;">
                            {{ number_format($hasil, 2) }}%
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div
                            style="background: rgba(255,255,255,0.2); padding: 8px 20px; border-radius: 20px; font-weight: 600; margin-bottom: 10px;">
                            {{ $status['icon'] }} {{ $status['label'] }}
                        </div>
                        <div style="opacity: 0.9; font-size: 0.9rem; max-width: 300px;">
                            {{ $status['description'] }}
                        </div>
                    </div>
                </div>
            </div>

            @isset($inputAnalysis)
                <div
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin-bottom: 25px;">
                    <div style="background: #f8fafc; padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #3b82f6;">{{ $inputAnalysis['original_length'] }}
                        </div>
                        <div style="color: #64748b; font-size: 0.8rem;">Karakter Asli</div>
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #8b5cf6;">
                            {{ $inputAnalysis['preprocessed_length'] }}</div>
                        <div style="color: #64748b; font-size: 0.8rem;">Setelah Preprocessing</div>
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">{{ $inputAnalysis['kgrams_count'] }}</div>
                        <div style="color: #64748b; font-size: 0.8rem;">K-gram Dihasilkan</div>
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">{{ $inputAnalysis['fingerprints_count'] }}
                        </div>
                        <div style="color: #64748b; font-size: 0.8rem;">Fingerprint</div>
                    </div>
                    <div style="background: #f8fafc; padding: 15px; border-radius: 10px; text-align: center;">
                        <div style="font-size: 1.5rem; font-weight: 700; color: #64748b;">{{ $totalCompared ?? 0 }}</div>
                        <div style="color: #64748b; font-size: 0.8rem;">Dokumen Dibandingkan</div>
                    </div>
                </div>
            @endisset

            @if(!empty($top))
                <h4 style="margin-bottom: 15px; color: #1e293b;">📋 Dokumen dengan Kemiripan Tertinggi</h4>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Judul Dokumen</th>
                                <th width="120">Penulis</th>
                                <th width="80">Tahun</th>
                                <th width="200">Kemiripan</th>
                                <th width="120">Fingerprint Sama</th>
                                <th width="140">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($top as $index => $row)
                                @php
                                    $sim = (float) $row['similarity'];
                                    $progressClass = $sim >= 50 ? 'red' : ($sim >= 25 ? 'orange' : 'green');
                                @endphp
                                <tr>
                                    <td style="text-align: center;">
                                        <span class="badge badge-info">{{ $index + 1 }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('dataset.show', $row['id']) }}"
                                            style="color: #2563eb; text-decoration: none; font-weight: 500;">
                                            {{ $row['judul'] }}
                                        </a>
                                    </td>
                                    <td>{{ $row['penulis'] ?? '-' }}</td>
                                    <td>{{ $row['tahun'] ?? '-' }}</td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <span style="font-weight: 600; width: 50px;">{{ number_format($sim, 2) }}%</span>
                                            <div class="progress-bar" style="flex: 1;">
                                                <div class="progress-fill {{ $progressClass }}" style="width: {{ min($sim, 100) }}%;">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <span
                                            class="badge badge-{{ $progressClass == 'green' ? 'success' : ($progressClass == 'orange' ? 'warning' : 'danger') }}">
                                            {{ $row['common_fingerprints'] }} sama
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <button class="btn-view-detail" onclick='showSimilarWords({{ json_encode($row) }})'>
                                            👁️ Lihat Kata Mirip
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @isset($parameters)
                <div style="margin-top: 20px; padding: 15px; background: #f1f5f9; border-radius: 8px;">
                    <strong>Parameter yang digunakan:</strong>
                    K-gram = {{ $parameters['k_gram'] }},
                    Window Size = {{ $parameters['window_size'] }},
                    Hash Base = {{ $parameters['hash_base'] ?? 31 }}
                </div>
            @endisset
        </div>
    @endisset

    {{-- Modal Kata yang Mirip --}}
    <div id="similarWordsModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title">
                    <span>🔍</span>
                    <span>Kata-Kata yang Mirip</span>
                </div>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body" id="modalBody">
                <div class="modal-loading">
                    <div class="spinner"></div>
                    <div style="color: #64748b; margin-top: 15px;">Memuat data...</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="card" style="background: #eff6ff; border: 1px solid #bfdbfe;">
        <h4 style="color: #1e40af; margin-bottom: 15px;">💡 Cara Kerja Algoritma Winnowing</h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <strong>1. Preprocessing</strong>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 5px;">
                    Menghilangkan tanda baca, angka, dan stopwords
                </p>
            </div>
            <div>
                <strong>2. K-gram</strong>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 5px;">
                    Memecah teks menjadi segmen k karakter
                </p>
            </div>
            <div>
                <strong>3. Rolling Hash</strong>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 5px;">
                    Mengkonversi k-gram menjadi nilai hash
                </p>
            </div>
            <div>
                <strong>4. Windowing</strong>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 5px;">
                    Memilih hash terkecil sebagai fingerprint
                </p>
            </div>
            <div>
                <strong>5. Jaccard Similarity</strong>
                <p style="color: #64748b; font-size: 0.85rem; margin-top: 5px;">
                    Menghitung kemiripan antar fingerprint
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // abstrak input (hasil ekstraksi file) untuk highlight di modal
            window.inputAbstrak = @json($inputText ?? '');

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('deteksiForm');
                const overlay = document.getElementById('loadingOverlay');

                if (form) {
                    form.addEventListener('submit', function () {
                        overlay.style.display = 'flex';
                    });
                }
            });

            /**
             * Tampilkan modal dengan kata-kata yang mirip
             */
            function showSimilarWords(data) {
                const modal = document.getElementById('similarWordsModal');
                const modalBody = document.getElementById('modalBody');
                modal.classList.add('active');

                modalBody.innerHTML = `
                <div class="modal-loading">
                    <div class="spinner"></div>
                    <div style="color: #64748b; margin-top: 15px;">Memuat kata-kata yang mirip...</div>
                </div>
            `;

                setTimeout(() => { renderSimilarWords(data); }, 400);
            }

            /**
             * Render kata-kata mirip + abstrak dengan highlight
             */
            function renderSimilarWords(data) {
                const modalBody = document.getElementById('modalBody');

                const similarity = parseFloat(data.similarity);
                const similarWords = data.similar_words || [];
                const matchedKgrams = data.matched_kgrams_count || 0;
                const totalKgramsInput = data.total_kgrams_input || 0;
                const totalKgramsDoc = data.total_kgrams_doc || 0;

                const inputAbstrak = window.inputAbstrak || '';
                const docAbstrak = data.abstrak || data.abstrak_doc || '';

                const abstrakInputHighlighted = highlightText(inputAbstrak, similarWords);
                const abstrakDocHighlighted = highlightText(docAbstrak, similarWords);

                let similarityLevel = 'low';
                if (similarity >= 50) similarityLevel = 'high';
                else if (similarity >= 25) similarityLevel = 'medium';

                const printDate = new Date().toLocaleDateString('id-ID', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                let html = `
                <div id="printArea" data-print-date="${printDate}">
                    <div class="doc-info-card">
                        <h3 style="margin: 0 0 15px 0; color: #1e293b; font-size: 1.2rem;">
                            📄 ${escapeHtml(data.judul)}
                        </h3>
                        <div class="doc-info-grid">
                            <div class="doc-info-item">
                                <span class="doc-info-label">👤 Penulis</span>
                                <span class="doc-info-value">${escapeHtml(data.penulis || '-')}</span>
                            </div>
                            <div class="doc-info-item">
                                <span class="doc-info-label">📅 Tahun</span>
                                <span class="doc-info-value">${escapeHtml(data.tahun || '-')}</span>
                            </div>
                            <div class="doc-info-item">
                                <span class="doc-info-label">📊 Tingkat Kemiripan</span>
                                <span class="similarity-badge ${similarityLevel}">
                                    ${similarity.toFixed(2)}%
                                </span>
                            </div>
                            <div class="doc-info-item">
                                <span class="doc-info-label">🔢 Fingerprint Sama</span>
                                <span class="doc-info-value">${data.common_fingerprints} dari ${data.total_fingerprints_input}</span>
                            </div>
                        </div>
                    </div>

                    <div class="kgram-stats">
                        <h4 style="margin: 0 0 10px 0; color: #1e40af; font-size: 1.1rem;">
                            📈 Detail Analisis K-gram
                        </h4>
                        <div class="stats-grid">
                            <div class="stat-box">
                                <div class="stat-number">${matchedKgrams}</div>
                                <div class="stat-label">K-gram yang Cocok</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">${totalKgramsInput}</div>
                                <div class="stat-label">Total K-gram Input</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">${totalKgramsDoc}</div>
                                <div class="stat-label">Total K-gram Dokumen</div>
                            </div>
                            <div class="stat-box">
                                <div class="stat-number">${matchedKgrams > 0 && totalKgramsInput > 0 ? ((matchedKgrams / totalKgramsInput) * 100).toFixed(1) : 0}%</div>
                                <div class="stat-label">Persentase Match</div>
                            </div>
                        </div>
                    </div>

                    <div class="abstract-compare">
                        <div>
                            <h4 class="abstract-title">Abstrak Input</h4>
                            <div class="abstract-box">
                                ${abstrakInputHighlighted || '<span style="color:#9ca3af;font-size:0.9rem;">Abstrak input tidak tersedia.</span>'}
                            </div>
                        </div>
                        <div>
                            <h4 class="abstract-title">Abstrak Dokumen</h4>
                            <div class="abstract-box">
                                ${abstrakDocHighlighted || '<span style="color:#9ca3af;font-size:0.9rem;">Abstrak dokumen tidak tersedia.</span>'}
                            </div>
                        </div>
                    </div>

                    <div class="similar-words-section">
                        <div class="section-header">
                            <div class="section-title">
                                <span>🔤</span>
                                <span>Kata-Kata yang Terdeteksi Mirip</span>
                            </div>
                            <div class="word-count-badge">${similarWords.length} kata</div>
                        </div>
            `;

                if (similarWords.length > 0) {
                    html += '<div class="words-container">';
                    similarWords.forEach(word => {
                        html += `<span class="similar-word">${escapeHtml(word)}</span>`;
                    });
                    html += '</div>';
                    html += `
                    <div class="info-notice">
                        <div class="info-notice-text">
                            <strong>💡 Penjelasan:</strong> Kata-kata di atas adalah kata asli dari teks yang terdeteksi mirip 
                            antara input Anda dengan dokumen di database. Sistem menemukan <strong>${matchedKgrams} k-gram yang cocok</strong> 
                            dari total <strong>${totalKgramsInput} k-gram</strong> yang dianalisis.
                        </div>
                    </div>
                `;
                } else {
                    html += `
                    <div class="words-container">
                        <div class="empty-state">
                            <div class="empty-icon">🔍</div>
                            <div class="empty-title">Tidak Ada Kata yang Mirip</div>
                            <div class="empty-text">
                                Sistem tidak menemukan kata-kata yang memiliki kemiripan signifikan dengan dokumen ini.
                                <br>Meskipun ada ${matchedKgrams} k-gram yang cocok, namun tidak membentuk kata yang jelas.
                            </div>
                        </div>
                    </div>
                `;
                }

                html += `
                    </div>
                </div>

                <div class="modal-actions">
                    <button onclick="downloadPDF()" class="btn-download" id="downloadBtn">
                        <span>📥</span>
                        <span>Download Laporan (PDF)</span>
                    </button>
                    <button onclick="viewFullDocument(${data.id})" class="btn-primary-action">
                        <span>📄</span>
                        <span>Lihat Dokumen Lengkap</span>
                    </button>
                    <button onclick="closeModal()" class="btn-secondary-action">
                        Tutup
                    </button>
                </div>
            `;

                modalBody.innerHTML = html;
            }

            function downloadPDF() {
                const downloadBtn = document.getElementById('downloadBtn');
                downloadBtn.classList.add('printing');
                downloadBtn.disabled = true;

                const originalTitle = document.title;
                document.title = 'Laporan_Analisis_Plagiarisme_' + new Date().getTime();

                setTimeout(() => {
                    window.print();
                    document.title = originalTitle;
                    downloadBtn.classList.remove('printing');
                    downloadBtn.disabled = false;
                }, 500);
            }

            function closeModal() {
                const modal = document.getElementById('similarWordsModal');
                modal.classList.remove('active');
            }

            function viewFullDocument(docId) {
                window.open(`/dataset/${docId}`, '_blank');
            }

            function escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            function escapeRegExp(string) {
                return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            }
            function highlightText(text, words) {
                if (!text) return '';
                if (!words || !words.length) return escapeHtml(text);

                const uniqueWords = [...new Set(
                    words.filter(Boolean).map(w => w.trim()).filter(Boolean)
                )];

                if (!uniqueWords.length) return escapeHtml(text);

                uniqueWords.sort((a, b) => b.length - a.length);

                const pattern = uniqueWords.map(w => escapeRegExp(w)).join('|');
                const regex = new RegExp(`\\b(${pattern})\\b`, 'gi'); //kata utuh saja

                const escaped = escapeHtml(text);
                return escaped.replace(regex, '<mark class="highlight">$1</mark>');
            }

            // Close modal saat klik di luar
            document.addEventListener('click', function (event) {
                const modal = document.getElementById('similarWordsModal');
                if (event.target === modal) closeModal();
            });

            // Close modal dengan ESC
            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') closeModal();
            });

            window.addEventListener('afterprint', function () {
                console.log('PDF berhasil di-generate atau print dialog ditutup');
            });
        </script>
    @endpush
@endsection