@extends('layouts.app')

@section('content')
<style>
    body {
        background: var(--soft-bg);
        margin: 0;
        height: 100vh;
        overflow: hidden;
    }

    .login-container {
        display: flex;
        height: 100vh;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    /* Animated background blobs */
    .bg-blob {
        position: fixed;
        border-radius: 50%;
        z-index: 0;
        filter: blur(80px);
        opacity: 0.5;
    }
    .blob-1 { 
        width: 500px; height: 500px;
        background: rgba(30, 136, 229, 0.12);
        top: -15%; left: -10%;
        animation: float1 8s ease-in-out infinite;
    }
    .blob-2 { 
        width: 400px; height: 400px;
        background: rgba(139, 92, 246, 0.08);
        bottom: -10%; right: -5%;
        animation: float2 10s ease-in-out infinite;
    }
    .blob-3 { 
        width: 300px; height: 300px;
        background: rgba(16, 185, 129, 0.06);
        top: 50%; left: 60%;
        animation: float3 12s ease-in-out infinite;
    }

    @keyframes float1 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(40px, 30px); } }
    @keyframes float2 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(-30px, -40px); } }
    @keyframes float3 { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(20px, -20px); } }

    .login-card {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 420px;
        padding: 20px;
    }

    .card-inner {
        background: var(--card-bg);
        padding: 45px 40px;
        border-radius: 28px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.06);
        border: 1px solid var(--border-color);
        backdrop-filter: blur(20px);
        animation: cardSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @keyframes cardSlideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .brand-header {
        text-align: center;
        margin-bottom: 35px;
    }
    .brand-icon {
        width: 56px;
        height: 56px;
        background: var(--primary-gradient);
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 24px;
        color: white;
        font-weight: 900;
        box-shadow: 0 8px 24px rgba(30, 136, 229, 0.3);
    }
    .brand-title {
        font-size: 26px;
        font-weight: 900;
        letter-spacing: -1.5px;
        color: var(--text-main);
    }
    .brand-subtitle {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 600;
        margin-top: 6px;
    }

    .form-group { margin-bottom: 18px; }
    .form-group label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        padding-left: 4px;
    }

    .login-input {
        width: 100%;
        padding: 14px 18px;
        border-radius: 14px;
        border: 1.5px solid var(--border-color);
        background: var(--soft-bg);
        font-size: 14px;
        font-weight: 600;
        color: var(--text-main);
        transition: all 0.25s ease;
        box-sizing: border-box;
        outline: none;
        font-family: inherit;
    }
    .login-input:focus {
        border-color: #1E88E5;
        box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.1);
    }
    .login-input::placeholder { color: var(--text-muted); font-weight: 500; }

    .btn-login {
        width: 100%;
        padding: 15px;
        border-radius: 14px;
        background: var(--primary-gradient);
        color: white;
        border: none;
        font-size: 15px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-top: 8px;
        box-shadow: 0 6px 20px rgba(30, 136, 229, 0.3);
        font-family: inherit;
    }
    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(30, 136, 229, 0.4);
    }
    .btn-login:active { transform: translateY(0); }

    .auth-footer {
        margin-top: 30px;
        text-align: center;
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 600;
    }
    .auth-footer a {
        color: #1E88E5;
        font-weight: 800;
        text-decoration: none;
        margin-left: 4px;
    }
    .auth-footer a:hover { text-decoration: underline; }

    .alert {
        padding: 14px;
        border-radius: 14px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 22px;
        text-align: center;
    }
    .alert-error {
        background: rgba(239, 68, 68, 0.08);
        color: #EF4444;
        border: 1px solid rgba(239, 68, 68, 0.1);
    }
    .alert-success {
        background: rgba(16, 185, 129, 0.08);
        color: #10B981;
        border: 1px solid rgba(16, 185, 129, 0.1);
    }
</style>

<div class="bg-blob blob-1"></div>
<div class="bg-blob blob-2"></div>
<div class="bg-blob blob-3"></div>

<div class="login-container">
    <div class="login-card">
        <div class="card-inner">
            <div class="brand-header">
                <div class="brand-icon">S</div>
                <div class="brand-title">Selamat Datang</div>
                <div class="brand-subtitle">Masuk ke ScheApp Pro untuk melanjutkan</div>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <form action="/login" method="POST" autocomplete="off">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="login-input" required placeholder="name@example.com" value="{{ old('email') }}" autocomplete="email">
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="login-input" required placeholder="••••••••" autocomplete="current-password">
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <div class="auth-footer">
                Belum punya akun? <a href="/register">Daftar sekarang</a>
            </div>
        </div>
    </div>
</div>
@endsection