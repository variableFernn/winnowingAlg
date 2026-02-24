<?php
// app/Services/WinnowingService.php

namespace App\Services;

use App\Models\Dataset;
use Illuminate\Support\Facades\Log;

class WinnowingService
{
    protected int $kGram;
    protected int $windowSize;
    protected int $hashBase = 31;
    protected int $hashMod = 1000000007;

    // Daftar stopword (kata umum/imbuhan yang diabaikan)
    protected array $stopwords = [
        'yang',
        'dan',
        'di',
        'dari',
        'untuk',
        'dengan',
        'pada',
        'adalah',
        'ini',
        'dalam',
        'tidak',
        'akan',
        'ke',
        'atau',
        'juga',
        'dapat',
        'oleh',
        'sebagai',
        'tersebut',
        'serta',
        'telah',
        'itu',
        'karena',
        'bahwa',
        'antara',
        'secara',
        'sebuah',
        'maka',
        'ada',
        'yaitu',
        'bisa',
        'harus',
        'sudah',
        'saat',
        'sangat',
        'lebih',
        'agar',
        'seperti',
        'hal',
        'tanpa',
        'mereka',
        'kita',
        'kami',
        'saya',
        'ia',
        'dia',
        'belum',
        'hanya',
        'tetapi',
        'namun',
        'sedangkan',
        'apabila',
        'jika',
        'melalui',
        'menggunakan',
        'digunakan',
        'dilakukan',
        'melakukan',
        'memiliki',
        'terdapat',
        'terhadap',
        'maupun',
        'hingga',
        'sampai',
        'begitu',
        'demikian',
        'setiap',
        'semua',
        'beberapa',
        'banyak',
        'sering',
        'selalu',
        'masih',
        'lagi',
        'pun',
        'lain',
        'sama',
        'sendiri',
        'diri',
        'paling',
        'cukup',
        'suatu',
        'atas',
        'bawah',
        'ketika',
        'sehingga',
        'yakni',
        'tentang',
        'sejak',
        'bagi',
        'saja',
        'sekitar',
        'bila',
        'oleh',
        'para',
        'baik',
        'tersebut',
        'abstrak'
    ];

    public function __construct(int $kGram = 5, int $windowSize = 4)
    {
        $this->kGram = $kGram;
        $this->windowSize = $windowSize;
    }

    public function setKGram(int $k): self
    {
        $this->kGram = $k;
        return $this;
    }

    public function setWindowSize(int $w): self
    {
        $this->windowSize = $w;
        return $this;
    }

