@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f0f2f5; font-family: sans-serif;">
    <div style="width: 100%; max-width: 400px; padding: 35px; background: white; border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; color: #1c1e21; margin-bottom: 10px;">Login ScheApp</h2>
        <p style="text-align: center; color: #606770; margin-bottom: 25px;">Kelola jadwal kegiatanmu dengan mudah.</p>

        @if(session('success'))
            <div style="background: #e7f3ff; color: #1877f2; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; text-align: center; border: 1px solid #1877f2;">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div style="background: #ffebe8; color: #f02849; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 14px; border: 1px solid #f02849;">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #4b4f56;">Email</label>
                <input type="email" name="email" required placeholder="Masukkan email..." 
                    style="width: 100%; padding: 12px; border: 1px solid #dddfe2; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: bold; margin-bottom: 5px; color: #4b4f56;">Password</label>
                <input type="password" name="password" required placeholder="Masukkan password..." 
                    style="width: 100%; padding: 12px; border: 1px solid #dddfe2; border-radius: 6px; box-sizing: border-box; font-size: 16px;">
            </div>

            <button type="submit" style="width: 100%; padding: 12px; background: #0866ff; color: white; border: none; border-radius: 6px; font-size: 18px; font-weight: bold; cursor: pointer;">
                Masuk
            </button>
        </form>

        <hr style="border: 0; border-top: 1px solid #dddfe2; margin: 25px 0;">

        <div style="text-align: center;">
            <p style="color: #606770; margin-bottom: 15px;">Belum punya akun?</p>
            <a href="/register" style="display: inline-block; padding: 10px 20px; background: #42b72a; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 14px;">
                Buat Akun Baru
            </a>
        </div>
    </div>
</div>
@endsection