@extends('layouts.master')

@section('title', 'Deteksi Plagiarisme')

@section('content')

<div class="app-wrapper">

    <div class="hero">
        <h1>Deteksi Plagiarisme Abstrak</h1>
        <p>Analisis kemiripan teks menggunakan algoritma cerdas untuk pengujian akademik</p>
    </div>
    

    <form method="POST" action="/proses-plagiarisme" enctype="multipart/form-data" class="glass-card">
        @csrf

        <!-- UPLOAD DATASET -->
        <div class="upload-box">
            <label for="dataset" class="upload-area">
                <div class="upload-icon">📂</div>
                <h3>Upload Dataset Pembanding</h3>
                <p>PDF atau TXT • Digunakan sebagai referensi pengecekan</p>
                <input type="file" id="dataset" name="dataset" required>
            </label>
        </div>

        <!-- TEXTAREA ABSTRAK -->
        <div class="text-box">
            <label>📝 Abstrak yang Akan Diuji</label>
            <textarea name="abstrak" placeholder="Tempelkan teks abstrak di sini..."></textarea>
        </div>

        <button type="submit" class="btn-proses">
            🔍 Mulai Analisis
        </button>
    </form>

    @isset($hasil)
    <div class="result-card">
        <h2>Hasil Analisis</h2>
        <div class="result-value">{{ $hasil }}%</div>
        <p>Tingkat kemiripan terhadap dataset</p>
    </div>
    @endisset

</div>

<style>

.app-wrapper{
    max-width:1100px;
    margin:auto;
}

.hero{
    text-align:center;
    margin-bottom:25px;
}

.hero h1{
    font-size:28px;
    color:#1e293b;
}

.hero p{
    color:#64748b;
    margin-top:5px;
}

/* GLASS CARD */
.glass-card{
    backdrop-filter: blur(12px);
    background: rgba(255,255,255,0.75);
    border-radius:16px;
    padding:30px;
    box-shadow:0 8px 30px rgba(0,0,0,0.08);
}

/* UPLOAD */
.upload-area{
    display:block;
    border:2px dashed #3b82f6;
    border-radius:12px;
    text-align:center;
    padding:30px;
    cursor:pointer;
    transition:.3s;
}

.upload-area:hover{
    background:#eff6ff;
}

.upload-area input{
    display:none;
}

.upload-icon{
    font-size:40px;
}

/* TEXTAREA */
.text-box{
    margin-top:25px;
}

textarea{
    width:100%;
    height:180px;
    border-radius:10px;
    border:1px solid #cbd5e1;
    padding:12px;
    margin-top:8px;
    resize:none;
}

/* BUTTON */
.btn-proses{
    margin-top:25px;
    width:100%;
    background:linear-gradient(135deg,#2563eb,#1d4ed8);
    border:none;
    color:white;
    padding:14px;
    border-radius:10px;
    font-size:15px;
    cursor:pointer;
    transition:.3s;
}

.btn-proses:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 15px rgba(37,99,235,.3);
}

/* RESULT */
.result-card{
    margin-top:25px;
    background:white;
    border-radius:14px;
    padding:25px;
    text-align:center;
    box-shadow:0 6px 18px rgba(0,0,0,0.07);
}

.result-value{
    font-size:42px;
    color:#2563eb;
    font-weight:bold;
}

</style>

@endsection
