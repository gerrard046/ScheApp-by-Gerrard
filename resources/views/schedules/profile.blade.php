@extends('layouts.app')

@section('content')
<style>
    .main-wrapper { display: flex; min-height: 100vh; background: var(--soft-bg); }
    
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
    .nav-link:hover { background: var(--soft-bg); transform: translateX(4px); }
    .nav-link.active { background: var(--primary-gradient); color: white; box-shadow: 0 6px 18px rgba(30, 136, 229, 0.25); }

    main { flex-grow: 1; padding: 40px; max-width: 1200px; }

    /* Profile Hero */
    .profile-hero {
        background: var(--primary-gradient);
        border-radius: 28px;
        padding: 45px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .profile-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .profile-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 20%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }

    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: 900;
        color: white;
        border: 3px solid rgba(255,255,255,0.3);
        backdrop-filter: blur(10px);
        background: rgba(255,255,255,0.15);
    }

    .level-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Stat Mini Cards */
    .stat-mini {
        background: var(--card-bg);
        padding: 22px;
        border-radius: 18px;
        border: 1px solid var(--border-color);
        box-shadow: var(--vibrant-shadow);
        text-align: center;
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .stat-mini:hover { transform: translateY(-4px); }
    .stat-mini .stat-val { font-size: 28px; font-weight: 900; color: var(--text-main); }
    .stat-mini .stat-label { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }

    /* Achievement Card */
    .achievement-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
    .achv-item {
        padding: 18px;
        border-radius: 16px;
        text-align: center;
        border: 1px solid var(--border-color);
        background: var(--card-bg);
        transition: 0.3s;
        position: relative;
        overflow: hidden;
    }
    .achv-item.locked { 
        opacity: 0.35;
        filter: grayscale(1);
    }
    .achv-item.unlocked {
        box-shadow: var(--vibrant-shadow);
    }
    .achv-item:hover { transform: scale(1.03); }
    .achv-icon { font-size: 28px; margin-bottom: 6px; }
    .achv-name { font-size: 11px; font-weight: 800; color: var(--text-main); margin-bottom: 3px; }
    .achv-desc { font-size: 9px; color: var(--text-muted); line-height: 1.4; }
    .achv-item.unlocked::after {
        content: '✓';
        position: absolute;
        top: 6px;
        right: 8px;
        background: #10B981;
        color: white;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 9px;
        font-weight: 900;
    }

    .achv-category-title {
        font-size: 11px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin: 20px 0 12px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Weekly Bar Chart */
    .weekly-bar {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        height: 120px;
        padding: 0 10px;
    }
    .bar-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
    }
    .bar-fill {
        width: 100%;
        min-height: 8px;
        background: var(--primary-gradient);
        border-radius: 8px 8px 4px 4px;
        transition: height 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .bar-label { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; }
    .bar-val { font-size: 11px; font-weight: 900; color: var(--text-main); }

    /* Category Pills */
    .cat-pill {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border-radius: 12px;
        background: var(--soft-bg);
        margin-bottom: 8px;
        transition: 0.3s;
    }
    .cat-pill:hover { transform: translateX(5px); }

    /* Zen Card */
    .zen-card {
        background: var(--card-bg);
        padding: 28px;
        border-radius: 22px;
        box-shadow: var(--vibrant-shadow);
        border: 1px solid var(--border-color);
        transition: 0.4s;
    }
    .zen-card:hover { transform: translateY(-4px); }

    /* Edit Profile Form */
    .edit-form .arctic-input {
        margin-bottom: 15px;
    }

    .color-picker-group {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
        flex-wrap: wrap;
    }
    .color-dot {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        cursor: pointer;
        transition: 0.3s;
        border: 3px solid transparent;
    }
    .color-dot:hover, .color-dot.active {
        transform: scale(1.15);
        border-color: var(--text-main);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Alert */
    .alert-toast {
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

    /* Responsive */
    @media (max-width: 768px) {
        aside { display: none; }
        main { padding: 20px !important; }
        .profile-hero { padding: 25px; }
        .achievement-grid { grid-template-columns: repeat(2, 1fr); }
    }
</style>

<div class="main-wrapper">
    <aside>
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 22px; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                <span style="color: #1E88E5;">⚡</span> ScheApp
            </h1>
        </div>
        
        <nav>
            <a href="/schedules" class="nav-link"><span>🏠</span> Dashboard</a>
            <a href="/calendar" class="nav-link"><span>📅</span> Kalender</a>
            <a href="/kanban" class="nav-link"><span>📋</span> Kanban Board</a>
            <a href="/groups" class="nav-link"><span>🤝</span> Tim Grup</a>
            <a href="/profile" class="nav-link active"><span>👤</span> Profil</a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-link"><span>📈</span> Insights</a>
            @endif
        </nav>

        <div style="margin-top: auto; padding: 20px; background: var(--soft-bg); border-radius: 18px; text-align: center; border: 1px solid var(--border-color);">
            <div style="font-size: 28px; margin-bottom: 8px;">👤</div>
            <h4 style="font-size: 12px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px;">Profile</h4>
            <p style="font-size: 10px; color: var(--text-muted); margin-top: 4px; line-height: 1.5;">Kelola profil dan lacak pencapaianmu.</p>
        </div>
    </aside>

    <main>
        @if(session('success'))
        <div class="alert-toast" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)">
            <div style="background: #10B981; color: white; padding: 15px 25px; border-radius: 16px; font-weight: 700; font-size: 14px; box-shadow: 0 10px 25px rgba(16, 185, 129, 0.3);">
                ✅ {{ session('success') }}
            </div>
        </div>
        @endif

        <!-- Profile Hero -->
        <div class="profile-hero">
            <div style="display: flex; align-items: center; gap: 22px; position: relative; z-index: 1;">
                <div class="avatar-circle" style="background: {{ $user->avatar_color ?? '#1E88E5' }}40;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div style="flex-grow: 1;">
                    <h1 style="font-size: 26px; font-weight: 900; letter-spacing: -1px; margin-bottom: 4px;">{{ $user->name }}</h1>
                    <p style="opacity: 0.8; font-size: 13px; font-weight: 600; margin-bottom: 10px;">{{ $user->bio ?? 'Belum ada bio. Edit profilmu!' }}</p>
                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                        <span class="level-badge">LVL {{ $user->level }}</span>
                        <span class="level-badge" style="background: var(--accent-gradient); border: none;">{{ $title }}</span>
                        <span class="level-badge">🔥 {{ $user->streak }} Streak</span>
                        <span class="level-badge">{{ $user->xp }} XP</span>
                        @if(($gamificationStats['combo'] ?? 0) > 0)
                        <span class="level-badge" style="background: rgba(239, 68, 68, 0.3);">🔥 Combo x{{ $gamificationStats['combo'] }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <div style="margin-top: 22px; position: relative; z-index: 1;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <span style="font-size: 10px; font-weight: 800; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px;">Progress ke Level {{ $user->level + 1 }}</span>
                    <span style="font-size: 10px; font-weight: 800;">{{ $user->xp }} / {{ $user->level * 100 }} XP</span>
                </div>
                <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.2); border-radius: 20px; overflow: hidden;">
                    <div style="width: {{ min(($user->xp / max($user->level * 100, 1)) * 100, 100) }}%; height: 100%; background: white; border-radius: 20px; transition: width 0.8s ease;"></div>
                </div>
            </div>
        </div>

        <!-- Stats Grid (Enhanced) -->
        <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 14px; margin-bottom: 30px;">
            <div class="stat-mini">
                <div class="stat-label">Total Tugas</div>
                <div class="stat-val">{{ $totalTasks }}</div>
            </div>
            <div class="stat-mini">
                <div class="stat-label">Selesai</div>
                <div class="stat-val" style="color: #10B981;">{{ $completedTasks }}</div>
            </div>
            <div class="stat-mini">
                <div class="stat-label">Completion</div>
                <div class="stat-val" style="color: #1E88E5;">{{ $completionRate }}%</div>
            </div>
            <div class="stat-mini">
                <div class="stat-label">Zen Time</div>
                <div class="stat-val" style="color: #8B5CF6;">{{ $zenTotal }}m</div>
            </div>
            <div class="stat-mini">
                <div class="stat-label">Selesai Awal</div>
                <div class="stat-val" style="color: #F59E0B;">{{ $earlyCompletions }} 🏃</div>
            </div>
            <div class="stat-mini">
                <div class="stat-label">Best Combo</div>
                <div class="stat-val" style="color: #EF4444;">{{ $gamificationStats['highest_combo'] }}x</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 30px;">
            <!-- Weekly Productivity -->
            <div class="zen-card">
                <h3 style="font-size: 13px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 22px;">📊 Produktivitas Mingguan</h3>
                <div class="weekly-bar">
                    @foreach($weeklyData as $day)
                    <div class="bar-col">
                        <div class="bar-val">{{ $day['completed'] }}</div>
                        <div class="bar-fill" style="height: {{ $day['completed'] > 0 ? max($day['completed'] * 20, 12) : 8 }}px; {{ $day['completed'] == 0 ? 'background: var(--soft-bg); border: 1px solid var(--border-color);' : '' }}"></div>
                        <div class="bar-label">{{ $day['day'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Category Breakdown -->
            <div class="zen-card">
                <h3 style="font-size: 13px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px; margin-bottom: 22px;">📂 Kategori</h3>
                @php
                    $catColors = ['Olahraga' => '#EF4444', 'Belajar' => '#1E88E5', 'Rapat' => '#F59E0B', 'Lainnya' => '#8B5CF6'];
                    $catIcons = ['Olahraga' => '💪', 'Belajar' => '📚', 'Rapat' => '🤝', 'Lainnya' => '☕'];
                @endphp
                @forelse($categoryBreakdown as $cat)
                <div class="cat-pill">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 10px; height: 10px; border-radius: 4px; background: {{ $catColors[$cat->category] ?? '#94A3B8' }};"></div>
                        <span style="font-size: 12px; font-weight: 700; color: var(--text-main);">{{ $catIcons[$cat->category] ?? '📌' }} {{ $cat->category }}</span>
                    </div>
                    <span style="font-size: 12px; font-weight: 900; color: var(--text-main);">{{ $cat->count }}</span>
                </div>
                @empty
                <p style="color: var(--text-muted); font-size: 12px; text-align: center; padding: 20px;">Belum ada data kategori</p>
                @endforelse
            </div>
        </div>

        <!-- Achievements (Enhanced with Categories) -->
        <div class="zen-card" style="margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 13px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px;">🏆 Achievements & Badges</h3>
                <span style="font-size: 11px; font-weight: 800; color: #1E88E5; background: rgba(30,136,229,0.1); padding: 5px 12px; border-radius: 10px;">
                    {{ collect($achievements)->where('unlocked', true)->count() }} / {{ count($achievements) }} Unlocked
                </span>
            </div>

            @php
                $categories = [
                    'milestone' => ['title' => '🎯 Milestone Tugas', 'items' => []],
                    'speed' => ['title' => '🏃 Kecepatan', 'items' => []],
                    'streak' => ['title' => '🔥 Streak', 'items' => []],
                    'combo' => ['title' => '💥 Combo', 'items' => []],
                    'level' => ['title' => '⚡ Level', 'items' => []],
                    'zen' => ['title' => '🧘 Zen Mode', 'items' => []],
                ];
                foreach ($achievements as $achv) {
                    $cat = $achv['category'] ?? 'milestone';
                    if (isset($categories[$cat])) {
                        $categories[$cat]['items'][] = $achv;
                    }
                }
            @endphp

            @foreach($categories as $catKey => $cat)
                @if(count($cat['items']) > 0)
                <div class="achv-category-title">{{ $cat['title'] }}</div>
                <div class="achievement-grid">
                    @foreach($cat['items'] as $achv)
                    <div class="achv-item {{ $achv['unlocked'] ? 'unlocked' : 'locked' }}">
                        <div class="achv-icon">{{ $achv['icon'] }}</div>
                        <div class="achv-name">{{ $achv['name'] }}</div>
                        <div class="achv-desc">{{ $achv['desc'] }}</div>
                    </div>
                    @endforeach
                </div>
                @endif
            @endforeach
        </div>

        <!-- Gamification Summary Card -->
        <div class="zen-card" style="margin-bottom: 30px; background: var(--primary-gradient); color: white; border: none;">
            <h3 style="font-size: 13px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px; opacity: 0.8;">🎮 Ringkasan Gamifikasi</h3>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                <div style="text-align: center;">
                    <div style="font-size: 28px; font-weight: 900;">{{ $user->level }}</div>
                    <div style="font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px;">Level</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 28px; font-weight: 900;">{{ $gamificationStats['total_xp_earned'] }}</div>
                    <div style="font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px;">Total XP</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 28px; font-weight: 900;">{{ $gamificationStats['badges_count'] }}</div>
                    <div style="font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px;">Badges</div>
                </div>
                <div style="text-align: center;">
                    <div style="font-size: 28px; font-weight: 900;">{{ $title }}</div>
                    <div style="font-size: 10px; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px;">Title</div>
                </div>
            </div>
            
            <!-- Next milestones -->
            <div style="margin-top: 20px; padding-top: 16px; border-top: 1px solid rgba(255,255,255,0.2);">
                <div style="font-size: 11px; font-weight: 800; opacity: 0.7; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px;">Target Berikutnya</div>
                <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                    @php
                        $nextBadges = collect($achievements)->where('unlocked', false)->take(3);
                    @endphp
                    @foreach($nextBadges as $next)
                    <div style="background: rgba(255,255,255,0.15); padding: 8px 14px; border-radius: 12px; display: flex; align-items: center; gap: 6px;">
                        <span style="font-size: 16px;">{{ $next['icon'] }}</span>
                        <span style="font-size: 11px; font-weight: 700;">{{ $next['name'] }}</span>
                    </div>
                    @endforeach
                    @if($nextBadges->isEmpty())
                    <div style="font-size: 12px; font-weight: 700; opacity: 0.7;">🎉 Semua badge sudah terbuka!</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Edit Profile -->
        <div class="zen-card" x-data="{ editing: false }">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h3 style="font-size: 13px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 2px;">⚙️ Edit Profil</h3>
                <button @click="editing = !editing" style="background: none; border: 1px solid var(--border-color); padding: 8px 18px; border-radius: 12px; cursor: pointer; font-size: 12px; font-weight: 800; color: var(--text-main); transition: 0.3s; font-family: inherit;"
                    x-text="editing ? 'Batal' : 'Edit'"></button>
            </div>
            <div x-show="editing" x-transition>
                <form action="/profile" method="POST" class="edit-form">
                    @csrf
                    <label style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 6px;">Nama</label>
                    <input type="text" name="name" class="arctic-input" value="{{ $user->name }}" required>

                    <label style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 6px;">Bio</label>
                    <input type="text" name="bio" class="arctic-input" value="{{ $user->bio }}" placeholder="Tulis sesuatu tentang dirimu...">

                    <label style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 6px;">Warna Avatar</label>
                    <div class="color-picker-group" x-data="{ color: '{{ $user->avatar_color ?? '#1E88E5' }}' }">
                        @foreach(['#1E88E5', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#06B6D4', '#F97316'] as $c)
                        <div class="color-dot" style="background: {{ $c }};" 
                            :class="{ 'active': color === '{{ $c }}' }"
                            @click="color = '{{ $c }}'"></div>
                        @endforeach
                        <input type="hidden" name="avatar_color" x-model="color">
                    </div>

                    <button type="submit" class="btn-arctic" style="margin-top: 10px;">Simpan Perubahan</button>
                </form>
            </div>
            <div x-show="!editing">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                    <div>
                        <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Nama</div>
                        <div style="font-size: 14px; font-weight: 700; color: var(--text-main);">{{ $user->name }}</div>
                    </div>
                    <div>
                        <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Email</div>
                        <div style="font-size: 14px; font-weight: 700; color: var(--text-main);">{{ $user->email }}</div>
                    </div>
                    <div>
                        <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Role</div>
                        <div style="font-size: 14px; font-weight: 700; color: var(--text-main);">{{ ucfirst($user->role ?? 'user') }}</div>
                    </div>
                    <div>
                        <div style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Member Since</div>
                        <div style="font-size: 14px; font-weight: 700; color: var(--text-main);">{{ $user->created_at->format('d M Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logout -->
        <div style="margin-top: 25px; text-align: center;">
            <form action="/logout" method="POST">
                @csrf
                <button type="submit" style="background: none; border: 2px solid #EF4444; color: #EF4444; padding: 14px 40px; border-radius: 18px; font-weight: 800; cursor: pointer; font-size: 14px; transition: 0.3s; font-family: inherit;" 
                    onmouseover="this.style.background='#EF4444'; this.style.color='white';"
                    onmouseout="this.style.background='none'; this.style.color='#EF4444';">
                    🚪 Keluar Akun
                </button>
            </form>
        </div>
    </main>
</div>
@endsection
