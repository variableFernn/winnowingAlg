@extends('layouts.master')

@section('title', 'Register - Plagiarism Detector')

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

    .auth-page::before {
        content: '';
        position: fixed;
        top: -20%;
        right: -10%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.13) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        animation: drift1 14s ease-in-out infinite alternate;
    }

    .auth-page::after {
        content: '';
        position: fixed;
        bottom: -20%;
        left: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        animation: drift2 18s ease-in-out infinite alternate;
    }

    @keyframes drift1 {
        from { transform: translate(0, 0) scale(1); }
        to { transform: translate(-40px, 30px) scale(1.12); }
    }

    @keyframes drift2 {
        from { transform: translate(0, 0) scale(1); }
        to { transform: translate(30px, -40px) scale(1.1); }
    }

    .grid-bg {
        position: fixed;
        inset: 0;
        background-image: 
            linear-gradient(rgba(139, 92, 246, 0.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(139, 92, 246, 0.035) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
    }

    .auth-wrapper {
        width: 100%;
        max-width: 480px;
        position: relative;
        z-index: 10;
        animation: slideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
        transform: translateY(30px);
    }

    @keyframes slideUp {
        to { opacity: 1; transform: translateY(0); }
    }

    .auth-brand {
        text-align: center;
        margin-bottom: 36px;
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
        margin-bottom: 14px;
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

    .brand-name span { color: #818cf8; }

    .auth-card {
        background: rgba(255,255,255,0.04);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 24px;
        padding: 38px 44px;
        backdrop-filter: blur(20px);
        box-shadow: 
            0 0 0 1px rgba(99,102,241,0.05),
            0 40px 80px rgba(0,0,0,0.4),
            inset 0 1px 0 rgba(255,255,255,0.06);
        animation: slideUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) 0.15s forwards;
        opacity: 0;
        transform: translateY(20px);
    }

    .card-header-text {
        margin-bottom: 4px;
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
    }

    .divider-line {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.07), transparent);
        margin: 26px 0;
    }

    /* Steps indicator */
    .step-dots {
        display: flex;
        gap: 6px;
        margin-bottom: 26px;
    }

    .step-dot {
        height: 3px;
        border-radius: 3px;
        background: rgba(255,255,255,0.1);
        flex: 1;
        transition: all 0.4s;
    }

    .step-dot.active {
        background: #6366f1;
        box-shadow: 0 0 8px rgba(99,102,241,0.5);
    }

    /* Alert */
    .alert-custom {
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13.5px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 400;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #fca5a5;
    }

    /* Form */
    .form-group {
        margin-bottom: 18px;
    }

    .form-label {
        display: block;
        font-size: 12.5px;
        font-weight: 500;
        color: #94a3b8;
        margin-bottom: 8px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .input-wrap {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #475569;
        pointer-events: none;
        transition: color 0.25s;
        display: flex;
    }

    .input-icon svg {
        width: 15px;
        height: 15px;
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
        padding: 13px 14px 13px 42px;
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        color: #e2e8f0;
        outline: none;
        transition: all 0.25s ease;
        -webkit-appearance: none;
    }

    .form-input::placeholder { color: #2d3a4a; }

    .form-input:focus {
        background: rgba(99, 102, 241, 0.06);
        border-color: rgba(99, 102, 241, 0.45);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        color: #f1f5f9;
    }

    .form-input:focus ~ .input-icon,
    .input-wrap:focus-within .input-icon {
        color: #818cf8;
    }

    /* Password strength */
    .pass-strength {
        margin-top: 8px;
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .strength-bar {
        height: 3px;
        flex: 1;
        border-radius: 3px;
        background: rgba(255,255,255,0.07);
        transition: all 0.35s ease;
    }

    .strength-bar.weak { background: #ef4444; }
    .strength-bar.medium { background: #f59e0b; }
    .strength-bar.strong { background: #10b981; }

    .strength-label {
        font-size: 11px;
        color: #475569;
        min-width: 40px;
        text-align: right;
    }

    .error-msg {
        font-size: 12px;
        color: #f87171;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Row layout */
    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    /* Submit */
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
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-submit::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.12) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.25s;
    }

    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 30px rgba(99, 102, 241, 0.45);
    }

    .btn-submit:hover::before { opacity: 1; }
    .btn-submit:active { transform: translateY(0); }

    .btn-submit svg {
        width: 16px;
        height: 16px;
        stroke: #fff;
        fill: none;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    /* Terms */
    .terms-note {
        font-size: 12px;
        color: #334155;
        text-align: center;
        margin-top: 14px;
        line-height: 1.6;
    }

    .terms-note a {
        color: #6366f1;
        text-decoration: none;
    }

    /* Footer */
    .auth-footer {
        text-align: center;
        margin-top: 26px;
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

    @media (max-width: 520px) {
        .auth-card { padding: 30px 24px; }
        .form-row-2 { grid-template-columns: 1fr; gap: 0; }
    }
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
                <h2>Buat Akun Baru</h2>
                <p>Daftar gratis dan mulai deteksi plagiarisme</p>
            </div>

            <div class="step-dots">
                <div class="step-dot active"></div>
                <div class="step-dot active"></div>
                <div class="step-dot active"></div>
                <div class="step-dot"></div>
            </div>

            @if(session('error'))
                <div class="alert-custom alert-danger">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input type="text"
                               class="form-input"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               placeholder="Nama lengkap Anda"
                               autofocus>
                    </div>
                    @error('name')
                        <div class="error-msg">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Email</label>
                    <div class="input-wrap">
                        <span class="input-icon">
                            <svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="3"/><polyline points="2,4 12,13 22,4"/></svg>
                        </span>
                        <input type="email"
                               class="form-input"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com">
                    </div>
                    @error('email')
                        <div class="error-msg">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </span>
                            <input type="password"
                                   class="form-input"
                                   id="password"
                                   name="password"
                                   placeholder="Min. 6 karakter"
                                   oninput="checkStrength(this.value)">
                        </div>
                        <div class="pass-strength" id="strengthBar">
                            <div class="strength-bar" id="s1"></div>
                            <div class="strength-bar" id="s2"></div>
                            <div class="strength-bar" id="s3"></div>
                            <span class="strength-label" id="sLabel"></span>
                        </div>
                        @error('password')
                            <div class="error-msg">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Konfirmasi</label>
                        <div class="input-wrap">
                            <span class="input-icon">
                                <svg viewBox="0 0 24 24"><path d="M9 12l2 2 4-4"/><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                            </span>
                            <input type="password"
                                   class="form-input"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   placeholder="Ulangi password">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                    Buat Akun Saya
                </button>

                <p class="terms-note">
                    Dengan mendaftar, Anda menyetujui <a href="#">Syarat & Ketentuan</a> kami
                </p>
            </form>
        </div>

        <div class="auth-footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
        </div>

    </div>
</div>

<script>
function checkStrength(val) {
    const s1 = document.getElementById('s1');
    const s2 = document.getElementById('s2');
    const s3 = document.getElementById('s3');
    const lbl = document.getElementById('sLabel');

    const hasUpper = /[A-Z]/.test(val);
    const hasNum = /[0-9]/.test(val);
    const hasSpecial = /[^A-Za-z0-9]/.test(val);

    s1.className = 'strength-bar';
    s2.className = 'strength-bar';
    s3.className = 'strength-bar';
    lbl.textContent = '';

    if (!val) return;

    if (val.length >= 6) {
        s1.classList.add('weak');
        lbl.textContent = 'Lemah';
        lbl.style.color = '#ef4444';
    }
    if (val.length >= 8 && (hasUpper || hasNum)) {
        s2.classList.add('medium');
        lbl.textContent = 'Sedang';
        lbl.style.color = '#f59e0b';
    }
    if (val.length >= 10 && hasUpper && hasNum && hasSpecial) {
        s3.classList.add('strong');
        lbl.textContent = 'Kuat';
        lbl.style.color = '#10b981';
    }
}
</script>

@endsection