<?php
// app/Http/Controllers/DatasetController.php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Services\WinnowingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DatasetController extends Controller
{
    protected WinnowingService $winnowingService;

    public function __construct(WinnowingService $winnowingService)
    {
        $this->winnowingService = $winnowingService;
    }

    /**
     * Tampilkan daftar dataset
     */
    public function index(Request $request)
    {
        $query = Dataset::query();

        // Filter pencarian
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter tahun
        if ($request->filled('tahun')) {
            $query->byYear($request->tahun);
        }

        // Sorting
        $sortBy = $request->get('sort', 'created_at');
        $sortDir = $request->get('dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $datasets = $query->paginate(10);

        // Get unique years for filter
        $years = Dataset::selectRaw('DISTINCT tahun')
                        ->whereNotNull('tahun')
                        ->orderBy('tahun', 'desc')
                        ->pluck('tahun');

        return view('pages.dataset', compact('datasets', 'years'));
    }

    /**
     * Form tambah dataset
     */
    public function create()
    {
        return view('pages.upload-dataset');
    }

    /**
     * Simpan dataset baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:500',
            'abstrak' => 'required|string|min:50',
            'penulis' => 'nullable|string|max:255',
            'tahun' => 'nullable|string|max:4',
            'prodi' => 'nullable|string|max:100'
        ], [
            'judul.required' => 'Judul wajib diisi',
            'abstrak.required' => 'Abstrak wajib diisi',
            'abstrak.min' => 'Abstrak minimal 50 karakter'
        ]);

        try {
            // Buat dataset
            $dataset = Dataset::create($validated);

            // Generate fingerprints menggunakan Winnowing
            $this->winnowingService->updateDatasetFingerprints($dataset);

            return redirect()
                ->route('dataset.index')
                ->with('success', 'Dataset berhasil disimpan dan fingerprint sudah digenerate!');

        } catch (\Exception $e) {
            Log::error('Error storing dataset: ' . $e->getMessage());
            
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal menyimpan dataset: ' . $e->getMessage());
        }
    }

    /**
     * Detail dataset
     */
    public function show($id)
    {
        $dataset = Dataset::findOrFail($id);

        // Get detailed analysis
        $analysis = $this->winnowingService->getDetailedAnalysis($dataset->abstrak);

        return view('pages.dataset-detail', compact('dataset', 'analysis'));
    }

    /**
     * Hapus dataset
     */
    public function destroy($id)
    {
        $dataset = Dataset::findOrFail($id);
        $dataset->delete();

        return redirect()
            ->back()
            ->with('success', 'Dataset berhasil dihapus.');
    }

    /**
     * Regenerate fingerprints untuk semua dataset
     */
    public function regenerateFingerprints()
    {
        $datasets = Dataset::all();
        $count = 0;

        foreach ($datasets as $dataset) {
            $this->winnowingService->updateDatasetFingerprints($dataset);
            $count++;
        }

        return redirect()
            ->back()
            ->with('success', "Berhasil regenerate fingerprint untuk {$count} dataset.");
    }

    /**
     * Import dataset dari file (CSV/Excel)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx|max:10240'
        ]);

        // TODO: Implement import logic
        
        return redirect()
            ->back()
            ->with('success', 'Dataset berhasil diimport.');
    }
}