@extends('layouts.app')

@section('content')
<style>
    body {
        /* Warmer, cheerfuller sunset gradient */
        background: linear-gradient(-45deg, #FF8C00, #FFD700, #FF6B6B, #FF8E53);
        background-size: 400% 400%;
        animation: gradientBG 10s ease infinite;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
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
        background: rgba(255, 215, 0, 0.4);
        top: -100px;
        left: -100px;
        border-radius: 50%;
    }
    .shape:nth-child(2) {
        height: 500px;
        width: 500px;
        background: rgba(255, 140, 0, 0.3);
        bottom: -150px;
        right: -100px;
        border-radius: 50%;
    }

    .login-container {
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
        padding: 45px 40px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 35px;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
        color: white;
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
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
        margin-bottom: 30px;
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

    .alert-success {
        background: rgba(46, 204, 113, 0.2);
        border: 1px solid rgba(46, 204, 113, 0.5);
        color: #fff;
    }

    .alert-error {
        background: rgba(231, 76, 60, 0.2);
        border: 1px solid rgba(231, 76, 60, 0.5);
        color: #fff;
    }

    /* Form Fields */
    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        font-size: 14px;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        padding: 15px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        color: white;
        font-size: 15px;
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
        padding: 15px;
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

    .btn-submit:active {
        transform: translateY(0);
    }

    /* Footer / Divider */
    .divider {
        height: 1px;
        background: rgba(255, 255, 255, 0.2);
        margin: 30px 0;
    }

    .auth-footer {
        text-align: center;
    }

    .auth-footer p {
        margin-bottom: 15px;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.8);
    }

    .btn-register {
        display: inline-block;
        padding: 12px 25px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        text-decoration: none;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-register:hover {
        background: rgba(255, 255, 255, 0.2);
        border-color: #fff;
    }
</style>

<div class="shape"></div>
<div class="shape"></div>

<div class="login-container">
    <div class="glass-card">
        <h2>ScheApp</h2>
        <p>Kelola jadwal kegiatanmu dengan mudah dan elegan.</p>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-input" required placeholder="Masukkan email...">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-input" required placeholder="Masukkan password...">
            </div>

            <button type="submit" class="btn-submit">
                Masuk
            </button>
        </form>

        <div class="divider"></div>

        <div class="auth-footer">
            <p>Belum punya akun?</p>
            <a href="/register" class="btn-register">
                Buat Akun Baru
            </a>
        </div>
    </div>
</div>
@endsection