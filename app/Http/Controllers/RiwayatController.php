<?php

namespace App\Http\Controllers;

use App\Models\FullText;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    /**
     * Tampilkan daftar riwayat
     */
    public function index(Request $request)
    {
        $query = FullText::query()->orderBy('created_at', 'desc');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal dari
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        // Filter tanggal sampai
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $results = $query->paginate(10)->withQueryString();

        return view('pages.riwayat', compact('results'));
    }

    /**
     * Tampilkan detail riwayat (teks yang diuji)
     */
    public function show($id)
    {
        $result = FullText::findOrFail($id);

        return view('pages.riwayat-show', compact('result'));
    }

    /**
     * Hapus riwayat
     */
    public function destroy($id)
    {
        $result = FullText::findOrFail($id);
        $result->delete();

        return redirect()->route('riwayat.index')
            ->with('success', 'Riwayat berhasil dihapus');
    }
}