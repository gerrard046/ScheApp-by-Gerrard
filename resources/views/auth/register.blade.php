@extends('layouts.app')

@section('content')
<style>
    body {
        /* Animated gradient background */
        background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
        background-size: 400% 400%;
        animation: gradientBG 15s ease infinite;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        margin: 0;
        height: 100vh;
        overflow: hidden;
    }

    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Decorative circles behind the glass */
    .shape {
        position: absolute;
        filter: blur(60px);
        z-index: 0;
    }
    .shape:nth-child(1) {
        height: 400px;
        width: 400px;
        background: rgba(255, 255, 255, 0.4);
        top: -100px;
        left: -100px;
        border-radius: 50%;
    }
    .shape:nth-child(2) {
        height: 500px;
        width: 500px;
        background: rgba(255, 255, 255, 0.3);
        bottom: -150px;
        right: -100px;
        border-radius: 50%;
    }

    .register-container {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        position: relative;
        z-index: 1;
        padding: 20px;
    }

    /* Glassmorphism Card */
    .glass-card {
        width: 100%;
        max-width: 420px;
        padding: 30px 35px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        color: white;
        transition: transform 0.3s ease;
    }
    
    .glass-card:hover {
        transform: translateY(-5px);
    }

    .glass-card h2 {
        text-align: center;
        margin: 0 0 10px 0;
        font-size: 32px;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .glass-card p {
        text-align: center;
        margin-bottom: 25px;
        font-size: 15px;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 300;
    }

    /* Alerts */
    .alert {
        padding: 12px 15px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 14px;
        text-align: center;
        backdrop-filter: blur(10px);
    }

    .alert-error {
        background: rgba(231, 76, 60, 0.2);
        border: 1px solid rgba(231, 76, 60, 0.5);
        color: #fff;
    }

    /* Form Fields */
    .form-group {
        margin-bottom: 12px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 5px;
        font-size: 13px;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        color: white;
        font-size: 14px;
        outline: none;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    
    .form-input::placeholder {
        color: rgba(255, 255, 255, 0.6);
    }

    .form-input:focus {
        background: rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
    }

    /* Button */
    .btn-submit {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        border-radius: 12px;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-top: 10px;
    }

    .btn-submit:hover {
        background: rgba(255, 255, 255, 0.25);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }

    /* Footer / Divider */
    .divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.2);
        margin: 20px 0;
    }

    .auth-footer {
        text-align: center;
    }

    .auth-footer p {
        margin-bottom: 10px;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.8);
    }

    .btn-login {
        display: inline-block;
        padding: 10px 20px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        text-decoration: none;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: #fff;
    }
</style>

<div class="shape"></div>
<div class="shape"></div>

<div class="register-container">
    <div class="glass-card">
        <h2>Daftar ScheApp</h2>
        <p>Gabung dan kelola jadwalmu secara cerdas.</p>

        @if($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/register" method="POST">
            @csrf
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-input" required placeholder="Masukkan nama..." value="{{ old('name') }}">
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-input" required placeholder="Masukkan email..." value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-input" required placeholder="Minimal 8 karakter...">
            </div>

            <div class="form-group">
                <label>Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-input" required placeholder="Ulangi password...">
            </div>

            <button type="submit" class="btn-submit">
                Daftar Sekarang
            </button>
        </form>

        <div class="divider"></div>

        <div class="auth-footer">
            <p>Sudah punya akun?</p>
            <a href="/login" class="btn-login">
                Masuk di Sini
            </a>
        </div>
    </div>
</div>
@endsection