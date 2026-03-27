@extends('layouts.app')

@section('content')
<style>
    /* Layout */
    .main-wrapper {
        display: flex;
        min-height: 100vh;
        background: var(--soft-bg);
    }

    /* Sidebar */
    aside {
        width: 260px;
        background: var(--card-bg);
        border-right: 1px solid var(--border-color);
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 64px;
        height: calc(100vh - 64px);
        backdrop-filter: blur(10px);
        overflow-y: auto;
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 16px;
        border-radius: 14px;
        color: var(--text-main);
        text-decoration: none;
        font-weight: 700;
        transition: all 0.25s ease;
        margin-bottom: 4px;
        font-size: 14px;
    }
    .nav-link:hover {
        background: var(--soft-bg);
        transform: translateX(4px);
    }
    .nav-link.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 6px 18px rgba(30, 136, 229, 0.25);
    }

    /* Content */
    main {
        flex-grow: 1;
        padding: 40px;
        max-width: 1000px;
        margin: 0 auto;
    }

    /* Cards */
    .zen-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 22px;
        padding: 28px;
        box-shadow: var(--vibrant-shadow);
        transition: all 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .zen-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 44px rgba(30, 136, 229, 0.1);
    }

    /* FABs */
    .fab-container {
        position: fixed;
        bottom: 100px;
        right: 30px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        z-index: 10000;
    }
    .fab {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        cursor: pointer;
        box-shadow: 0 8px 25px rgba(30, 136, 229, 0.3);
        transition: all 0.25s ease;
        border: none;
    }
    .fab:hover { transform: scale(1.08); }
    .fab-zen {
        background: #1E293B;
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }

    /* Overlay */
    .arctic-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(12px);
        z-index: 20000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .zen-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: var(--soft-bg);
        z-index: 20000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Cards & Inputs */
    .card-item { transition: 0.25s; }
    .card-item:hover { transform: scale(1.005); }

    .arctic-input {
        width: 100%;
        padding: 14px 18px;
        background: var(--soft-bg);
        border: 1.5px solid var(--border-color);
        border-radius: 14px;
        color: var(--text-main);
        font-weight: 600;
        outline: none;
        transition: all 0.25s ease;
        margin-bottom: 12px;
        font-family: inherit;
        font-size: 14px;
    }
    .arctic-input:focus {
        border-color: #1E88E5;
        box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.1);
    }
    .arctic-input::placeholder { color: var(--text-muted); font-weight: 500; }

    /* Alerts */
    .flash-alert {
        position: fixed;
        top: 80px;
        right: 20px;
        z-index: 99999;
        animation: slideInRight 0.4s ease;
    }
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    /* Section heading */
    .section-title {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
    }

    /* Priority Selector */
    .priority-group {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }
    .priority-option {
        flex: 1;
        padding: 10px;
        border-radius: 12px;
        text-align: center;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        border: 2px solid var(--border-color);
        background: var(--soft-bg);
        color: var(--text-muted);
        transition: 0.2s;
    }
    .priority-option:hover { border-color: var(--primary); }
    .priority-option.selected { 
        border-color: currentColor;
        color: white;
    }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(30, 136, 229, 0.12); border-radius: 10px; }

    /* Responsive */
    @media (max-width: 768px) {
        aside { display: none; }
        main { padding: 20px !important; }
        .fab-container { bottom: 90px; right: 16px; }
        .fab { width: 50px; height: 50px; font-size: 20px; }
    }
</style>

