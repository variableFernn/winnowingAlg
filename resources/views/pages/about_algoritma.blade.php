{{-- resources/views/pages/deteksi.blade.php --}}
@extends('layouts.master')
@section('title', 'Deteksi Plagiarisme')
@section('content')
<!-- <div class="page-header">
    <h1 class="page-title">🔎 Deteksi Plagiarisme</h1>
    <p class="page-subtitle">
        Bandingkan teks abstrak dengan {{ $datasetCount ?? 0 }} dokumen di database menggunakan Algoritma Winnowing
    </p>
</div> -->

<style>
    .winnowing-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .hero-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 60px 40px;
        color: white;
        text-align: center;
        margin-bottom: 40px;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 50px 50px;
        animation: moveBackground 20s linear infinite;
    }

    @keyframes moveBackground {
        0% { transform: translate(0, 0); }
        100% { transform: translate(50px, 50px); }
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-section h2 {
        font-size: 2.5em;
        margin-bottom: 15px;
        animation: fadeInDown 0.8s ease;
    }

    .hero-section p {
        font-size: 1.2em;
        opacity: 0.95;
        animation: fadeInUp 0.8s ease;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .info-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }

    .info-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .info-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        border-color: #667eea;
    }

    .info-card .icon {
        font-size: 3em;
        margin-bottom: 15px;
        animation: bounce 2s infinite;
    }

    .info-card:nth-child(1) .icon { animation-delay: 0s; }
    .info-card:nth-child(2) .icon { animation-delay: 0.3s; }
    .info-card:nth-child(3) .icon { animation-delay: 0.6s; }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    .info-card h3 {
        color: #333;
        margin-bottom: 10px;
        font-size: 1.4em;
    }

    .info-card p {
        color: #666;
        line-height: 1.6;
    }

    .demo-section {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }

    .demo-section h3 {
        font-size: 1.8em;
        color: #333;
        margin-bottom: 30px;
        text-align: center;
    }

    .algorithm-steps {
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .step {
        display: flex;
        gap: 20px;
        align-items: flex-start;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        border-left: 4px solid #667eea;
        opacity: 0;
        animation: slideIn 0.5s ease forwards;
    }

    .step:nth-child(1) { animation-delay: 0.2s; }
    .step:nth-child(2) { animation-delay: 0.4s; }
    .step:nth-child(3) { animation-delay: 0.6s; }
    .step:nth-child(4) { animation-delay: 0.8s; }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .step-number {
        flex-shrink: 0;
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.3em;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .step-content h4 {
        color: #333;
        margin-bottom: 10px;
        font-size: 1.2em;
    }

    .step-content p {
        color: #666;
        line-height: 1.6;
    }

    .visual-demo {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        border-radius: 15px;
        padding: 40px;
        margin-bottom: 40px;
        overflow: hidden;
    }

    .visual-demo h3 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
        font-size: 1.8em;
    }

    .text-example {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-family: 'Courier New', monospace;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .hash-animation {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }

    .hash-block {
        background: #667eea;
        color: white;
        padding: 10px 15px;
        border-radius: 8px;
        font-family: 'Courier New', monospace;
        animation: popIn 0.5s ease;
        box-shadow: 0 3px 10px rgba(102, 126, 234, 0.3);
    }

    @keyframes popIn {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .window-demo {
        display: flex;
        gap: 5px;
        margin-bottom: 20px;
        overflow-x: auto;
        padding: 20px;
        background: white;
        border-radius: 10px;
    }

    .window-item {
        min-width: 60px;
        height: 60px;
        background: #e0e7ff;
        border: 2px solid #667eea;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #667eea;
        transition: all 0.3s ease;
    }

    .window-item.active {
        background: #667eea;
        color: white;
        transform: scale(1.1);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .window-item.selected {
        background: #10b981;
        border-color: #10b981;
        color: white;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .fingerprint-result {
        background: white;
        padding: 25px;
        border-radius: 10px;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }

    .fingerprint-result h4 {
        color: #333;
        margin-bottom: 15px;
    }

    .fingerprint-items {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .fingerprint-item {
        background: #10b981;
        color: white;
        padding: 12px 18px;
        border-radius: 20px;
        font-family: 'Courier New', monospace;
        font-weight: bold;
        animation: fadeInScale 0.5s ease;
        box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .cta-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 50px 40px;
        text-align: center;
        color: white;
        box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
    }

    .cta-section h3 {
        font-size: 2em;
        margin-bottom: 15px;
    }

    .cta-section p {
        font-size: 1.1em;
        margin-bottom: 30px;
        opacity: 0.95;
    }

    .btn-start {
        display: inline-block;
        background: white;
        color: #667eea;
        padding: 15px 40px;
        border-radius: 30px;
        font-weight: bold;
        font-size: 1.1em;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 5px 20px rgba(255,255,255,0.3);
    }

    .btn-start:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(255,255,255,0.4);
        color: #667eea;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 40px 20px;
        }

        .hero-section h2 {
            font-size: 1.8em;
        }

        .info-cards {
            grid-template-columns: 1fr;
        }

        .demo-section {
            padding: 20px;
        }

        .visual-demo {
            padding: 20px;
        }
    }
</style>

<div class="winnowing-container">
    <!-- Hero Section -->
    <div class="hero-section">
        <div class="hero-content">
            <h2>🎯 Algoritma Winnowing</h2>
            <p>Teknik canggih untuk mendeteksi plagiarisme dengan fingerprinting dokumen</p>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="info-cards">
        <div class="info-card">
            <div class="icon">⚡</div>
            <h3>Cepat & Efisien</h3>
            <p>Memproses dokumen dalam hitungan detik dengan kompleksitas waktu O(n)</p>
        </div>
        <div class="info-card">
            <div class="icon">🎯</div>
            <h3>Akurat</h3>
            <p>Mendeteksi kesamaan teks bahkan dengan modifikasi kecil seperti mengganti kata</p>
        </div>
        <div class="info-card">
            <div class="icon">🔒</div>
            <h3>Robust</h3>
            <p>Tahan terhadap manipulasi seperti penambahan spasi atau perubahan format</p>
        </div>
    </div>

    <!-- Algorithm Steps -->
    <div class="demo-section">
        <h3>📝 Cara Kerja Algoritma Winnowing</h3>
        <div class="algorithm-steps">
            <div class="step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <h4>Preprocessing Text</h4>
                    <p>Teks dibersihkan dari karakter khusus, diubah ke huruf kecil, dan dipecah menjadi token (kata-kata). Proses ini memastikan konsistensi dalam perbandingan.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <h4>K-gram Generation</h4>
                    <p>Membuat k-gram dari token (misalnya k=5). Setiap k-gram adalah serangkaian 5 kata berurutan yang membentuk "sliding window" sepanjang teks.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <h4>Hash Calculation</h4>
                    <p>Setiap k-gram diubah menjadi nilai hash numerik unik menggunakan fungsi hash. Ini mengubah teks menjadi representasi angka yang mudah dibandingkan.</p>
                </div>
            </div>
            <div class="step">
                <div class="step-number">4</div>
                <div class="step-content">
                    <h4>Winnowing Selection</h4>
                    <p>Dari sekumpulan hash dalam window tertentu, dipilih nilai hash terkecil sebagai "fingerprint". Ini menghasilkan signature unik untuk dokumen.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Visual Demo -->
    <div class="visual-demo">
        <h3>🎨 Visualisasi Winnowing</h3>
        
        <div class="text-example">
            <strong>Contoh Teks:</strong><br>
            "Algoritma winnowing adalah teknik untuk mendeteksi plagiarisme secara efisien"
        </div>

        <div class="hash-animation" id="hashContainer">
            <!-- Hash blocks will be dynamically added -->
        </div>

        <div class="window-demo" id="windowContainer">
            <!-- Window items will be dynamically added -->
        </div>

        <div class="fingerprint-result">
            <h4>✨ Fingerprint Hasil:</h4>
            <div class="fingerprint-items" id="fingerprintContainer">
                <!-- Fingerprint items will be dynamically added -->
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="cta-section">
        <h3>Siap Mencoba?</h3>
        <p>Mulai deteksi plagiarisme dokumen Anda sekarang dengan teknologi Winnowing</p>
        <a href="#" class="btn-start" onclick="scrollToForm(); return false;">Mulai Deteksi →</a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Simulate winnowing process with animation
    const exampleHashes = [234, 156, 423, 189, 567, 234, 678, 345, 123, 456];
    const windowSize = 4;
    
    // Add hash blocks with animation
    const hashContainer = document.getElementById('hashContainer');
    exampleHashes.forEach((hash, index) => {
        setTimeout(() => {
            const block = document.createElement('div');
            block.className = 'hash-block';
            block.textContent = hash;
            block.style.animationDelay = `${index * 0.1}s`;
            hashContainer.appendChild(block);
        }, index * 200);
    });

    // Animate window selection
    setTimeout(() => {
        const windowContainer = document.getElementById('windowContainer');
        exampleHashes.forEach((hash, index) => {
            const item = document.createElement('div');
            item.className = 'window-item';
            item.textContent = hash;
            item.dataset.index = index;
            windowContainer.appendChild(item);
        });

        // Simulate window movement and selection
        let currentWindow = 0;
        const animateWindow = setInterval(() => {
            // Clear previous active states
            document.querySelectorAll('.window-item').forEach(item => {
                item.classList.remove('active', 'selected');
            });

            // Highlight current window
            for (let i = currentWindow; i < currentWindow + windowSize && i < exampleHashes.length; i++) {
                document.querySelectorAll('.window-item')[i].classList.add('active');
            }

            // Find minimum in window and mark as selected
            let minIndex = currentWindow;
            let minValue = exampleHashes[currentWindow];
            for (let i = currentWindow; i < currentWindow + windowSize && i < exampleHashes.length; i++) {
                if (exampleHashes[i] < minValue) {
                    minValue = exampleHashes[i];
                    minIndex = i;
                }
            }
            document.querySelectorAll('.window-item')[minIndex].classList.add('selected');

            currentWindow++;
            if (currentWindow > exampleHashes.length - windowSize) {
                clearInterval(animateWindow);
                
                // Show final fingerprints
                setTimeout(() => {
                    const fingerprints = [123, 156, 189, 234, 345];
                    const fingerprintContainer = document.getElementById('fingerprintContainer');
                    fingerprints.forEach((fp, index) => {
                        setTimeout(() => {
                            const item = document.createElement('div');
                            item.className = 'fingerprint-item';
                            item.textContent = fp;
                            fingerprintContainer.appendChild(item);
                        }, index * 300);
                    });
                }, 500);
            }
        }, 1500);
    }, exampleHashes.length * 200 + 500);

    // Form submission handling
    const form = document.getElementById('deteksiForm');
    const overlay = document.getElementById('loadingOverlay');
    if (form) {
        form.addEventListener('submit', function() {
            if (overlay) {
                overlay.style.display = 'flex';
            }
        });
    }
});

function scrollToForm() {
    // If you have a form on the page, scroll to it
    // Otherwise, you can redirect to the detection page
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'smooth'
    });
}
</script>
@endpush
@endsection