@extends('layouts.app')

@section('content')
<style>
    body { background: #f8fafc; font-family: 'Inter', sans-serif; }
    .main-wrapper { display: flex; min-height: 100vh; }
    
    aside {
        width: 280px;
        background: white;
        padding: 30px 20px;
        border-right: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        height: 100vh;
    }

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
    .nav-item:hover { background: #FFF5E6; color: #FF8C00; }
    .nav-item.active { background: var(--primary-gradient); color: white; box-shadow: 0 4px 12px rgba(255,140,0,0.3); }

    main { flex-grow: 1; padding: 40px; }

    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-card {
        background: white;
        padding: 30px;
        border-radius: var(--card-radius);
        box-shadow: var(--vibrant-shadow);
        border: 2px solid #FFEDCC;
        text-align: center;
        transition: transform 0.3s;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-card h1 { margin: 10px 0 0; font-size: 32px; color: #1e293b; }
    .stat-card p { margin: 0; color: #64748b; font-size: 13px; font-weight: bold; text-transform: uppercase; }

    .insight-table {
        width: 100%;
        background: white;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        border-collapse: collapse;
    }
    .insight-table th { text-align: left; padding: 15px; color: #64748b; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
    .insight-table td { padding: 15px; color: #1e293b; border-bottom: 1px solid #f8fafc; }

    .rank-badge { background: var(--primary-gradient); color: white; width: 28px; height: 28px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 800; margin-right: 10px; box-shadow: 0 4px 8px rgba(255,140,0,0.3); }
    .risk-alert { 
        background: linear-gradient(135deg, #FF8C00, #FF6B6B); 
        color: white; 
        padding: 20px 25px; 
        border-radius: var(--card-radius); 
        margin-bottom: 25px; 
        display: flex; 
        align-items: center; 
        gap: 15px; 
        font-weight: 700;
        box-shadow: 0 10px 25px rgba(255, 107, 107, 0.3);
    }
</style>

<div class="main-wrapper">
    <aside>
        <h2 style="font-weight: 800; letter-spacing: -1px; margin-bottom: 30px; color: #1e293b;">ScheApp Pro</h2>
        
        <nav>
            <a href="/schedules" class="nav-item">
                <span>🏠</span> Dashboard
            </a>
            <a href="/calendar" class="nav-item">
                <span>📅</span> Calendar View
            </a>
            <a href="/groups" class="nav-item">
                <span>🤝</span> Team Groups
            </a>
            <a href="/admin/insights" class="nav-item active">
                <span>📈</span> Admin Insights
            </a>
        </nav>
    </aside>

    <main>
        <div class="animate-cheerful" style="background: white; padding: 25px 35px; border-radius: var(--card-radius); box-shadow: var(--vibrant-shadow); border: 2px solid #FFEDCC; margin-bottom: 35px; display: flex; justify-content: space-between; align-items: center;">
            <h1 style="font-weight: 800; letter-spacing: -1.5px; margin: 0; color: #FF8C00;">📊 Pantauan Admin Pro</h1>
            <div style="font-size: 12px; color: #64748b; font-weight: 800; text-transform: uppercase; background: #FFF5E6; padding: 5px 15px; border-radius: 20px; border: 1px solid #FFEDCC;">Real-time Insights ⚡</div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <p>Total User</p>
                <h1>{{ $totalUsers }}</h1>
            </div>
            <div class="stat-card">
                <p>Global Success</p>
                <h1>{{ $globalCompletionRate }}%</h1>
            </div>
            <div class="stat-card">
                <p>Tugas Hari Ini</p>
                <h1>{{ $todaySchedules }}</h1>
            </div>
            <div class="stat-card">
                <p>Selesai Hari Ini</p>
                <h1 style="color: #10b981;">{{ $todayDone }}</h1>
            </div>
        </div>

        @if($riskUsers->count() > 0)
        <div class="risk-alert">
            <span>🚨</span>
            <div>
                <b>Burnout Risk Detection:</b> Terdapat {{ $riskUsers->count() }} user yang memiliki tingkat stres tinggi (memiliki >3 tugas terlewat minggu ini).
            </div>
        </div>
        @endif

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <div>
                <h3 style="margin-bottom: 20px;">Top Performance (by XP)</h3>
                <table class="insight-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Nama User</th>
                            <th>Level</th>
                            <th>Total XP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topUsers as $index => $u)
                        <tr>
                            <td><span class="rank-badge">{{ $index + 1 }}</span></td>
                            <td><b>{{ $u->name }}</b><br><small style="color: #94a3b8;">{{ $u->email }}</small></td>
                            <td><span style="background: #f1f5f9; padding: 3px 8px; border-radius: 10px; font-size: 11px; font-weight: bold;">LVL {{ $u->level }}</span></td>
                            <td><b>{{ $u->xp }} XP</b></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <h3 style="margin-bottom: 20px;">User di Bawah Pengawasan</h3>
                <div style="background: white; border-radius: 20px; padding: 25px; border: 1px solid #f1f5f9; box-shadow: 0 10px 25px rgba(0,0,0,0.05);">
                    @forelse($riskUsers as $u)
                    <div style="padding: 10px 0; border-bottom: 1px solid #f8fafc; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600;">{{ $u->name }}</span>
                        <span style="color: #ef4444; font-size: 12px; font-weight: bold;">{{ $u->schedules_count }} MISS</span>
                    </div>
                    @empty
                    <p style="color: #94a3b8; font-size: 13px; font-style: italic; text-align: center;">Tidak ada user yang terdeteksi kelelahan/stres.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
