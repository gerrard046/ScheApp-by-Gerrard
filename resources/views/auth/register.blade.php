@extends('layouts.app')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; background: #f0f2f5;">
    <div style="width: 100%; max-width: 400px; padding: 30px; background: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 20px;">Daftar Akun ScheApp</h2>
        <form action="/register" method="POST">
            @csrf
            <div style="margin-bottom: 15px;">
                <label>Nama Lengkap</label>
                <input type="text" name="name" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div style="margin-bottom: 15px;">
                <label>Email</label>
                <input type="email" name="email" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; margin-top: 5px; border: 1px solid #ddd; border-radius: 6px;">
            </div>
            <button type="submit" style="width: 100%; padding: 12px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold;">Daftar Sekarang</button>
        </form>
        <p style="text-align: center; margin-top: 15px;">Sudah punya akun? <a href="/login">Login di sini</a></p>
    </div>
</div>
@endsection