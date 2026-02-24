<?php
// app/Http/Controllers/PlagiarismController.php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\DetectionResult;
use App\Services\WinnowingService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

class PlagiarismController extends Controller
{
    protected WinnowingService $winnowingService;

    public function __construct(WinnowingService $winnowingService)
    {
        $this->winnowingService = $winnowingService;
    }

    /**
     * Halaman deteksi plagiarisme
     */
    public function index()
    {
        $datasetCount = Dataset::count();

        return view('pages.deteksi', [
            'datasetCount' => $datasetCount
        ]);
    }

    /**
     * Proses deteksi plagiarisme
     * Input: file utuh (PDF/DOC/DOCX/TXT), tapi yang dipakai hanya ABSTRAK
     */
    public function process(Request $request)
    {
        $request->validate([
            'abstrak_file' => 'required|file|mimes:pdf,doc,docx,txt|max:5120',
            'k_gram' => 'nullable|integer|min:2|max:10',
            'window_size' => 'nullable|integer|min:2|max:10',
        ], [
            'abstrak_file.required' => 'File abstrak wajib diupload.',
            'abstrak_file.mimes' => 'Format file harus PDF, DOC, DOCX, atau TXT.',
            'abstrak_file.max' => 'Ukuran file maksimal 5MB.',
        ]);

        $kGram = $request->input('k_gram', 5);
        $windowSize = $request->input('window_size', 4);

        try {
            // Cek dataset
            $datasetCount = Dataset::count();
            if ($datasetCount === 0) {
                return view('pages.deteksi', [
                    'error' => 'Dataset kosong. Silakan upload dataset terlebih dahulu.',
                    'datasetCount' => 0,
                ]);
            }

            // 1) Ekstrak teks penuh dari file
            $file = $request->file('abstrak_file');
            $fullText = $this->extractTextFromFile($file);

            // 2) Ambil hanya bagian ABSTRAK dari teks penuh
            $inputText = $this->extractAbstractSection($fullText);

            // Tidak ada heading ABSTRAK/ABSTRACT/INTISARI/RINGKASAN
            if (trim($inputText) === '') {
                return view('pages.deteksi', [
                    'error' => 'Bagian "ABSTRAK" tidak ditemukan di dalam file. 
Pastikan dokumen memiliki heading ABSTRAK (atau ABSTRACT/INTISARI/RINGKASAN) yang berdiri sendiri di satu baris.',
                    'datasetCount' => $datasetCount,
                    'inputText' => '',
                ]);
            }

            // Abstrak terlalu pendek
            if (mb_strlen(trim($inputText)) < 50) {
                return view('pages.deteksi', [
                    'error' => 'Teks abstrak yang terdeteksi di dalam file minimal 50 karakter. 
Pastikan file memiliki heading "ABSTRAK" dengan isi di bawahnya.',
                    'datasetCount' => $datasetCount,
                    'inputText' => $inputText,
                ]);
            }

            // 3) Set parameter Winnowing
            $this->winnowingService
                ->setKGram($kGram)
                ->setWindowSize($windowSize);

            // 4) Jalankan deteksi
            $result = $this->winnowingService->detectPlagiarism($inputText);

            if (!$result['success']) {
                return view('pages.deteksi', [
                    'error' => $result['error'],
                    'datasetCount' => $datasetCount,
                    'inputText' => $inputText,
                ]);
            }

            $topResults = $result['top_results'] ?? [];

            // 5) Simpan ke database
            $detectionResult = DetectionResult::create([
                'input_text' => $inputText,
                'input_preprocessed' => $result['input_analysis']['preprocessed_length'] ?? null,
                'input_fingerprints' => $result['input_fingerprints'],
                'highest_similarity' => $result['best_similarity'],
                'comparison_results' => $topResults,
                'k_gram' => $kGram,
                'window_size' => $windowSize,
                'status' => $result['status']['level'],
                'ip_address' => $request->ip(),
            ]);

            // 6) Kirim ke view
            return view('pages.deteksi', [
                'hasil' => $result['best_similarity'],
                'status' => $result['status'],
                'top' => $topResults,
                'inputAnalysis' => $result['input_analysis'],
                'parameters' => $result['parameters'],
                'totalCompared' => $result['total_compared'],
                'detectionId' => $detectionResult->id,
                'datasetCount' => $datasetCount,
                'inputText' => $inputText, // abstrak saja akan ditampilkan & di-highlight
            ]);

        } catch (\Throwable $e) {
            Log::error('Plagiarism detection error: ' . $e->getMessage());

            return view('pages.deteksi', [
                'error' => 'Terjadi kesalahan saat memproses (DOCX/PDF): ' . $e->getMessage(),
                'datasetCount' => Dataset::count(),
            ]);
        }
    }

