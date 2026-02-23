@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary: #FF8C00;
        --success: #4ECDC4;
        --danger: #FF6B6B;
        --bg: #FFF9F0;
        --gold: #FFD700;
    }

    .main-wrapper.admin-layout {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 30px;
        max-width: 1400px;
        margin: 40px auto;
        padding: 0 25px;
    }
    
    .main-wrapper.user-layout {
        max-width: 1000px;
        margin: 40px auto;
        padding: 0 25px;
    }

    /* Sidebar Form */
    .form-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 30px;
        box-shadow: var(--vibrant-shadow);
        position: sticky;
        top: 100px;
        height: fit-content;
        border: 2px solid #FFEDCC;
    }

    /* Modern Cards */
    .schedule-card {
        background: white;
        border-radius: var(--card-radius);
        padding: 25px;
        border: none;
        box-shadow: 0 5px 15px rgba(255, 140, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }
    
    .schedule-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(255, 140, 0, 0.1);
    }

    .is-completed {
        border-right: 8px solid var(--success) !important;
        background: #F0FFFD !important;
        opacity: 0.9;
    }

    /* AI Prioritizer Pulse Effect */
    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .top-priority {
        border-left: 8px solid var(--danger) !important;
        animation: pulse-border 2s infinite;
        background: #FFF5F5 !important;
    }

    /* Burnout Alert */
    .insight-alert {
        background: linear-gradient(135deg, #FF8C00, #FF6B6B);
        color: white;
        padding: 20px 30px;
        border-radius: var(--card-radius);
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        font-weight: 700;
        box-shadow: 0 15px 35px rgba(255, 107, 107, 0.3);
        border: none;
    }

    /* Focus Mode Overlay */
    .focus-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.98);
        backdrop-filter: blur(10px);
        z-index: 9999;
        display: none;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        color: white;
    }

    .focus-overlay.active { display: flex; }

    .pomodoro-timer {
        font-size: 80px;
        font-weight: 800;
        margin: 20px 0;
        font-family: monospace;
        letter-spacing: -2px;
        color: var(--success);
    }

    .focus-task-card {
        background: rgba(255,255,255,0.1);
        padding: 40px;
        border-radius: 24px;
        text-align: center;
        border: 1px solid rgba(255,255,255,0.2);
        max-width: 500px;
        width: 100%;
    }

    .btn-focus-toggle {
        padding: 10px 20px;
        background: #1e293b;
        color: white;
        border-radius: 12px;
        border: none;
        cursor: pointer;
        font-weight: bold;
        transition: 0.3s;
    }
    .btn-focus-toggle:hover { background: #334155; }


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

    .schedule-card.cat-olahraga { border-left: 5px solid #ef4444; }
    .schedule-card.cat-belajar { border-left: 5px solid #6366f1; }
    .schedule-card.cat-rapat { border-left: 5px solid #f59e0b; }
    .schedule-card.cat-lainnya { border-left: 5px solid #10b981; }

    .schedule-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1);
    }

    /* Sidebar Nav */
    .sidebar-nav { margin-bottom: 30px; }
    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        border-radius: 12px;
        color: #64748b;
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 5px;
        transition: all 0.2s;
    }
    .nav-item:hover { background: #f1f5f9; color: #1e293b; }
    .nav-item.active { background: #6366f1; color: white; }

    /* Sub-task styles */
    .sub-task-list {
        margin-top: 15px;
        background: rgba(241, 245, 249, 0.5);
        border-radius: 10px;
        padding: 10px;
    }
    .sub-task-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        margin-bottom: 5px;
        color: #475569;
    }
    .sub-task-progress-container {
        width: 100%;
        background: #e2e8f0;
        height: 6px;
        border-radius: 3px;
        margin: 10px 0;
        overflow: hidden;
    }
    .sub-task-progress-fill {
        height: 100%;
        background: #6366f1;
        transition: width 0.3s ease;
    }
    .btn-add-sub {
        background: none;
        border: 1px dashed #cbd5e1;
        width: 100%;
        padding: 5px;
        font-size: 10px;
        color: #94a3b8;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 5px;
    }
    .btn-add-sub:hover { background: #f8fafc; color: #6366f1; border-color: #6366f1; }

    /* Analytics Section */
    .analytics-panel {
        background: linear-gradient(135deg, #4f46e5, #6366f1);
        border-radius: 20px;
        padding: 20px;
        color: white;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .xp-bar-container {
        width: 100%;
        background: rgba(255,255,255,0.2);
        height: 12px;
        border-radius: 6px;
        margin-top: 10px;
        overflow: hidden;
    }

    .xp-bar-fill {
        height: 100%;
        background: #10b981;
        transition: width 0.5s ease;
    }

    .heatmap-grid {
        display: flex;
        gap: 5px;
        margin-top: 10px;
    }

    .heatmap-cell {
        width: 25px;
        height: 25px;
        border-radius: 6px;
        background: rgba(255,255,255,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        border: 1px solid rgba(255,255,255,0.05);
    }

    .heatmap-cell.active { background: #10b981; box-shadow: 0 0 10px rgba(16, 185, 129, 0.5); }
</style>

<!-- Confetti & Sound -->
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<audio id="focusAudio" loop>
    <source src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" type="audio/mpeg">
</audio>

<div class="main-wrapper {{ auth()->check() ? 'admin-layout' : 'user-layout' }}">
    <aside>
        <div class="sidebar-nav">
            <h2 style="font-weight: 800; letter-spacing: -1px; margin-bottom: 20px; color: #1e293b;">ScheApp Pro</h2>
            <nav>
                <a href="/schedules" class="nav-item active">
                    <span>🏠</span> Dashboard
                </a>
            <nav style="display: flex; flex-direction: column; gap: 8px;">
                <a href="/schedules" class="nav-item active" style="background: var(--primary-gradient); color: white; border: none; box-shadow: 0 4px 12px rgba(255,140,0,0.3);">
                    <span>🏠</span> Dashboard Utama
                </a>
                <a href="/calendar" class="nav-item" style="color: #4A4A4A; font-weight: 600; padding: 12px 15px; border-radius: 12px; display: flex; align-items: center; gap: 10px; text-decoration: none; transition: 0.3s;">
                    <span>📅</span> View Kalender
                </a>
                <a href="/groups" class="nav-item" style="color: #4A4A4A; font-weight: 600; padding: 12px 15px; border-radius: 12px; display: flex; align-items: center; gap: 10px; text-decoration: none; transition: 0.3s;">
                    <span>🤝</span> Grup Kerjasama
                </a>
                @if(auth()->user()->role === 'admin')
                <a href="/admin/insights" class="nav-item" style="color: #4A4A4A; font-weight: 600; padding: 12px 15px; border-radius: 12px; display: flex; align-items: center; gap: 10px; text-decoration: none; transition: 0.3s;">
                    <span>📈</span> Pantauan Admin
                </a>
                @endif
            </nav>
        </div>

        @if(auth()->check() && auth()->user()->role === 'admin')
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
                    <select name="group_id" class="input-style">
                        <option value="">👤 Personal</option>
                        @foreach($groups as $g)
                        <option value="{{ $g->id }}">🤝 {{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="margin-bottom: 15px;">
                    <select name="priority" class="input-style" style="margin: 0;">
                        <option value="high">Penting</option>
                        <option value="med" selected>Biasa</option>
                        <option value="low">Santai</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; align-items: flex-end;">
                    <div style="flex-grow: 1;">
                        <label style="font-size: 11px; font-weight: bold; color: #64748b;">📅 Tanggal</label>
                        <input type="date" name="date" id="input_date" class="input-style" value="{{ date('Y-m-d') }}">
                    </div>
                    <div style="flex-grow: 1; position: relative;">
                        <label style="font-size: 11px; font-weight: bold; color: #64748b;">⏰ Waktu</label>
                        <input type="time" name="time" id="input_time" class="input-style" value="{{ date('H:i') }}">
                        <button type="button" onclick="getAISuggestion()" style="position: absolute; right: 5px; top: 25px; background: #6366f1; color: white; border: none; padding: 5px 8px; border-radius: 6px; font-size: 10px; cursor: pointer;" title="AI Suggest Slot">🤖 AI</button>
                    </div>
                </div>

                <button type="submit" class="btn-cheerful" style="width: 100%; margin-top: 10px; font-size: 16px;">⚡ Simpan Jadwal Seru!</button>
            </form>

            <hr style="margin: 25px 0; border: 1px solid #edf2f7;">

            <form action="/schedules/snooze" method="POST">
                @csrf
                <button class="btn-submit" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;" type="submit" onclick="return confirm('Mundurkan semua jadwal HARI INI sebanyak 2 jam?')">
                    🚨 Kena Apel Mendadak
                </button>
            </form>

            <hr style="margin: 25px 0; border: 1px solid #FFEDCC;">
            <h3 style="font-size: 13px; color: #FF8C00; margin-bottom: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">🕹️ Kontrol Admin</h3>
            <a href="/schedules?needs_verification=1" class="btn-cheerful" style="background: var(--secondary-gradient); color: white; display: block; text-decoration: none; text-align: center; margin-bottom: 8px; box-shadow: 0 4px 15px rgba(255,107,107,0.3);">⏳ Butuh Verifikasi</a>
            <a href="/schedules" class="btn-cheerful" style="background: white; color: #FF8C00; display: block; text-decoration: none; text-align: center; border: 2px solid #FF8C00; box-shadow: none;">Semua Jadwal</a>
        @endif
    </aside>

    <main>
        <!-- Header Info -->
        <div class="animate-cheerful" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: white; padding: 25px 30px; border-radius: var(--card-radius); box-shadow: var(--vibrant-shadow); border: 2px solid #FFEDCC;">
            <div>
                <h3 style="margin: 0; color: #1e293b; font-size: 20px;">Halo, {{ auth()->user()->name ?? 'Guest' }}! 🌟 <span style="font-size: 14px; background: var(--primary-gradient); color: white; padding: 5px 15px; border-radius: 20px; margin-left: 10px; font-weight: 800; box-shadow: 0 4px 10px rgba(255,140,0,0.3);">LVL {{ $stats['level'] }}</span></h3>
                <p style="margin: 8px 0 0; color: #64748b; font-size: 13px;">🔥 <b>{{ $stats['streak'] }} Day Streak</b> | 🎯 <b>{{ $stats['completion_rate'] }}% Selesai</b></p>
            </div>
            
            <div style="flex-grow: 1; max-width: 250px; margin: 0 30px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 800; color: #FF8C00; margin-bottom: 8px;">
                    <span>XP: {{ $stats['xp'] }}</span>
                    <span>NEXT: {{ $stats['xp_next'] }}</span>
                </div>
                <div class="xp-bar-container" style="background: #FFF5E6; height: 12px; border-radius: 10px; border: 1px solid #FFEDCC; overflow: hidden;">
                    <div class="xp-bar-fill" style="width: {{ ($stats['xp'] / $stats['xp_next']) * 100 }}%; background: var(--secondary-gradient); height: 100%; border-radius: 10px; box-shadow: 0 0 10px rgba(255, 107, 107, 0.4);"></div>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-cheerful" style="background: rgba(255, 107, 107, 0.1); color: #FF6B6B; border: 2px solid #FF6B6B; box-shadow: none;">
                    👋 Keluar
                </button>
            </form>
        </div>

        <!-- Heatmap Analytics -->
        <div class="analytics-panel">
            <div>
                <h4 style="margin: 0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">Papan Produktivitas (7 Hari)</h4>
                <div class="heatmap-grid">
                    @foreach($stats['heatmap'] as $day)
                    <div class="heatmap-cell {{ $day['count'] > 0 ? 'active' : '' }}" title="{{ $day['date'] }}: {{ $day['count'] }} tugas">
                        {{ substr(\Carbon\Carbon::parse($day['date'])->format('D'), 0, 1) }}
                    </div>
                    @endforeach
                </div>
            </div>
            <div style="text-align: right;">
                <h1 style="margin: 0; font-size: 32px;">{{ $stats['completion_rate'] }}%</h1>
                <p style="margin: 0; font-size: 11px; opacity: 0.8;">Skor Fokus</p>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div style="flex-grow: 1;">
                @if(isset($insightMessage))
                <div class="insight-alert" style="margin-bottom: 0;">
                    💡 {{ $insightMessage }}
                </div>
                @endif
            </div>
            <button class="btn-focus-toggle" onclick="toggleFocusMode()" style="margin-left: 20px;">
                🎯 Focus Mode
            </button>
        </div>

        <div class="search-box">
            <span>🔍</span>
            <input type="text" id="search" class="input-style" style="margin:0; border:none; background:none;" placeholder="Cari kegiatan atau kategori..." onkeyup="doSearch()">
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;" id="list">
            @php $topCount = 0; @endphp
            @foreach($schedules as $item)
            @php 
                $done = $item->is_completed; 
                $past = \Carbon\Carbon::parse($item->date.' '.$item->time)->isPast();
                $catClass = 'cat-' . strtolower($item->category);
                
                // Highlight 2 tugas teratas yang belum selesai dan belum terlewat dengan prioritas tinggi
                $isTopPriority = false;
                if (!$done && !$past && $item->ai_score > 30 && $topCount < 2) {
                    $isTopPriority = true;
                    if ($topCount == 0) {
                        echo "<script>window.topTaskId = {$item->id}; window.topTaskName = '{$item->activity_name}';</script>";
                    }
                    $topCount++;
                }

                $cardClass = $catClass;
                if ($done) $cardClass .= ' is-completed';
                elseif ($isTopPriority) $cardClass .= ' top-priority';
            @endphp
            <div class="schedule-card {{ $cardClass }} card-item" data-text="{{ strtolower($item->activity_name.' '.$item->category) }}">
                <div style="display: flex; justify-content: space-between; margin-bottom: 15px;">
                    <span style="font-size: 10px; font-weight: 800; color: #64748b; background: #f1f5f9; padding: 4px 10px; border-radius: 6px;">
                        {{ strtoupper($item->category) }}
                    </span>
                    @if(auth()->check() && auth()->user()->role === 'admin')
                    <form action="/schedules/{{ $item->id }}/toggle" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 18px;">
                            {{ $done ? '✅' : '⬜' }}
                        </button>
                    </form>
                    @else
                    <span style="font-size: 18px;">{{ $done ? '✅' : '⏳' }}</span>
                    @endif
                </div>

                <h3 style="margin: 0 0 10px; text-decoration: {{ $done ? 'line-through' : 'none' }};">
                    <span class="badge-prio prio-{{ $item->priority }}"></span> {{ $item->activity_name }}
                </h3>

                @if($item->subTasks->count() > 0)
                @php
                    $completedSub = $item->subTasks->where('is_completed', true)->count();
                    $totalSub = $item->subTasks->count();
                    $percent = round(($completedSub / $totalSub) * 100);
                @endphp
                <div class="sub-task-progress-container">
                    <div class="sub-task-progress-fill" style="width: {{ $percent }}%"></div>
                </div>
                <div class="sub-task-list">
                    @foreach($item->subTasks as $sub)
                    <div class="sub-task-item">
                        <form action="/sub-tasks/{{ $sub->id }}/toggle" method="POST" style="display:inline;">
                            @csrf
                            <input type="checkbox" {{ $sub->is_completed ? 'checked' : '' }} onchange="this.form.submit()" style="cursor:pointer">
                        </form>
                        <span style="{{ $sub->is_completed ? 'text-decoration: line-through; opacity: 0.6;' : '' }}">{{ $sub->title }}</span>
                    </div>
                    @endforeach
                </div>
                @endif

                @if(auth()->check() && auth()->user()->role === 'admin' && !$done)
                <form action="/sub-tasks" method="POST" style="margin-top: 10px; display: flex; gap: 5px;">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $item->id }}">
                    <input type="text" name="title" placeholder="Checklist baru..." required style="flex-grow: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 5px 8px; font-size: 11px;">
                    <button type="submit" style="background: #6366f1; color: white; border: none; border-radius: 6px; padding: 0 10px; font-size: 11px; cursor: pointer;">+</button>
                </form>
                @endif

                <div style="font-size: 13px; color: #64748b; margin-top: 15px;">
                    <p>👤 <b>{{ $item->user_name }}</b> • {{ $item->group_name }}</p>
                    <p>📅 {{ $item->date }} | ⏰ {{ $item->time }}</p>
                </div>

                <div style="margin-top: 15px; border-top: 1px solid #f1f5f9; padding-top: 15px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 10px; color: {{ $past && !$done ? 'red' : '#94a3b8' }}; font-weight: 700;">
                            {{ $past && !$done ? '⚠ TERLEWAT' : ($done ? 'SELESAI' : 'AKAN DATANG') }}
                        </span>
                        @if($done && !$item->is_verified)
                            <span style="font-size: 9px; background: #fff7ed; color: #c2410c; padding: 2px 6px; border-radius: 4px; margin-left: 5px; font-weight: bold;">⏳ WAITING VERIFY</span>
                        @elseif($done && $item->is_verified)
                            <span style="font-size: 9px; background: #dcfce7; color: #15803d; padding: 2px 6px; border-radius: 4px; margin-left: 5px; font-weight: bold;">✅ VERIFIED</span>
                        @endif
                    </div>
                    <div style="display: flex; gap: 10px;">
                        @if(auth()->check() && auth()->user()->role === 'admin')
                            @if($done && !$item->is_verified)
                            <form action="/schedules/{{ $item->id }}/toggle" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" style="background: #10b981; color: white; border: none; border-radius: 6px; padding: 4px 10px; font-size: 10px; font-weight: bold; cursor: pointer;">VERIFIKASI</button>
                            </form>
                            @endif
                            
                            <form action="/schedules/{{ $item->id }}" method="POST">
                                @csrf @method('DELETE')
                                <button style="background: none; border: none; color: #f87171; cursor: pointer; font-size: 11px;">Hapus</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </main>
</div>

<!-- Focus Mode Overlay -->
<div class="focus-overlay" id="focusOverlay">
    <div class="focus-task-card">
        <h3 style="color: #94a3b8; text-transform: uppercase; font-size: 14px; letter-spacing: 2px;">Sedang Dikerjakan</h3>
        <h1 id="focusTaskName" style="font-size: 32px; margin: 15px 0;">Tidak ada tugas prioritas mendesak.</h1>
        
        <div class="pomodoro-timer" id="pomodoroTimer">25:00</div>
        
        <div style="display: flex; gap: 15px; justify-content: center; margin-top: 30px;">
            <button onclick="startTimer()" style="padding: 12px 25px; background: var(--success); border: none; border-radius: 8px; color: white; cursor: pointer; font-weight: bold;">Mulai</button>
            <button onclick="resetTimer()" style="padding: 12px 25px; background: #64748b; border: none; border-radius: 8px; color: white; cursor: pointer; font-weight: bold;">Reset</button>
            <button onclick="toggleFocusMode()" style="padding: 12px 25px; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; color: white; cursor: pointer; font-weight: bold;">Keluar</button>
        </div>
    </div>
</div>

@if(session('success'))
<script>
    if("{{ session('success') }}".includes("XP kamu")) {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 },
            colors: ['#6366f1', '#10b981', '#ffffff']
        });
    }
</script>
@endif

@if(session('levelup'))
<script>
    confetti({
        particleCount: 200,
        spread: 100,
        origin: { y: 0.4 },
        colors: ['#f59e0b', '#ef4444', '#ffffff']
    });
    alert("{{ session('levelup') }}");
</script>
@endif

<script>
let timerId = null;
let timeLeft = 25 * 60; // 25 minutes
const audio = document.getElementById('focusAudio');

function doSearch() {
    let q = document.getElementById('search').value.toLowerCase();
    let cards = document.getElementsByClassName('card-item');
    for (let c of cards) {
        c.style.display = c.getAttribute('data-text').includes(q) ? "" : "none";
    }
}

function toggleFocusMode() {
    const overlay = document.getElementById('focusOverlay');
    overlay.classList.toggle('active');
    
    if (overlay.classList.contains('active')) {
        if (window.topTaskName) {
            document.getElementById('focusTaskName').innerText = window.topTaskName;
        } else {
            document.getElementById('focusTaskName').innerText = "Belum ada tugas prioritas mendesak saat ini.";
        }
    } else {
        clearInterval(timerId);
        timerId = null;
        audio.pause();
        audio.currentTime = 0;
    }
}

function startTimer() {
    if (timerId) return;
    
    // Play Focus Sound
    audio.play().catch(e => console.log("Autoplay blocked, click play first"));

    timerId = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        
        if (timeLeft <= 0) {
            clearInterval(timerId);
            timerId = null;
            audio.pause();
            alert('🍅 Waktu Pomodoro selesai! Silakan istirahat sebentar.');
            resetTimer();
        }
    }, 1000);
}

function resetTimer() {
    clearInterval(timerId);
    timerId = null;
    timeLeft = 25 * 60;
    audio.pause();
    audio.currentTime = 0;
    updateTimerDisplay();
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    const display = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    document.getElementById('pomodoroTimer').innerText = display;
}

async function getAISuggestion() {
    const date = document.getElementById('input_date').value;
    const btn = event.currentTarget;
    const originalText = btn.innerText;
    btn.innerText = "⏳";
    
    try {
        const response = await fetch(`/schedules/suggest?date=${date}`);
        const data = await response.json();
        document.getElementById('input_time').value = data.suggested_time;
        btn.innerText = "✨";
        setTimeout(() => btn.innerText = originalText, 2000);
    } catch (e) {
        btn.innerText = "❌";
        setTimeout(() => btn.innerText = originalText, 2000);
    }
}

// --- Smart Alerts (Notifications) ---
document.addEventListener('DOMContentLoaded', function() {
    if ("Notification" in window) {
        if (Notification.permission !== "granted" && Notification.permission !== "denied") {
            Notification.requestPermission();
        }
    }
    
    // Check every minute
    setInterval(checkUpcomingTasks, 60000);
    setTimeout(checkUpcomingTasks, 2000); // Initial check after 2s
});

function checkUpcomingTasks() {
    const cards = document.getElementsByClassName('card-item');
    const now = new Date();
    
    for (let card of cards) {
        const textElement = card.querySelector('h3');
        if (!textElement) continue;
        const taskName = textElement.innerText.trim();
        
        const timeElements = card.querySelectorAll('p');
        if (timeElements.length < 2) continue;
        const timeStr = timeElements[1].innerText; // Contains "date | time"
        const isDone = card.classList.contains('is-completed');
        
        if (isDone) continue;

        try {
            const parts = timeStr.split('|');
            const datePart = parts[0].replace('📅', '').trim();
            const timePart = parts[1].replace('⏰', '').trim();
            const taskTime = new Date(datePart + ' ' + timePart);
            
            const diffMs = taskTime - now;
            const diffMin = Math.round(diffMs / 60000);

            // Notify if between 9 and 11 minutes away to avoid missing the window
            if (diffMin === 10) {
                if (Notification.permission === "granted") {
                    new Notification("🔔 ScheApp Pro: Pengingat", {
                        body: `Jadwal "${taskName}" akan dimulai dalam 10 menit!`,
                    });
                }
            }
        } catch (e) {}
    }
}
</script>
@endsection