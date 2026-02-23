@extends('layouts.app')

@section('content')
<style>
    body {
        background: var(--soft-bg);
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        margin: 0;
        height: 100vh;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Arctic decorative elements */
    .arctic-blob {
        position: absolute;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(30, 136, 229, 0.05) 0%, rgba(30, 136, 229, 0) 70%);
        border-radius: 50%;
        z-index: 0;
    }
    .blob-1 { top: -200px; left: -200px; }
    .blob-2 { bottom: -200px; right: -200px; }

    .login-wrapper {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 440px;
        padding: 20px;
    }

    .arctic-card {
        background: var(--card-bg);
        padding: 50px 45px;
        border-radius: 35px;
        box-shadow: var(--vibrant-shadow);
        border: 1px solid var(--border-color);
        backdrop-filter: blur(20px);
    }

    .brand-logo {
        font-size: 32px;
        font-weight: 900;
        letter-spacing: -2px;
        color: var(--text-main);
        text-align: center;
        margin-bottom: 35px;
    }

    .brand-logo span { color: #1E88E5; }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        padding-left: 5px;
    }

    .arctic-input {
        width: 100%;
        padding: 15px 20px;
        border-radius: 18px;
        border: 1px solid var(--border-color);
        background: var(--soft-bg);
        font-size: 15px;
        font-weight: 600;
        color: var(--text-main);
        transition: 0.3s;
        box-sizing: border-box;
    }

    .arctic-input:focus {
        border-color: #1E88E5;
        box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.1);
        outline: none;
    }

    .btn-arctic {
        width: 100%;
        padding: 16px;
        border-radius: 18px;
        background: var(--primary-gradient);
        color: white;
        border: none;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        margin-top: 10px;
        box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2);
    }

    .btn-arctic:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(30, 136, 229, 0.3);
    }

    .auth-footer {
        margin-top: 35px;
        text-align: center;
        font-size: 14px;
        color: var(--text-muted);
    }

    .auth-footer a {
        color: #1E88E5;
        font-weight: 800;
        text-decoration: none;
        margin-left: 5px;
    }

    .alert {
        padding: 15px;
        border-radius: 18px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 25px;
        text-align: center;
    }

    .alert-error {
        background: #FFF5F5;
        color: #EF4444;
        border: 1px solid rgba(239, 68, 68, 0.1);
    }

    .alert-success {
        background: #F0FDF4;
        color: #10B981;
        border: 1px solid rgba(16, 185, 129, 0.1);
    }
</style>

<div class="arctic-blob blob-1"></div>
<div class="arctic-blob blob-2"></div>

<div class="login-wrapper">
    <div class="arctic-card">
        <div class="brand-logo">
            <span>⚡</span> ScheApp
        </div>

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
                <label>Email Address</label>
                <input type="email" name="email" class="arctic-input" required placeholder="name@example.com" value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="arctic-input" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-arctic">
                Sign In
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="/register">Create one</a>
        </div>
    </div>
</div>
@endsection