<div class="main-wrapper" x-data="{ 
    showCreate: false, 
    zen: false,
    focusMinutes: 25,
    focusSeconds: 0,
    timerActive: false,
    timerId: null,
    urgentTask: '{{ $schedules->where('is_completed', false)->sortBy('date')->first()->activity_name ?? 'Istirahat' }}',
    selectedPriority: 'med',

    toggleTimer() {
        if (this.timerActive) {
            clearInterval(this.timerId);
            this.timerActive = false;
        } else {
            this.timerActive = true;
            this.timerId = setInterval(() => {
                if (this.focusSeconds === 0) {
                    if (this.focusMinutes === 0) {
                        clearInterval(this.timerId);
                        this.timerActive = false;
                        alert('Waktu Fokus Selesai! 🎉');
                        return;
                    }
                    this.focusMinutes--;
                    this.focusSeconds = 59;
                } else {
                    this.focusSeconds--;
                }
            }, 1000);
        }
    }
}">

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="flash-alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
        <div style="background: #10B981; color: white; padding: 14px 24px; border-radius: 14px; font-weight: 700; font-size: 13px; box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);">
            ✅ {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('levelup'))
    <div class="flash-alert" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
        <div style="background: var(--accent-gradient); color: white; padding: 14px 24px; border-radius: 14px; font-weight: 700; font-size: 13px; box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);">
            🎉 {{ session('levelup') }}
        </div>
    </div>
    @endif

    <!-- FABs -->
    <div class="fab-container">
        <button class="fab fab-zen" @click="zen = true" title="Zen Mode">🧘</button>
        <button class="fab" @click="showCreate = true" title="Tambah Agenda">+</button>
    </div>

    <!-- Sidebar -->
    <aside>
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 22px; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                <span style="color: #1E88E5;">⚡</span> ScheApp
            </h1>
        </div>

        <nav>
            <a href="/schedules" class="nav-link active"><span>🏠</span> Dashboard</a>
            <a href="/calendar" class="nav-link"><span>📅</span> Kalender</a>
            <a href="/kanban" class="nav-link"><span>📋</span> Kanban Board</a>
            <a href="/groups" class="nav-link"><span>🤝</span> Tim Grup</a>
            <a href="/profile" class="nav-link"><span>👤</span> Profil</a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-link"><span>📈</span> Insights</a>
            @endif
        </nav>

        <div style="margin-top: auto; padding: 20px; background: var(--soft-bg); border-radius: 18px; text-align: center; border: 1px solid var(--border-color);">
            <div style="font-size: 28px; margin-bottom: 8px;">🧊</div>
            <h4 style="font-size: 12px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px;">Arctic Zen</h4>
            <p style="font-size: 10px; color: var(--text-muted); margin-top: 4px; line-height: 1.5;">Tetap tenang di tengah tugas.</p>
        </div>
    </aside>

    <!-- Content -->
    <main class="animate-cheerful">
        <!-- Header -->
        <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 35px;">
            <div>
                <p style="font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 3px; margin-bottom: 8px;">{{ \Carbon\Carbon::now()->format('D, d M Y') }}</p>
                <h2 style="font-size: 30px; font-weight: 900; letter-spacing: -1px; color: var(--text-main); margin-bottom: 4px;">Selamat Fokus, {{ explode(' ', auth()->user()->name)[0] }}! 👋</h2>
                <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                    <span style="font-size: 11px; font-weight: 800; background: var(--accent-gradient); color: white; padding: 3px 12px; border-radius: 20px;">{{ $stats['title'] }}</span>
                    @if($stats['combo'] > 0)
                    <span style="font-size: 11px; font-weight: 800; background: rgba(239, 68, 68, 0.1); color: #EF4444; padding: 3px 12px; border-radius: 20px; animation: pulse 2s infinite;">🔥 Combo x{{ $stats['combo'] }}</span>
                    @endif
                    @if($stats['total_early'] > 0)
                    <span style="font-size: 11px; font-weight: 800; background: rgba(245, 158, 11, 0.1); color: #D97706; padding: 3px 12px; border-radius: 20px;">🏃 {{ $stats['total_early'] }} Early</span>
                    @endif
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 11px; font-weight: 900; color: #1E88E5; margin-bottom: 6px;">LEVEL {{ $stats['level'] }} — {{ $stats['title'] }}</div>
                <div style="width: 160px; height: 8px; background: rgba(30, 136, 229, 0.1); border-radius: 20px; overflow: hidden;">
                    <div style="width: {{ ($stats['xp'] / max($stats['xp_next'], 1)) * 100 }}%; height: 100%; background: var(--primary-gradient); border-radius: 20px; transition: width 0.5s ease;"></div>
                </div>
                <div style="font-size: 10px; color: var(--text-muted); margin-top: 3px; font-weight: 700;">{{ $stats['xp'] }} / {{ $stats['xp_next'] }} XP</div>
            </div>
        </header>

        <!-- AI Briefing -->
        <div class="zen-card" style="margin-bottom: 30px; border-left: 4px solid #1E88E5; padding: 22px 28px;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 42px; height: 42px; background: rgba(30, 136, 229, 0.1); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px;">🤖</div>
                <div>
                    <h4 style="font-size: 10px; font-weight: 800; color: #1E88E5; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px;">AI Daily Briefing</h4>
                    <p style="font-size: 13px; font-weight: 600; color: var(--text-main); line-height: 1.5;">{{ $stats['briefing'] }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid (Enhanced) -->
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 24px;">
            <div class="zen-card" style="padding: 20px; text-align: center; background: var(--primary-gradient); color: white; border: none;">
                <h4 style="font-size: 10px; opacity: 0.8; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px;">Focus Score</h4>
                <div style="font-size: 32px; font-weight: 900;">{{ $stats['completion_rate'] }}%</div>
                <div style="font-size: 9px; opacity: 0.7; margin-top: 4px;">{{ $stats['zen_correlation']['efficiency'] }} tugas / jam Zen</div>
            </div>
            <div class="zen-card" style="padding: 20px; text-align: center;">
                <h4 style="font-size: 10px; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px;">Daily Streak</h4>
                <div style="font-size: 32px; font-weight: 900; color: var(--text-main);">{{ $stats['streak'] }} 🔥</div>
            </div>
            <div class="zen-card" style="padding: 20px; text-align: center;">
                <h4 style="font-size: 10px; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px;">Combo</h4>
                <div style="font-size: 32px; font-weight: 900; color: {{ $stats['combo'] >= 5 ? '#EF4444' : ($stats['combo'] >= 3 ? '#F59E0B' : 'var(--text-main)') }};">{{ $stats['combo'] }}x</div>
                <div style="font-size: 9px; color: var(--text-muted); margin-top: 4px;">Best: {{ $stats['highest_combo'] }}x</div>
            </div>
            <div class="zen-card" style="padding: 20px; text-align: center;">
                <h4 style="font-size: 10px; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px;">Selesai Awal</h4>
                <div style="font-size: 32px; font-weight: 900; color: #F59E0B;">{{ $stats['total_early'] }} 🏃</div>
            </div>
        </div>

        <!-- Badges Strip -->
        @if(isset($userBadges))
        <div class="zen-card" style="padding: 16px 22px; margin-bottom: 24px;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                <h4 style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">🏆 Badges</h4>
                <span style="font-size: 10px; font-weight: 800; color: #1E88E5;">{{ collect($userBadges)->where('unlocked', true)->count() }}/{{ count($userBadges) }}</span>
            </div>
            <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 4px;">
                @foreach($userBadges as $badge)
                <div title="{{ $badge['name'] }}: {{ $badge['desc'] }}" style="min-width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; {{ $badge['unlocked'] ? 'background: var(--soft-bg); border: 1px solid var(--border-color);' : 'background: var(--soft-bg); opacity: 0.3; filter: grayscale(1);' }} transition: 0.3s; cursor: default;" {{ $badge['unlocked'] ? '' : '' }}>
                    {{ $badge['icon'] }}
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Leaderboard + Heatmap Row -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 30px;">
            <!-- Mini Leaderboard -->
            @if(isset($leaderboard) && $leaderboard->count() > 0)
            <div class="zen-card" style="padding: 22px;">
                <h4 style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 14px;">🏅 Leaderboard</h4>
                <div style="display: flex; flex-direction: column; gap: 8px;">
                    @foreach($leaderboard as $idx => $player)
                    <div style="display: flex; align-items: center; gap: 10px; padding: 8px 12px; border-radius: 12px; {{ $player->id === auth()->id() ? 'background: rgba(30, 136, 229, 0.08); border: 1px solid rgba(30, 136, 229, 0.15);' : 'background: var(--soft-bg);' }}">
                        <div style="font-size: 14px; font-weight: 900; color: {{ $idx === 0 ? '#F59E0B' : ($idx === 1 ? '#94A3B8' : ($idx === 2 ? '#CD7F32' : 'var(--text-muted)')) }}; width: 22px;">{{ $idx === 0 ? '🥇' : ($idx === 1 ? '🥈' : ($idx === 2 ? '🥉' : '#'.($idx+1))) }}</div>
                        <div style="width: 28px; height: 28px; border-radius: 8px; background: {{ $player->avatar_color ?? '#1E88E5' }}; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 900;">{{ strtoupper(substr($player->name, 0, 1)) }}</div>
                        <div style="flex-grow: 1;">
                            <div style="font-size: 12px; font-weight: 700; color: var(--text-main);">{{ Str::limit($player->name, 15) }}</div>
                            <div style="font-size: 10px; color: var(--text-muted); font-weight: 600;">LVL {{ $player->level }} • {{ $player->title ?? 'Pemula' }}</div>
                        </div>
                        <div style="font-size: 12px; font-weight: 900; color: #1E88E5;">{{ $player->xp }} XP</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Heatmap -->
            <div class="zen-card" style="padding: 22px;">
                <h4 style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 14px; letter-spacing: 1px;">📊 Heatmap (90 Hari)</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 3px; justify-content: center; max-width: 100%;">
                    @foreach($stats['heatmap'] as $day)
                    <div style="width: 10px; height: 10px; border-radius: 3px; background: {{ $day['count'] > 0 ? ($day['count'] > 3 ? '#1565C0' : ($day['count'] > 2 ? '#1E88E5' : ($day['count'] > 1 ? '#64B5F6' : '#BBDEFB'))) : 'var(--soft-bg)' }}; border: 1px solid {{ $day['count'] > 0 ? 'transparent' : 'var(--border-color)' }};" title="{{ $day['date'] }}: {{ $day['count'] }} tugas selesai"></div>
                    @endforeach
                </div>
                <div style="display: flex; justify-content: center; gap: 6px; margin-top: 10px; align-items: center;">
                    <span style="font-size: 9px; color: var(--text-muted); font-weight: 700;">Sedikit</span>
                    <div style="width: 10px; height: 10px; border-radius: 3px; background: #BBDEFB;"></div>
                    <div style="width: 10px; height: 10px; border-radius: 3px; background: #64B5F6;"></div>
                    <div style="width: 10px; height: 10px; border-radius: 3px; background: #1E88E5;"></div>
                    <div style="width: 10px; height: 10px; border-radius: 3px; background: #1565C0;"></div>
                    <span style="font-size: 9px; color: var(--text-muted); font-weight: 700;">Banyak</span>
                </div>
            </div>
        </div>

        <!-- Search -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 22px;">
            <h3 style="font-size: 18px; font-weight: 800; color: var(--text-main);">Daftar Agenda</h3>
            <div style="background: var(--card-bg); padding: 8px 16px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 14px;">🔍</span>
                <input type="text" id="search" placeholder="Cari..." style="border: none; background: none; outline: none; font-size: 13px; font-weight: 600; color: var(--text-main); width: 140px; font-family: inherit;" onkeyup="doSearch()">
            </div>
        </div>

        <!-- Task Sections -->
        <div id="list" style="display: grid; gap: 30px;">
            
            <!-- TODAY & URGENT -->
            <div>
                <h3 class="section-title" style="color: #1E88E5;">
                    <span>⚡</span> Agenda Hari Ini & Mendesak
                </h3>
                <div style="display: grid; gap: 12px;">
                    @php 
                        $todayTasks = $schedules->filter(fn($item) => !$item->is_completed && ($item->date == date('Y-m-d') || $item->is_missed));
                    @endphp
                    @forelse($todayTasks as $item)
                        @include('schedules.partials.task_card', ['item' => $item])
                    @empty
                        <div class="zen-card" style="text-align: center; padding: 35px; background: rgba(255,255,255,0.5); border: 1px dashed var(--border-color);">
                            <p style="font-size: 28px; margin-bottom: 8px;">🧊</p>
                            <p style="color: var(--text-muted); font-size: 13px; font-weight: 700;">Tidak ada agenda mendesak. Santai dulu!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- FUTURE TASKS -->
            <div>
                <h3 class="section-title" style="color: #64748b;">
                    <span>📅</span> Persiapan Mendatang
                </h3>
                <div style="display: grid; gap: 12px;">
                    @php 
                        $futureTasks = $schedules->filter(fn($item) => !$item->is_completed && $item->date > date('Y-m-d'));
                    @endphp
                    @forelse($futureTasks as $item)
                        @include('schedules.partials.task_card', ['item' => $item, 'is_future' => true])
                    @empty
                        <p style="color: var(--text-muted); font-size: 12px; font-style: italic; padding-left: 10px;">Belum ada rencana mendatang.</p>
                    @endforelse
                </div>
            </div>

            <!-- COMPLETED -->
            <div x-data="{ showDone: false }">
                <button @click="showDone = !showDone" style="background: none; border: none; font-size: 13px; font-weight: 800; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; gap: 8px; margin-bottom: 16px; font-family: inherit;">
                    <span x-text="showDone ? '🔽' : '▶️'"></span> Tugas Selesai ({{ $schedules->where('is_completed', true)->count() }})
                </button>
                <div x-show="showDone" x-transition style="display: grid; gap: 12px;">
                    @foreach($schedules->where('is_completed', true) as $item)
                        @include('schedules.partials.task_card', ['item' => $item])
                    @endforeach
                </div>
            </div>
        </div>
    </main>

    <!-- Create Modal -->
    <div class="arctic-overlay" x-show="showCreate" x-cloak x-transition @click.self="showCreate = false">
        <div class="zen-card" style="width: 500px; padding: 35px; border: none; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 style="margin: 0; font-size: 22px; font-weight: 900; letter-spacing: -1px;">🎯 Agenda Baru</h2>
                <button @click="showCreate = false" style="background: none; border: none; font-size: 28px; cursor: pointer; color: var(--text-muted); font-family: inherit;">&times;</button>
            </div>
            <form action="/schedules" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="activity_name" class="arctic-input" placeholder="Apa kegiatannya?" required>
                <input type="text" name="group_name" class="arctic-input" placeholder="Grup atau Lokasi" required>
                
                <!-- Notes -->
                <textarea name="notes" class="arctic-input" placeholder="Catatan tambahan (opsional)..." style="min-height: 70px; resize: vertical; font-family: inherit;"></textarea>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <input type="date" name="date" class="arctic-input" value="{{ date('Y-m-d') }}" required>
                    <input type="time" name="time" class="arctic-input" value="{{ date('H:i') }}" required>
                </div>

                <!-- Priority Selector -->
                <label style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 8px;">PRIORITAS</label>
                <div class="priority-group">
                    <div class="priority-option" :class="{ 'selected': selectedPriority === 'low' }" 
                        @click="selectedPriority = 'low'" 
                        :style="selectedPriority === 'low' ? 'background: #10B981; border-color: #10B981; color: white;' : ''">
                        🟢 Rendah
                    </div>
                    <div class="priority-option" :class="{ 'selected': selectedPriority === 'med' }" 
                        @click="selectedPriority = 'med'"
                        :style="selectedPriority === 'med' ? 'background: #F59E0B; border-color: #F59E0B; color: white;' : ''">
                        🟡 Sedang
                    </div>
                    <div class="priority-option" :class="{ 'selected': selectedPriority === 'high' }" 
                        @click="selectedPriority = 'high'"
                        :style="selectedPriority === 'high' ? 'background: #EF4444; border-color: #EF4444; color: white;' : ''">
                        🔴 Tinggi
                    </div>
                </div>
                <input type="hidden" name="priority" x-model="selectedPriority">

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <select name="category" class="arctic-input">
                        <option value="Olahraga">💪 Olahraga</option>
                        <option value="Belajar">📚 Belajar</option>
                        <option value="Rapat">🤝 Rapat</option>
                        <option value="Lainnya">☕ Lainnya</option>
                    </select>
                    <select name="group_id" class="arctic-input">
                        <option value="">👤 Personal</option>
                        @foreach($groups as $g)
                        <option value="{{ $g->id }}">🤝 {{ $g->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 12px;">
                    <label style="font-size: 10px; font-weight: 800; color: var(--text-muted); display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 1px;">TUGAS PRASYARAT</label>
                    <select name="dependency_id" class="arctic-input">
                        <option value="">-- Tanpa Prasyarat --</option>
                        @foreach($schedules->where('is_completed', false) as $t)
                        <option value="{{ $t->id }}">⛓️ {{ $t->activity_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div style="background: var(--soft-bg); padding: 14px; border-radius: 14px; margin-bottom: 20px; border: 1px dashed var(--border-color);">
                    <label style="font-size: 10px; font-weight: 800; color: var(--text-muted); display: block; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 1px;">📎 LAMPIRAN</label>
                    <input type="file" name="attachment_file" style="font-size: 12px; font-family: inherit;">
                </div>

                <button type="submit" class="btn-arctic" style="width: 100%; padding: 16px; font-size: 15px;">Simpan Agenda</button>
            </form>
        </div>
    </div>

    <!-- Zen Mode -->
    <div class="zen-overlay" x-show="zen" x-cloak x-transition>
        <button @click="zen = false; if(timerActive) toggleTimer()" style="position: absolute; top: 30px; right: 30px; background: none; border: 1px solid var(--border-color); font-size: 13px; font-weight: 800; color: var(--text-muted); cursor: pointer; text-transform: uppercase; letter-spacing: 2px; padding: 10px 25px; border-radius: 14px; font-family: inherit;">Keluar Zen &times;</button>
        
        <div style="text-align: center; max-width: 600px; width: 100%;">
            <p style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 6px; margin-bottom: 40px;">Sekarang Fokus Pada:</p>
            
            <div class="zen-card" style="padding: 60px; border: none; box-shadow: 0 30px 80px rgba(30, 136, 229, 0.12);">
                <h1 style="font-size: 42px; font-weight: 900; color: var(--text-main); margin-bottom: 15px; letter-spacing: -2px;" x-text="urgentTask"></h1>
                <p style="font-size: 15px; color: var(--text-muted); font-weight: 600; margin-bottom: 50px;">Matikan notifikasi, ambil nafas dalam-dalam.</p>
                
                <div style="font-size: 100px; font-weight: 900; font-family: 'JetBrains Mono', monospace; color: var(--text-main); margin-bottom: 35px; letter-spacing: -3px;">
                    <span x-text="focusMinutes.toString().padStart(2, '0')"></span>:<span x-text="focusSeconds.toString().padStart(2, '0')"></span>
                </div>
                
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <button class="btn-arctic" @click="toggleTimer(); if(!timerActive && focusMinutes < 25) logZen()" x-text="timerActive ? 'JEDA' : 'MULAI'" style="padding: 16px 50px; border-radius: 50px; font-size: 13px; letter-spacing: 2px;"></button>
                    <button class="btn-arctic" @click="focusMinutes = 25; focusSeconds = 0; if(timerActive) toggleTimer()" style="background: var(--soft-bg); color: var(--text-main); border: 1px solid var(--border-color); box-shadow: none; padding: 16px 35px; border-radius: 50px;">RESET</button>
                </div>
            </div>

            <script>
                function logZen() {
                    fetch('/zen/log', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ minutes: 5 })
                    });
                }
            </script>
            
            <div style="margin-top: 40px; display: flex; align-items: center; justify-content: center; gap: 12px;">
                <div style="width: 30px; height: 1px; background: var(--border-color);"></div>
                <p style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px;">🎵 Arctic Lo-Fi beats</p>
                <div style="width: 30px; height: 1px; background: var(--border-color);"></div>
            </div>
        </div>
    </div>
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