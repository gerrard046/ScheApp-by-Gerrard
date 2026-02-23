@extends('layouts.app')

@section('content')
<style>
    /* Reset & Base Layout */
    .main-wrapper {
        display: flex;
        min-height: 100vh;
        background: var(--soft-bg);
    }

    /* Minimalist Sidebar */
    aside {
        width: 280px;
        background: var(--card-bg);
        border-right: 1px solid var(--border-color);
        padding: 40px 30px;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        height: 100vh;
        backdrop-filter: blur(10px);
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 18px;
        border-radius: 15px;
        color: var(--text-main);
        text-decoration: none;
        font-weight: 700;
        transition: 0.3s;
        margin-bottom: 10px;
        font-size: 15px;
    }

    .nav-link:hover {
        background: var(--soft-bg);
        transform: translateX(5px);
    }

    .nav-link.active {
        background: var(--primary-gradient);
        color: white;
        box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2);
    }

    /* Content Area */
    main {
        flex-grow: 1;
        padding: 50px;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Arctic Glass Cards */
    .zen-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 30px;
        box-shadow: var(--vibrant-shadow);
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .zen-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(30, 136, 229, 0.1);
    }

    /* Floating Action Buttons */
    .fab-container {
        position: fixed;
        bottom: 40px;
        right: 40px;
        display: flex;
        flex-direction: column;
        gap: 20px;
        z-index: 10000;
    }

    .fab {
        width: 65px;
        height: 65px;
        border-radius: 20px;
        background: var(--primary-gradient);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        cursor: pointer;
        box-shadow: 0 15px 35px rgba(30, 136, 229, 0.3);
        transition: 0.3s;
        border: none;
    }

    .fab:hover {
        transform: scale(1.1) rotate(3deg);
    }

    .fab-zen {
        background: #1E293B;
        font-size: 20px;
    }

    /* Modals & Overlays */
    .arctic-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(12px);
        z-index: 20000;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .zen-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--soft-bg);
        z-index: 20000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-item {
        transition: 0.3s;
    }
    .card-item:hover { transform: scale(1.01); }

    /* Input Styles Refinement */
    .arctic-input {
        width: 100%;
        padding: 16px 20px;
        background: var(--soft-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        color: var(--text-main);
        font-weight: 600;
        outline: none;
        transition: 0.3s;
        margin-bottom: 15px;
    }

    .arctic-input:focus {
        border-color: #1E88E5;
        box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.1);
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: rgba(30, 136, 229, 0.1); border-radius: 10px; }
</style>

