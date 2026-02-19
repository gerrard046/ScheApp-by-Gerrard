@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #6366f1;
        --success: #10b981;
        --danger: #ef4444;
        --bg: #f4f7fe;
    }

    .main-wrapper {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 30px;
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 25px;
    }

    /* Sidebar Form */
    .form-card {
        background: white;
        border-radius: 24px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
        position: sticky;
        top: 100px;
        height: fit-content;
        border: 1px solid #e2e8f0;
    }

    /* Modern Cards */
    .schedule-card {
        background: white;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid #f1f5f9;
        transition: 0.3s;
        position: relative;
    }

    .is-completed {
        border-left: 8px solid var(--success) !important;
        background: #f0fff4 !important;
    }

    /* Search Bar */
    .search-box {
        background: white;
        padding: 15px 25px;
        border-radius: 20px;
        margin-bottom: 25px;
        display: flex;
        gap: 15px;
        border: 2px solid #edf2f7;
    }

    .input-style {
        width: 100%;
        padding: 12px 15px;
        border-radius: 12px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        margin-bottom: 15px;
        outline: none;
        font-family: inherit;
    }

    .badge-prio {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
    }

    .prio-high { background: var(--danger); }
    .prio-med { background: #f59e0b; }
    .prio-low { background: var(--success); }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 800;
        cursor: pointer;
    }
</style>

<div class="main-wrapper">
    <aside>
        <div class="form-card">
            <h2 style="margin-bottom: 25px; font-weight: 800; letter-spacing: -1px;">🎯 Buat Agenda</h2>
            <form action="/schedules" method="POST">
                @csrf
                <input type="text" name="activity_name" class="input-style" placeholder="Nama Kegiatan (ex: Lari Pagi)" required>
                <input type="text" name="group_name" class="input-style" placeholder="Grup / Lokasi" required>
                
                <div style="display: flex; gap: 10px;">
                    <select name="category" class="input-style">
                        <option value="Olahraga">💪 Olahraga</option>
                        <option value="Belajar">📚 Belajar</option>
                        <option value="Rapat">🤝 Rapat</option>
                        <option value="Lainnya">☕ Lainnya</option>
                    </select>
                    <select name="priority" class="input-style">
                        <option value="high">Penting</option>
                        <option value="med" selected>Biasa</option>
                        <option value="low">Santai</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px;">
                    <input type="date" name="date" class="input-style" value="{{ date('Y-m-d') }}">
                    <input type="time" name="time" class="input-style" value="{{ date('H:i') }}">
                </div>

                <button type="submit" class="btn-submit">⚡ Simpan Jadwal</button>
            </form>
        </div>
    </aside>

    <main>
        <div class="search-box">
            <span>🔍</span>
            <input type="text" id="search" class="input-style" style="margin:0; border:none; background:none;" placeholder="Cari kegiatan atau kategori..." onkeyup="doSearch()">
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;" id="list">
            @foreach($schedules as $item)
            @php 
                $done = $item->is_completed; 
                $past = \Carbon\Carbon::parse($item->date.' '.$item->time)->isPast();
            @endphp
            <div class="schedule-card {{ $done ? 'is-completed' : '' }} card-item" data-text="{{ strtolower($item->activity_name.' '.$item->category) }}">
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span style="font-size: 10px; font-weight: 800; color: #64748b; background: #f1f5f9; padding: 4px 10px; border-radius: 6px;">
                        {{ strtoupper($item->category) }}
                    </span>
                    <form action="/schedules/{{ $item->id }}/toggle" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 18px;">
                            {{ $done ? '✅' : '⬜' }}
                        </button>
                    </form>
                </div>

                <h3 style="margin: 0 0 10px; text-decoration: {{ $done ? 'line-through' : 'none' }};">
                    <span class="badge-prio prio-{{ $item->priority }}"></span> {{ $item->activity_name }}
                </h3>

                <div style="font-size: 13px; color: #64748b;">
                    <p>👤 <b>{{ $item->user_name }}</b> • {{ $item->group_name }}</p>
                    <p>📅 {{ $item->date }} | ⏰ {{ $item->time }}</p>
                </div>

                <div style="margin-top: 15px; border-top: 1px solid #f1f5f9; padding-top: 15px; display: flex; justify-content: space-between;">
                    <span style="font-size: 10px; color: {{ $past && !$done ? 'red' : '#94a3b8' }}; font-weight: 700;">
                        {{ $past && !$done ? '⚠ TERLEWAT' : ($done ? 'SELESAI' : 'AKAN DATANG') }}
                    </span>
                    <form action="/schedules/{{ $item->id }}" method="POST">
                        @csrf @method('DELETE')
                        <button style="background: none; border: none; color: #f87171; cursor: pointer; font-size: 11px;">Hapus</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </main>
</div>

<script>
function doSearch() {
    let q = document.getElementById('search').value.toLowerCase();
    let cards = document.getElementsByClassName('card-item');
    for (let c of cards) {
        c.style.display = c.getAttribute('data-text').includes(q) ? "" : "none";
    }
}
</script>
@endsection