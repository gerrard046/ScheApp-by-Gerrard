@extends('layouts.app')

@section('content')
<div class="main-layout">
    <aside class="sidebar">
        <h2>Tambah Jadwal</h2>
        <hr>
        <form action="/schedules" method="POST">
            @csrf
            <p>Nama User:<br><input type="text" name="user_name" required style="width:100%"></p>
            <p>Nama Grup:<br><input type="text" name="group_name" required style="width:100%"></p>
            <p>Kegiatan:<br><input type="text" name="activity_name" required style="width:100%"></p>
            <p>Kategori:<br>
                <select name="category" required style="width:100%">
                    <option value="Belajar">Belajar</option>
                    <option value="Kerja">Kerja</option>
                    <option value="Meeting">Meeting</option>
                    <option value="Santai">Santai</option>
                </select>
            </p>
            <p>Tanggal:<br><input type="date" name="date" required style="width:100%"></p>
            <p>Jam:<br><input type="time" name="time" style="width:100%"></p>
            <button type="submit" style="width:100%; padding: 10px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
                + Simpan Jadwal
            </button>
        </form>
    </aside>

    <main class="content">
        <div class="stats-container">
            <div class="stat-card">
                <h3>{{ $stats['total'] }}</h3>
                <p>Total Jadwal</p>
            </div>
            <div class="stat-card" style="background: #17a2b8;">
                <h3>{{ $stats['today'] }}</h3>
                <p>Hari Ini</p>
            </div>
            <div class="stat-card" style="background: #6c757d;">
                <h3>{{ $stats['groups'] }}</h3>
                <p>Grup Aktif</p>
            </div>
        </div>

        <h1>Daftar Jadwal</h1>
        <div class="schedule-grid">
            @forelse($schedules as $item)
                <div class="schedule-card">
                    <small style="color: blue; font-weight: bold;">{{ $item->category }}</small>
                    <h3>{{ $item->activity_name }}</h3>
                    <p>👤 {{ $item->user_name }} ({{ $item->group_name }})</p>
                    <p>📅 {{ $item->date }} | ⏰ {{ $item->time ?? '--:--' }}</p>
                    
                    <form action="/schedules/{{ $item->id }}" method="POST" onsubmit="return confirm('Hapus jadwal ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">Hapus</button>
                    </form>
                </div>
            @empty
                <p>Belum ada jadwal. Silakan tambah di samping!</p>
            @endforelse
        </div>
    </main>
</div>
@endsection