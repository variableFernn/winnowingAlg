@extends('layouts.master')

@section('title', 'Login - Plagiarism Detector')

@section('content')

<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap');

    * { box-sizing: border-box; margin: 0; padding: 0; }

    .auth-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'DM Sans', sans-serif;
        background: #09090f;
        position: relative;
        overflow: hidden;
        padding: 40px 20px;
    }

    /* Ambient background orbs */
    .auth-page::before {
        content: '';
        position: fixed;
        top: -20%;
        left: -10%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        animation: drift1 12s ease-in-out infinite alternate;
    }

    .auth-page::after {
        content: '';
        position: fixed;
        bottom: -20%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(236, 72, 153, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        animation: drift2 15s ease-in-out infinite alternate;
    }

    @keyframes drift1 {
        from { transform: translate(0, 0) scale(1); }
        to { transform: translate(40px, 30px) scale(1.1); }
    }

    @keyframes drift2 {
        from { transform: translate(0, 0) scale(1); }
        to { transform: translate(-30px, -40px) scale(1.15); }
    }

    /* Grid texture */
    .grid-bg {
        position: fixed;
        inset: 0;
        background-image: 
            linear-gradient(rgba(99, 102, 241, 0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(99, 102, 241, 0.04) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
    }

    .auth-wrapper {
        width: 100%;
        max-width: 460px;
        position: relative;
        z-index: 10;
        animation: slideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    @keyframes slideUp {
        to { opacity: 1; transform: translateY(0); }
    }

    /* Logo / Brand */
    .auth-brand {
        text-align: center;
        margin-bottom: 40px;
        animation: slideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.1s forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    .brand-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border-radius: 14px;
        margin-bottom: 16px;
        box-shadow: 0 0 0 1px rgba(99,102,241,0.3), 0 20px 40px rgba(99,102,241,0.25);
    }

    .brand-icon svg {
        width: 26px;
        height: 26px;
        fill: none;
        stroke: #fff;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .brand-name {
        font-family: 'Syne', sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        letter-spacing: -0.5px;
    }

    .brand-name span {
        color: #818cf8;
    }

    /* Card */
    .auth-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 24px;
        padding: 40px 44px;
        backdrop-filter: blur(20px);
        box-shadow: 
            0 0 0 1px rgba(99,102,241,0.05),
            0 40px 80px rgba(0,0,0,0.4),
            inset 0 1px 0 rgba(255,255,255,0.06);
        animation: slideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    .card-header-text h2 {
        font-family: 'Syne', sans-serif;
        font-size: 26px;
        font-weight: 700;
        color: #f1f5f9;
        letter-spacing: -0.5px;
        margin-bottom: 6px;
    }

    .card-header-text p {
        color: #64748b;
        font-size: 14px;
        font-weight: 300;
        letter-spacing: 0.1px;
    }

    .divider-line {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.07), transparent);
        margin: 28px 0;
    }

    /* Alerts */
    .alert-custom {
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13.5px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 400;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #6ee7b7;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #fca5a5;
    }

    /* Form */
    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #94a3b8;
        margin-bottom: 8px;
        letter-spacing: 0.3px;
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #475569;
        pointer-events: none;
        transition: color 0.2s;
    }

    .input-icon svg {
        width: 16px;
        height: 16px;
        stroke: currentColor;
        fill: none;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .form-input {
        width: 100%;
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 12px;
        padding: 13px 16px 13px 44px;
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        color: #e2e8f0;
        outline: none;
        transition: all 0.25s ease;
        -webkit-appearance: none;
    }

    .form-input::placeholder {
        color: #334155;
    }

    .form-input:focus {
        background: rgba(99, 102, 241, 0.06);
        border-color: rgba(99, 102, 241, 0.5);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        color: #f1f5f9;
    }

    .form-input:focus + .input-focus-line {
        width: 100%;
    }

    .form-input:focus ~ .input-icon,
    .input-wrap:focus-within .input-icon {
        color: #818cf8;
    }

    .error-msg {
        font-size: 12px;
        color: #f87171;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Remember + Forgot */
    .form-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 28px;
    }

    .check-label {
        display: flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        font-size: 13.5px;
        color: #64748b;
        user-select: none;
    }

    .check-label input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 16px;
        height: 16px;
        border: 1.5px solid rgba(255,255,255,0.15);
        border-radius: 5px;
        background: rgba(255,255,255,0.04);
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        flex-shrink: 0;
    }

    .check-label input[type="checkbox"]:checked {
        background: #6366f1;
        border-color: #6366f1;
    }

    .check-label input[type="checkbox"]:checked::after {
        content: '';
        position: absolute;
        top: 2px; left: 4px;
        width: 5px; height: 8px;
        border: 1.5px solid #fff;
        border-top: none;
        border-left: none;
        transform: rotate(40deg);
    }

    .forgot-link {
        font-size: 13px;
        color: #6366f1;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .forgot-link:hover { color: #818cf8; }

    /* Submit button */
    .btn-submit {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        border: none;
        border-radius: 12px;
        font-family: 'Syne', sans-serif;
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        cursor: pointer;
        letter-spacing: 0.2px;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(99, 102, 241, 0.35);
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        opacity: 0;
        transition: opacity 0.25s;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 30px rgba(99, 102, 241, 0.45);
    }

    .btn-submit:hover::before { opacity: 1; }

    .btn-submit:active {
        transform: translateY(0);
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    /* Footer link */
    .auth-footer {
        text-align: center;
        margin-top: 28px;
        font-size: 13.5px;
        color: #475569;
        animation: slideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.25s forwards;
        opacity: 0;
        transform: translateY(10px);
    }

    .auth-footer a {
        color: #818cf8;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .auth-footer a:hover { color: #a5b4fc; }

    /* Hide sidebar */
    .sidebar { display: none !important; }
    .main-content { margin-left: 0 !important; }
    body { background: #09090f !important; }
    .navbar, header, nav:not(.auth-nav) { display: none !important; }
</style>

<div class="auth-page">
    <div class="grid-bg"></div>

    <div class="auth-wrapper">

        <div class="auth-brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24">
                    <path d="M9 12h6M9 16h6M9 8h6M5 4h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V6a2 2 0 012-2z"/>
                    <path d="M15 2v4H9V2"/>
                </svg>
            </div>
            <div class="brand-name">Plagiarism<span>Scan</span></div>
        </div>

        <div class="auth-card">
            <div class="card-header-text">
                <h2>Selamat Datang</h2>
                <p>Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <div class="divider-line"></div>

            @if(session('success'))
                <div class="alert-custom alert-success">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-custom alert-danger">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="3"/><polyline points="2,4 12,13 22,4"/></svg>
                        </span>
                        <input type="email" name="email" class="form-input"
                               value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                    </div>
                    @error('email')
                        <div class="error-msg">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        </span>
                        <input type="password" name="password" class="form-input"
                               placeholder="Masukkan password" required>
                    </div>
                    @error('password')
                        <div class="error-msg">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-row">
                    <label class="check-label">
                        <input type="checkbox" name="remember" id="remember">
                        Ingat saya
                    </label>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>

                <button type="submit" class="btn-submit">
                    Masuk Sekarang
                </button>
            </form>
        </div>

        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a>
        </div>

    </div>
</div>

@endsection