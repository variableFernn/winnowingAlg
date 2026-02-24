<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\DetectionResult;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_dataset' => Dataset::count(),
            'total_deteksi' => DetectionResult::count(),
            'deteksi_hari_ini' => DetectionResult::whereDate('created_at', today())->count(),
            'rata_rata_similarity' => DetectionResult::avg('highest_similarity') ?? 0,
        ];

        // Statistik status
        $statusStats = [
            'low' => DetectionResult::where('status', 'low')->count(),
            'medium' => DetectionResult::where('status', 'medium')->count(),
            'high' => DetectionResult::where('status', 'high')->count(),
        ];

        // Deteksi terbaru
        $recentDetections = DetectionResult::orderBy('created_at', 'desc')
                                           ->take(5)
                                           ->get();

        return view('pages.home', compact('stats', 'statusStats', 'recentDetections'));
    }
}