    /**
     * Ekstrak teks dari file utuh.
     * - TXT   : langsung dibaca
     * - PDF   : smalot/pdfparser
     * - DOCX  : baca word/document.xml via ZipArchive (tanpa PhpWord)
     * - DOC   : pakai PhpWord, ekstrak teks secara rekursif
     */
    protected function extractTextFromFile(UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        // 1) TXT
        if ($ext === 'txt') {
            return file_get_contents($path) ?: '';
        }

        // 2) PDF
        if ($ext === 'pdf') {
            if (!class_exists(\Smalot\PdfParser\Parser::class)) {
                throw new \RuntimeException(
                    'Ekstraksi PDF belum dikonfigurasi. Jalankan: composer require smalot/pdfparser'
                );
            }

            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();

            return $text ?: '';
        }

        // 3) DOCX (format Word baru) - TANPA PhpWord (hindari error m:oMath, dll.)
        if ($ext === 'docx') {
            if (!class_exists(\ZipArchive::class)) {
                throw new \RuntimeException(
                    'Ekstensi PHP "zip" belum aktif. Aktifkan extension=zip di php.ini lalu restart server.'
                );
            }

            $zip = new \ZipArchive();
            if ($zip->open($path) !== true) {
                throw new \RuntimeException('Tidak dapat membuka file DOCX.');
            }

            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            if ($xml === false) {
                throw new \RuntimeException('Tidak dapat menemukan word/document.xml di file DOCX.');
            }

            // Anggap setiap paragraf/line break sebagai newline
            $xml = preg_replace('/<w:p[^>]*>/', "\n", $xml);
            $xml = preg_replace('/<w:br[^>]*\/>/', "\n", $xml);

            // Buang semua tag XML (termasuk m:oMath, gambar, dsb.)
            $text = strip_tags($xml);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_XML1, 'UTF-8');

            return $text ?: '';
        }