    /**
     * Preprocess standar untuk pembentukan fingerprint
     * (hapus angka, tanda baca, stopword, lalu digabung tanpa spasi)
     */
    public function preprocess(string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $text = strtolower($text);
        $text = preg_replace('/\d+/', '', $text);
        $text = preg_replace('/[^\p{L}\s]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        $words = explode(' ', $text);
        $filteredWords = array_filter($words, function ($word) {
            return !in_array($word, $this->stopwords) && strlen($word) > 1;
        });

        return implode('', $filteredWords);
    }

    /**
     * Preprocess tapi juga menyimpan mapping kata asli
     * Digunakan untuk ekstraksi kata mirip (bukan hanya k-gram)
     */
    public function preprocessWithMapping(string $text): array
    {
        if (empty($text)) {
            return ['preprocessed' => '', 'words' => [], 'filtered_words' => []];
        }

        // Lowercase dan bersihkan
        $text = strtolower($text);
        $text = preg_replace('/\d+/', '', $text);
        $text = preg_replace('/[^\p{L}\s]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        $words = explode(' ', $text);

        // Kata asli (untuk analisis) tapi TANPA stopword
        $originalWords = array_filter($words, function ($word) {
            $word = trim($word);
            return strlen($word) > 2 && !in_array($word, $this->stopwords);
        });

        // Filter stopword untuk teks yang akan dijadikan k-gram
        $filteredWords = array_filter($words, function ($word) {
            return !in_array($word, $this->stopwords) && strlen($word) > 1;
        });

        return [
            'preprocessed' => implode('', $filteredWords),
            'words' => array_values($originalWords),   // kata asli, bebas stopword
            'filtered_words' => array_values($filteredWords),
        ];
    }

    public function createKGrams(string $text): array
    {
        $kgrams = [];
        $length = strlen($text);

        if ($length < $this->kGram) {
            return [$text];
        }

        for ($i = 0; $i <= $length - $this->kGram; $i++) {
            $kgrams[] = substr($text, $i, $this->kGram);
        }

        return $kgrams;
    }

    public function rollingHash(string $kgram): int
    {
        $hash = 0;
        $length = strlen($kgram);

        for ($i = 0; $i < $length; $i++) {
            $charValue = ord($kgram[$i]);
            $hash = (($hash * $this->hashBase) + $charValue) % $this->hashMod;
        }

        return $hash;
    }

    public function computeAllHashes(array $kgrams): array
    {
        return array_map([$this, 'rollingHash'], $kgrams);
    }

    public function createWindows(array $hashes): array
    {
        $windows = [];
        $length = count($hashes);

        if ($length < $this->windowSize) {
            return [$hashes];
        }

        for ($i = 0; $i <= $length - $this->windowSize; $i++) {
            $windows[] = array_slice($hashes, $i, $this->windowSize);
        }

        return $windows;
    }

    public function selectFingerprints(array $windows): array
    {
        if (empty($windows)) {
            return [];
        }

        $fingerprints = [];
        $prevMinIndex = -1;

        foreach ($windows as $windowIndex => $window) {
            $minValue = min($window);
            $minPosition = -1;

            for ($i = count($window) - 1; $i >= 0; $i--) {
                if ($window[$i] === $minValue) {
                    $minPosition = $i;
                    break;
                }
            }

            $absolutePosition = $windowIndex + $minPosition;

            if ($absolutePosition !== $prevMinIndex) {
                $fingerprints[] = $minValue;
                $prevMinIndex = $absolutePosition;
            }
        }

        return array_unique($fingerprints);
    }

    public function generateFingerprints(string $text): array
    {
        $preprocessed = $this->preprocess($text);
        if (empty($preprocessed)) {
            return [
                'preprocessed' => '',
                'kgrams' => [],
                'hashes' => [],
                'windows' => [],
                'fingerprints' => []
            ];
        }

        $kgrams = $this->createKGrams($preprocessed);
        $hashes = $this->computeAllHashes($kgrams);
        $windows = $this->createWindows($hashes);
        $fingerprints = $this->selectFingerprints($windows);

        return [
            'preprocessed' => $preprocessed,
            'kgrams' => $kgrams,
            'kgrams_count' => count($kgrams),
            'hashes' => $hashes,
            'windows_count' => count($windows),
            'fingerprints' => $fingerprints,
            'fingerprints_count' => count($fingerprints)
        ];
    }

    public function jaccardSimilarity(array $fingerprints1, array $fingerprints2): float
    {
        if (empty($fingerprints1) || empty($fingerprints2)) {
            return 0.0;
        }

        $intersection = array_intersect($fingerprints1, $fingerprints2);
        $intersectionCount = count($intersection);

        $union = array_unique(array_merge($fingerprints1, $fingerprints2));
        $unionCount = count($union);

        if ($unionCount === 0) {
            return 0.0;
        }

        $similarity = $intersectionCount / $unionCount;
        return round($similarity * 100, 2);
    }

    /**
     * Extract kata asli yang mirip dari teks
     * - hasil sudah bebas stopword
     */
    public function extractSimilarWords(string $inputText, string $docText, array $matchedHashes): array
    {
        if (empty($matchedHashes)) {
            return [
                'similar_words' => [],
                'word_frequency' => [],
                'matched_kgrams_count' => 0,
                'total_kgrams_input' => 0,
                'total_kgrams_doc' => 0,
            ];
        }

        // Preprocess dengan mapping kata asli (tanpa stopword)
        $inputData = $this->preprocessWithMapping($inputText);
        $docData = $this->preprocessWithMapping($docText);

        // Generate k-grams untuk matching
        $inputKgrams = $this->createKGrams($inputData['preprocessed']);
        $docKgrams = $this->createKGrams($docData['preprocessed']);
        $inputHashes = $this->computeAllHashes($inputKgrams);
        $docHashes = $this->computeAllHashes($docKgrams);

        // Hitung berapa k-gram yang match
        $matchedKgramsCount = 0;
        $matchedKgramStrings = [];

        foreach ($matchedHashes as $hash) {
            $inputPositions = array_keys($inputHashes, $hash);
            $docPositions = array_keys($docHashes, $hash);

            foreach ($inputPositions as $pos) {
                if (isset($inputKgrams[$pos])) {
                    $matchedKgramsCount++;
                    $matchedKgramStrings[] = $inputKgrams[$pos];
                }
            }
        }

        // ============================
        // Cari kata-kata asli yang mirip
        // ============================
        $similarWords = [];
        $wordFrequency = [
            'input' => [],
            'doc' => [],
        ];

        // Frekuensi kata di INPUT (tanpa stopword)
        foreach ($inputData['words'] as $word) {
            $w = trim($word);
            if (strlen($w) <= 2 || in_array($w, $this->stopwords)) {
                continue;
            }
            if (!isset($wordFrequency['input'][$w])) {
                $wordFrequency['input'][$w] = 0;
            }
            $wordFrequency['input'][$w]++;
        }

        // Frekuensi kata di DOKUMEN (tanpa stopword)
        foreach ($docData['words'] as $word) {
            $w = trim($word);
            if (strlen($w) <= 2 || in_array($w, $this->stopwords)) {
                continue;
            }
            if (!isset($wordFrequency['doc'][$w])) {
                $wordFrequency['doc'][$w] = 0;
            }
            $wordFrequency['doc'][$w]++;
        }

        // Kata unik di masing-masing teks
        $inputUniqueWords = array_keys($wordFrequency['input']);
        $docUniqueWords = array_keys($wordFrequency['doc']);

        // Kata yang muncul di KEDUA teks (irisan)
        $commonWords = array_intersect($inputUniqueWords, $docUniqueWords);

        foreach ($commonWords as $w) {
            // stopword sudah difilter di atas, tapi kita jaga lagi
            if (!in_array($w, $this->stopwords)) {
                $similarWords[] = $w;
            }
        }

        // Jika tidak ada kata yang mirip, fallback dari k-gram yang match
        if (empty($similarWords) && !empty($matchedKgramStrings)) {
            $similarWords = $this->findWordsFromKgrams(
                $matchedKgramStrings,
                $inputData['words'],
                $docData['words']
            );
        }

        // Hapus duplikat dan sort (terpanjang dulu)
        $similarWords = array_unique($similarWords);
        usort($similarWords, function ($a, $b) {
            return strlen($b) - strlen($a);
        });

        return [
            'similar_words' => array_slice($similarWords, 0, 50),
            'word_frequency' => $wordFrequency,
            'matched_kgrams_count' => $matchedKgramsCount,
            'total_kgrams_input' => count($inputKgrams),
            'total_kgrams_doc' => count($docKgrams),
        ];
    }

    /**
     * Cari kata yang mengandung k-gram
     * (tetap mengabaikan stopword)
     */
    protected function findWordsFromKgrams(array $kgrams, array $inputWords, array $docWords): array
    {
        $words = [];
        $allWords = array_merge($inputWords, $docWords);

        foreach ($kgrams as $kgram) {
            foreach ($allWords as $word) {
                $w = trim($word);
                if (
                    stripos($w, $kgram) !== false &&
                    strlen($w) > 3 &&
                    !in_array($w, $this->stopwords)
                ) {
                    $words[] = $w;
                }
            }
        }

        return array_unique($words);
    }

    public function detectPlagiarism(string $inputText, int $topN = 10): array
    {
        $inputResult = $this->generateFingerprints($inputText);
        $inputFingerprints = $inputResult['fingerprints'];

        if (empty($inputFingerprints)) {
            return [
                'success' => false,
                'error' => 'Teks terlalu pendek atau tidak valid setelah preprocessing',
                'input_analysis' => $inputResult,
                'best_similarity' => 0,
                'results' => []
            ];
        }

        $datasets = Dataset::whereNotNull('fingerprints')
            ->where('fingerprints', '!=', '[]')
            ->get();

        if ($datasets->isEmpty()) {
            $datasets = Dataset::all();
            foreach ($datasets as $dataset) {
                $this->updateDatasetFingerprints($dataset);
            }
            $datasets = $datasets->fresh();
        }

        $comparisons = [];

        foreach ($datasets as $dataset) {
            $docFingerprints = $dataset->fingerprints ?? [];

            if (empty($docFingerprints)) {
                continue;
            }

            $similarity = $this->jaccardSimilarity($inputFingerprints, $docFingerprints);
            $intersection = array_intersect($inputFingerprints, $docFingerprints);

            // Extract kata-kata asli yang mirip
            $similarWordsData = [];
            if ($similarity > 5) { // threshold bisa diatur
                $similarWordsData = $this->extractSimilarWords(
                    $inputText,
                    $dataset->abstrak,
                    array_values($intersection)
                );
            }

            $comparisons[] = [
                'id' => $dataset->id,
                'judul' => $dataset->judul,
                'penulis' => $dataset->penulis ?? '-',
                'tahun' => $dataset->tahun ?? '-',
                'abstrak' => $dataset->abstrak,
                'similarity' => $similarity,
                'input_fingerprints' => count($inputFingerprints),
                'doc_fingerprints' => count($docFingerprints),
                'common_fingerprints' => count($intersection),
                'matched_hashes' => array_values($intersection),

                'similar_words' => $similarWordsData['similar_words'] ?? [],
                'matched_kgrams_count' => $similarWordsData['matched_kgrams_count'] ?? 0,
                'total_kgrams_input' => $similarWordsData['total_kgrams_input'] ?? 0,
                'total_kgrams_doc' => $similarWordsData['total_kgrams_doc'] ?? 0,

                'total_fingerprints_input' => count($inputFingerprints),
                'total_fingerprints_doc' => count($docFingerprints),
            ];
        }

        usort($comparisons, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $topResults = array_slice($comparisons, 0, $topN);
        $bestSimilarity = !empty($topResults) ? $topResults[0]['similarity'] : 0;

        return [
            'success' => true,
            'input_analysis' => [
                'original_length' => strlen($inputText),
                'preprocessed_length' => strlen($inputResult['preprocessed']),
                'kgrams_count' => $inputResult['kgrams_count'],
                'fingerprints_count' => $inputResult['fingerprints_count']
            ],
            'input_text' => $inputText,
            'parameters' => [
                'k_gram' => $this->kGram,
                'window_size' => $this->windowSize,
                'hash_base' => $this->hashBase
            ],
            'best_similarity' => $bestSimilarity,
            'status' => $this->determineStatus($bestSimilarity),
            'total_compared' => count($comparisons),
            'top_results' => $topResults,
            'input_fingerprints' => $inputFingerprints
        ];
    }

    public function updateDatasetFingerprints(Dataset $dataset): Dataset
    {
        $result = $this->generateFingerprints($dataset->abstrak);

        $dataset->update([
            'abstrak_preprocessed' => $result['preprocessed'],
            'fingerprints' => $result['fingerprints'],
            'fingerprint_count' => $result['fingerprints_count']
        ]);

        return $dataset;
    }

    protected function determineStatus(float $similarity): array
    {
        if ($similarity < 25) {
            return [
                'level' => 'low',
                'label' => 'Aman (Kemiripan Rendah)',
                'description' => 'Tidak terindikasi plagiarisme signifikan. Dokumen cukup orisinal.',
                'color' => 'green',
                'icon' => '✅'
            ];
        } elseif ($similarity < 50) {
            return [
                'level' => 'medium',
                'label' => 'Perlu Tinjauan (Sedang)',
                'description' => 'Terdapat beberapa kesamaan frase yang perlu dicek dan direvisi.',
                'color' => 'orange',
                'icon' => '⚠️'
            ];
        } else {
            return [
                'level' => 'high',
                'label' => 'Terindikasi Plagiarisme (Tinggi)',
                'description' => 'Ditemukan tingkat kesamaan yang sangat tinggi. Perlu revisi signifikan.',
                'color' => 'red',
                'icon' => '🚨'
            ];
        }
    }

    public function getDetailedAnalysis(string $text): array
    {
        $steps = [];

        $steps['step1_original'] = [
            'name' => 'Teks Asli',
            'description' => 'Teks input sebelum diproses',
            'data' => $text,
            'length' => strlen($text)
        ];

        $preprocessed = $this->preprocess($text);
        $steps['step2_preprocessing'] = [
            'name' => 'Hasil Preprocessing',
            'description' => 'Teks setelah lowercase, hapus angka, tanda baca, dan stopwords',
            'data' => $preprocessed,
            'length' => strlen($preprocessed)
        ];

        $kgrams = $this->createKGrams($preprocessed);
        $steps['step3_kgrams'] = [
            'name' => 'Pembentukan K-gram',
            'description' => "Teks dipecah menjadi segmen {$this->kGram} karakter",
            'parameter' => "k = {$this->kGram}",
            'data' => array_slice($kgrams, 0, 20),
            'total' => count($kgrams),
            'sample' => count($kgrams) > 20 ? '(menampilkan 20 dari ' . count($kgrams) . ')' : ''
        ];

        $hashes = $this->computeAllHashes($kgrams);
        $steps['step4_hashes'] = [
            'name' => 'Rolling Hash',
            'description' => 'Setiap k-gram dikonversi menjadi nilai hash',
            'formula' => 'H = Σ(char × base^position) mod ' . $this->hashMod,
            'data' => array_slice($hashes, 0, 20),
            'total' => count($hashes)
        ];

        $windows = $this->createWindows($hashes);
        $steps['step5_windows'] = [
            'name' => 'Pembentukan Window',
            'description' => "Hash dikelompokkan ke dalam window berukuran {$this->windowSize}",
            'parameter' => "w = {$this->windowSize}",
            'data' => array_slice($windows, 0, 10),
            'total' => count($windows)
        ];

        $fingerprints = $this->selectFingerprints($windows);
        $steps['step6_fingerprints'] = [
            'name' => 'Pemilihan Fingerprint',
            'description' => 'Nilai hash terkecil dari setiap window dipilih sebagai fingerprint',
            'data' => $fingerprints,
            'total' => count($fingerprints)
        ];

        return [
            'steps' => $steps,
            'summary' => [
                'original_length' => strlen($text),
                'preprocessed_length' => strlen($preprocessed),
                'kgrams_count' => count($kgrams),
                'hashes_count' => count($hashes),
                'windows_count' => count($windows),
                'fingerprints_count' => count($fingerprints)
            ]
        ];
    }
}