<div class="main-wrapper" x-data="{ 
    showCreate: false, 
    zen: false,
    focusMinutes: 25,
    focusSeconds: 0,
    timerActive: false,
    timerId: null,
    urgentTask: '{{ $schedules->where('is_completed', false)->sortBy('date')->first()->activity_name ?? 'Istirahat' }}',

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

    <!-- FABs -->
    <div class="fab-container">
        <button class="fab fab-zen" @click="zen = true" title="Zen Mode">🧘</button>
        @if(auth()->user()->role === 'admin')
        <button class="fab" @click="showCreate = true" title="Tambah Agenda">+</button>
        @endif
    </div>

    <!-- Sidebar -->
    <aside>
        <div style="margin-bottom: 50px;">
            <h1 style="font-size: 28px; font-weight: 900; letter-spacing: -2px; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="color: #1E88E5;">⚡</span> ScheApp
            </h1>
        </div>

        <nav>
            <a href="/schedules" class="nav-link active"><span>🏠</span> Dashboard</a>
            <a href="/calendar" class="nav-link"><span>📅</span> Kalender</a>
            <a href="/groups" class="nav-link"><span>🤝</span> Tim Grup</a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-link"><span>📈</span> Insights</a>
            @endif
        </nav>

        <div style="margin-top: auto; padding: 25px; background: var(--soft-bg); border-radius: 24px; text-align: center; border: 1px solid var(--border-color);">
            <div style="font-size: 32px; margin-bottom: 10px;">🧊</div>
            <h4 style="font-size: 13px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px;">Arctic Zen</h4>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px; line-height: 1.5;">Tetap tenang di tengah gempuran tugas.</p>
        </div>
    </aside>

    <!-- Content -->
    <main>
        <!-- Header -->
        <header style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 50px;">
            <div>
                <p style="font-size: 13px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 3px; margin-bottom: 10px;">{{ \Carbon\Carbon::now()->format('D, d M Y') }}</p>
                <h2 style="font-size: 36px; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main);">Selamat Fokus, {{ explode(' ', auth()->user()->name)[0] }}!</h2>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 12px; font-weight: 900; color: #1E88E5; margin-bottom: 8px;">LEVEL {{ $stats['level'] }}</div>
                <div style="width: 180px; height: 10px; background: rgba(30, 136, 229, 0.1); border-radius: 20px; overflow: hidden; border: 1px solid var(--border-color);">
                    <div style="width: {{ ($stats['xp'] / $stats['xp_next']) * 100 }}%; height: 100%; background: var(--primary-gradient);"></div>
                </div>
            </div>
        </header>

        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 40px;">
            <div class="zen-card" style="padding: 25px; text-align: center; background: var(--primary-gradient); color: white; border: none;">
                <h4 style="font-size: 12px; opacity: 0.8; margin-bottom: 10px;">FOCUS SCORE</h4>
                <div style="font-size: 42px; font-weight: 900;">{{ $stats['completion_rate'] }}%</div>
            </div>
            <div class="zen-card" style="padding: 25px; text-align: center;">
                <h4 style="font-size: 12px; color: var(--text-muted); margin-bottom: 10px;">DAILY STREAK</h4>
                <div style="font-size: 42px; font-weight: 900; color: var(--text-main);">{{ $stats['streak'] }} 🔥</div>
            </div>
            <div class="zen-card" style="padding: 25px; text-align: center;">
                <h4 style="font-size: 12px; color: var(--text-muted); margin-bottom: 10px;">WEEKLY PRODUCTIVITY</h4>
                <div style="display: flex; gap: 8px; justify-content: center; margin-top: 10px;">
                    @foreach($stats['heatmap'] as $day)
                    <div style="width: 14px; height: 14px; border-radius: 4px; background: {{ $day['count'] > 0 ? '#1E88E5' : 'var(--soft-bg)' }};" title="{{ $day['date'] }}"></div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Task List Header -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h3 style="font-size: 20px; font-weight: 800; color: var(--text-main);">Daftar Agenda</h3>
            <div style="background: var(--card-bg); padding: 10px 20px; border-radius: 12px; border: 1px solid var(--border-color); display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 14px;">🔍</span>
                <input type="text" id="search" placeholder="Cari..." style="border: none; background: none; outline: none; font-size: 13px; font-weight: 600; color: var(--text-main);" onkeyup="doSearch()">
            </div>
        </div>

        <!-- Task Sections -->
        <div id="list" style="display: grid; gap: 35px;">
            
            <!-- 1. AGENDA HARI INI -->
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #1E88E5; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <span>⚡</span> Agenda Hari Ini & Mendesak
                </h3>
                <div style="display: grid; gap: 15px;">
                    @php 
                        $todayTasks = $schedules->filter(fn($item) => !$item->is_completed && ($item->date == date('Y-m-d') || $item->is_missed));
                    @endphp
                    @forelse($todayTasks as $item)
                        @include('schedules.partials.task_card', ['item' => $item])
                    @empty
                        <div class="zen-card" style="text-align: center; padding: 40px; background: rgba(255,255,255,0.5); border: 1px dashed var(--border-color);">
                            <p style="color: var(--text-muted); font-size: 14px; font-weight: 700;">Tidak ada agenda mendesak hari ini. Santai dulu! 🧊</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 2. RENCANA MENDATANG (Lakukan Lebih Awal) -->
            <div>
                <h3 style="font-size: 16px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <span>📅</span> Persiapan Masa Depan (Lakukan Lebih Awal)
                </h3>
                <div style="display: grid; gap: 15px;">
                    @php 
                        $futureTasks = $schedules->filter(fn($item) => !$item->is_completed && $item->date > date('Y-m-d'));
                    @endphp
                    @forelse($futureTasks as $item)
                        @include('schedules.partials.task_card', ['item' => $item, 'is_future' => true])
                    @empty
                        <p style="color: var(--text-muted); font-size: 13px; font-style: italic; padding-left: 10px;">Belum ada rencana masa depan. Tambahkan sekarang?</p>
                    @endforelse
                </div>
            </div>

            <!-- 3. RIWAYAT SELESAI -->
            <div x-data="{ showDone: false }">
                <button @click="showDone = !showDone" style="background: none; border: none; font-size: 14px; font-weight: 800; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; gap: 10px; margin-bottom: 20px;">
                    <span x-text="showDone ? '🔽' : '▶️'"></span> Lihat Tugas Selesai ({{ $schedules->where('is_completed', true)->count() }})
                </button>
                <div x-show="showDone" x-transition style="display: grid; gap: 15px;">
                    @foreach($schedules->where('is_completed', true) as $item)
                        @include('schedules.partials.task_card', ['item' => $item])
                    @endforeach
                </div>
            </div>
        </div>
    </main>

    <!-- Create Modal -->
    <div class="arctic-overlay" x-show="showCreate" x-cloak x-transition @click.self="showCreate = false">
        <div class="zen-card" style="width: 500px; padding: 40px; border: none;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                <h2 style="margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -1px;">🎯 Agenda Baru</h2>
                <button @click="showCreate = false" style="background: none; border: none; font-size: 30px; cursor: pointer; color: var(--text-muted);">&times;</button>
            </div>
            <form action="/schedules" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="text" name="activity_name" class="arctic-input" placeholder="Apa kegiatannya?" required>
                <input type="text" name="group_name" class="arctic-input" placeholder="Grup atau Lokasi" required>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <input type="date" name="date" class="arctic-input" value="{{ date('Y-m-d') }}" required>
                    <input type="time" name="time" class="arctic-input" value="{{ date('H:i') }}" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
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
                <div style="background: var(--soft-bg); padding: 15px; border-radius: 15px; margin-bottom: 25px; border: 1px dashed var(--border-color);">
                    <label style="font-size: 11px; font-weight: 800; color: var(--text-muted); display: block; margin-bottom: 10px;">FILE LAMPIRAN (PDF/IMAGE)</label>
                    <input type="file" name="attachment_file" style="font-size: 12px;">
                </div>
                <button type="submit" class="btn-arctic" style="width: 100%; padding: 18px; background: var(--primary-gradient); border: none; font-size: 16px; margin-top: 0;">Simpan Agenda Arctic</button>
            </form>
        </div>
    </div>

    <!-- Zen Mode -->
    <div class="zen-overlay" x-show="zen" x-cloak x-transition>
        <button @click="zen = false; if(timerActive) toggleTimer()" style="position: absolute; top: 40px; right: 40px; background: none; border: none; font-size: 14px; font-weight: 800; color: #1E88E5; cursor: pointer; text-transform: uppercase; letter-spacing: 2px;">Keluar Zen &times;</button>
        
        <div style="text-align: center; max-width: 700px; width: 100%;">
            <p style="font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 6px; margin-bottom: 50px;">Sekarang Fokus Pada:</p>
            
            <div class="zen-card" style="padding: 70px; border: none; box-shadow: 0 50px 100px rgba(30, 136, 229, 0.15); background: white;">
                <h1 style="font-size: 56px; font-weight: 900; color: var(--text-main); margin-bottom: 20px; letter-spacing: -2px;" x-text="urgentTask"></h1>
                <p style="font-size: 18px; color: var(--text-muted); font-weight: 600; margin-bottom: 60px;">Matikan notifikasi, ambil nafas dalam-dalam.</p>
                
                <div style="font-size: 120px; font-weight: 900; font-family: 'JetBrains Mono', monospace; color: var(--text-main); margin-bottom: 40px; letter-spacing: -5px;">
                    <span x-text="focusMinutes.toString().padStart(2, '0')"></span>:<span x-text="focusSeconds.toString().padStart(2, '0')"></span>
                </div>
                
                <div style="display: flex; gap: 20px; justify-content: center;">
                    <button class="btn-arctic" @click="toggleTimer()" x-text="timerActive ? 'JEDA' : 'MULAI'" style="padding: 20px 60px; border-radius: 50px; font-size: 14px; letter-spacing: 2px; width: auto; margin-top: 0;"></button>
                    <button class="btn-arctic" @click="focusMinutes = 25; focusSeconds = 0; if(timerActive) toggleTimer()" style="background: var(--soft-bg); color: var(--text-main); border: 1px solid var(--border-color); box-shadow: none; padding: 20px 40px; border-radius: 50px; width: auto; margin-top: 0;">RESET</button>
                </div>
            </div>
            
            <div style="margin-top: 60px; display: flex; align-items: center; justify-content: center; gap: 15px;">
                <div style="width: 40px; height: 1px; background: var(--border-color);"></div>
                <p style="font-size: 12px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px;">🎵 Memutar: Arctic Lo-Fi beats</p>
                <div style="width: 40px; height: 1px; background: var(--border-color);"></div>
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