        // 4) DOC (Word lama) - pakai PhpWord
        if ($ext === 'doc') {
            if (!class_exists(\PhpOffice\PhpWord\IOFactory::class)) {
                throw new \RuntimeException(
                    'Ekstraksi Word (.doc) belum dikonfigurasi. Jalankan: composer require phpoffice/phpword'
                );
            }

            $phpWord = \PhpOffice\PhpWord\IOFactory::load($path);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $text .= $this->collectPhpWordText($element);
                }
                $text .= "\n";
            }

            return trim($text);
        }

        throw new \RuntimeException('Tipe file tidak didukung untuk ekstraksi teks: ' . $ext);
    }

    /**
     * Rekursif: ambil teks dari elemen PhpWord (digunakan untuk file .doc)
     */
    protected function collectPhpWordText($element): string
    {
        $text = '';

        // Teks biasa
        if ($element instanceof \PhpOffice\PhpWord\Element\Text) {
            $text .= $element->getText() . ' ';
        }
        // Hyperlink
        elseif ($element instanceof \PhpOffice\PhpWord\Element\Link) {
            $text .= $element->getText() . ' ';
        }
        // Break baris
        elseif ($element instanceof \PhpOffice\PhpWord\Element\TextBreak) {
            $text .= "\n";
        }
        // Kumpulan elemen teks (TextRun, Paragraph, dll.)
        elseif (
            $element instanceof \PhpOffice\PhpWord\Element\TextRun ||
            $element instanceof \PhpOffice\PhpWord\Element\Paragraph ||
            (method_exists($element, 'getElements') &&
                $element instanceof \PhpOffice\PhpWord\Element\AbstractElement)
        ) {
            if (method_exists($element, 'getElements')) {
                foreach ($element->getElements() as $child) {
                    $text .= $this->collectPhpWordText($child);
                }
            }
        }
        // Tabel
        elseif ($element instanceof \PhpOffice\PhpWord\Element\Table) {
            foreach ($element->getRows() as $row) {
                foreach ($row->getCells() as $cell) {
                    foreach ($cell->getElements() as $cellElement) {
                        $text .= $this->collectPhpWordText($cellElement);
                    }
                    $text .= "\n";
                }
                $text .= "\n";
            }
        }
        // Elemen lain (gambar, shape, dsb.) diabaikan

        return $text;
    }

    /**
     * Ambil hanya bagian ABSTRAK dari teks penuh dokumen.
     * Heading yang dianggap: ABSTRAK / ABSTRACT / INTISARI / RINGKASAN
     * Heading harus muncul sebagai satu baris sendiri (bukan sekedar kata "abstrak" di judul).
     */
    protected function extractAbstractSection(string $fullText): string
    {
        if (trim($fullText) === '') {
            return '';
        }

        // Normalisasi line break & spasi
        $normalized = preg_replace("/\r\n+|\n+|\r+/", "\n", $fullText);
        $normalized = preg_replace('/[ \t]+/', ' ', $normalized);
        $normalized = trim($normalized);

        // Pecah per baris
        $lines = explode("\n", $normalized);
        $normalized = implode("\n", $lines); // pastikan struktur konsisten
        $lineCount = count($lines);

        // Heading awal abstrak
        $startKeywords = [
            'ABSTRAK',
            'ABSTRACT',
            'INTISARI',
            'RINGKASAN',
        ];

        // Heading akhir abstrak (bagian berikutnya setelah abstrak)
        $endKeywords = [
            'KATA KUNCI',
            'DAFTAR ISI',
            'DAFTAR TABEL',
            'DAFTAR GAMBAR',
            'DAFTAR PERSAMAAN',
            'BAB I PENDAHULUAN',
            'BAB 1 PENDAHULUAN',
            'BAB I ',
            'BAB 1 ',
            'PENDAHULUAN',
        ];

        $startPos = false;
        $startLineIndex = null;

        // ==============================
        // 1. CARI BARIS HEADING ABSTRAK
        // ==============================
        $offset = 0; // offset karakter dari awal string

        for ($i = 0; $i < $lineCount; $i++) {
            $line = $lines[$i];
            $lineLen = mb_strlen($line, 'UTF-8');
            $lineTrim = trim($line);

            $lineStartOffset = $offset;

            if ($lineTrim !== '') {
                $upperLine = mb_strtoupper($lineTrim, 'UTF-8');

                foreach ($startKeywords as $kw) {
                    // Heading dianggap valid jika SATU BARIS saja:
                    // "ABSTRAK", "ABSTRAK.", atau "ABSTRAK:"
                    if (
                        $upperLine === $kw ||
                        $upperLine === $kw . '.' ||
                        $upperLine === $kw . ':'
                    ) {
                        // Mulai setelah baris heading ini
                        $startPos = $lineStartOffset + $lineLen;
                        if ($i < $lineCount - 1) {
                            $startPos += 1; // lewati newline
                        }
                        $startLineIndex = $i;
                        break 2; // keluar dari kedua loop
                    }
                }
            }

            // Update offset ke awal baris berikutnya
            $offset = $lineStartOffset + $lineLen;
            if ($i < $lineCount - 1) {
                $offset += 1; // newline
            }
        }

        // Jika tidak ketemu heading abstrak sama sekali → anggap TIDAK ADA abstrak
        if ($startPos === false) {
            return '';
        }

        // ==============================
        // 2. CARI HEADING BERIKUTNYA (END)
        // ==============================
        $endPos = mb_strlen($normalized, 'UTF-8');
        $offset = 0;

        for ($i = 0; $i < $lineCount; $i++) {
            $line = $lines[$i];
            $lineLen = mb_strlen($line, 'UTF-8');
            $lineTrim = trim($line);

            $lineStartOffset = $offset;

            if ($i > $startLineIndex && $lineTrim !== '') {
                $upperLine = mb_strtoupper($lineTrim, 'UTF-8');

                foreach ($endKeywords as $kw) {
                    // Jika baris DIAWALI salah satu keyword akhir → dianggap heading berikut
                    if (mb_stripos($upperLine, $kw, 0, 'UTF-8') === 0) {
                        $endPos = $lineStartOffset; // abstrak berakhir sebelum baris heading ini
                        break 2;
                    }
                }
            }

            // Update offset
            $offset = $lineStartOffset + $lineLen;
            if ($i < $lineCount - 1) {
                $offset += 1; // newline
            }
        }

        if ($endPos <= $startPos) {
            return '';
        }

        $length = $endPos - $startPos;
        $abstract = mb_substr($normalized, $startPos, $length, 'UTF-8');

        return trim($abstract);
    }

    /**
     * Tampilkan analisis detail untuk teks tertentu
     */
    public function analyze(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:10'
        ]);

        $text = $request->input('text');
        $analysis = $this->winnowingService->getDetailedAnalysis($text);

        return response()->json([
            'success' => true,
            'analysis' => $analysis
        ]);
    }

    /**
     * Halaman riwayat deteksi
     */
    public function history(Request $request)
    {
        $query = DetectionResult::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $results = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('pages.riwayat', compact('results'));
    }

    /**
     * Detail hasil deteksi
     */
    public function showResult($id)
    {
        $result = DetectionResult::findOrFail($id);

        return view('pages.hasil-detail', compact('result'));
    }

    /**
     * Hapus hasil deteksi
     */
    public function deleteResult($id)
    {
        DetectionResult::findOrFail($id)->delete();

        return redirect()
            ->back()
            ->with('success', 'Riwayat deteksi berhasil dihapus.');
    }

    /**
     * Export hasil ke PDF
     */
    public function exportPdf($id)
    {
        $result = DetectionResult::findOrFail($id);

        // TODO: Implement PDF export

        return response()->json(['message' => 'Export PDF - Coming soon']);
    }

    /**
     * API endpoint untuk deteksi (jika diperlukan)
     */
    public function apiDetect(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:50',
            'k_gram' => 'nullable|integer|min:2|max:10',
            'window_size' => 'nullable|integer|min:2|max:10',
        ]);

        $this->winnowingService
            ->setKGram($request->input('k_gram', 5))
            ->setWindowSize($request->input('window_size', 4));

        $result = $this->winnowingService->detectPlagiarism($request->input('text'));

        return response()->json($result);
    }
}