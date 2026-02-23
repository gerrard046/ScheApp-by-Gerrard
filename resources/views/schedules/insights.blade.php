@extends('layouts.app')

@section('content')
<style>
    /* Arctic Breeze Insights Theme */
    .main-wrapper { display: flex; min-height: 100vh; background: var(--soft-bg); }
    
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
        margin-bottom: 5px;
        font-size: 15px;
    }
    .nav-link:hover { background: var(--soft-bg); transform: translateX(5px); }
    .nav-link.active { background: var(--primary-gradient); color: white; box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2); }

    main { flex-grow: 1; padding: 50px; }

    .zen-card {
        background: var(--card-bg);
        padding: 30px;
        border-radius: 24px;
        box-shadow: var(--vibrant-shadow);
        border: 1px solid var(--border-color);
        transition: 0.4s;
    }
    .zen-card:hover { transform: translateY(-5px); }

    .insight-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    .insight-table th { text-align: left; padding: 15px 20px; color: var(--text-muted); font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
    .insight-table td { padding: 20px; background: var(--card-bg); color: var(--text-main); border: 1px solid var(--border-color); }
    .insight-table tr td:first-child { border-radius: 15px 0 0 15px; border-right: none; }
    .insight-table tr td:last-child { border-radius: 0 15px 15px 0; border-left: none; }

    .risk-alert { 
        background: #1E293B; 
        color: white; 
        padding: 25px; 
        border-radius: 24px; 
        margin-bottom: 35px; 
        display: flex; 
        align-items: center; 
        gap: 20px; 
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
    }
</style>

<div class="main-wrapper">
    <aside>
        <div style="margin-bottom: 50px;">
            <h1 style="font-size: 28px; font-weight: 900; letter-spacing: -2px; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="color: #1E88E5;">⚡</span> ScheApp
            </h1>
        </div>
        
        <nav>
            <a href="/schedules" class="nav-link"><span>🏠</span> Dashboard</a>
            <a href="/calendar" class="nav-link"><span>📅</span> Kalender</a>
            <a href="/groups" class="nav-link"><span>🤝</span> Tim Grup</a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-link active"><span>📈</span> Insights</a>
            @endif
        </nav>

        <div style="margin-top: auto; padding: 25px; background: var(--soft-bg); border-radius: 24px; text-align: center; border: 1px solid var(--border-color);">
            <div style="font-size: 32px; margin-bottom: 10px;">📊</div>
            <h4 style="font-size: 13px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px;">Admin Panel</h4>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px; line-height: 1.5;">Pantau performa dan kesehatan tim.</p>
        </div>
    </aside>

    <main>
        <header style="margin-bottom: 50px;">
            <p style="font-size: 13px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 3px; margin-bottom: 10px;">GLOBAL ANALYTICS</p>
            <h2 style="font-size: 36px; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main);">Pantauan Admin Pro ⚡</h2>
        </header>

        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px;">
            <div class="zen-card" style="text-align: center;">
                <p style="font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 10px;">TOTAL USER</p>
                <div style="font-size: 32px; font-weight: 900; color: #1E88E5;">{{ $totalUsers }}</div>
            </div>
            <div class="zen-card" style="text-align: center;">
                <p style="font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 10px;">GLOBAL SUCCESS</p>
                <div style="font-size: 32px; font-weight: 900; color: #10B981;">{{ $globalCompletionRate }}%</div>
            </div>
            <div class="zen-card" style="text-align: center;">
                <p style="font-size: 11px; font-weight: 800; color: var(--text-muted); margin-bottom: 10px;">TUGAS HARI INI</p>
                <div style="font-size: 32px; font-weight: 900;">{{ $todaySchedules }}</div>
            </div>
            <div class="zen-card" style="text-align: center; background: var(--primary-gradient); color: white; border: none;">
                <p style="font-size: 11px; font-weight: 800; opacity: 0.8; margin-bottom: 10px;">SELESAI HARI INI</p>
                <div style="font-size: 32px; font-weight: 900;">{{ $todayDone }}</div>
            </div>
        </div>

        @if($riskUsers->count() > 0)
        <div class="risk-alert">
            <div style="font-size: 32px;">🚨</div>
            <div>
                <b style="font-size: 16px; display: block; margin-bottom: 5px;">Burnout Risk Alert</b>
                <span style="opacity: 0.8; font-size: 13px;">Terdapat {{ $riskUsers->count() }} user dengan komitmen tinggi yang terancam burnout (>3 tugas terlewat).</span>
            </div>
        </div>
        @endif

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
            <div>
                <h3 style="font-size: 20px; font-weight: 900; color: var(--text-main); margin-bottom: 25px;">Top Performance</h3>
                <table class="insight-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>User</th>
                            <th>Level</th>
                            <th>XP</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topUsers as $index => $u)
                        <tr>
                            <td style="width: 60px; text-align: center;">
                                <div style="width: 35px; height: 35px; background: {{ $index == 0 ? '#FFD700' : ($index == 1 ? '#C0C0C0' : ($index == 2 ? '#CD7F32' : 'var(--soft-bg)')) }}; color: {{ $index < 3 ? 'white' : 'var(--text-main)' }}; display: flex; align-items: center; justify-content: center; border-radius: 12px; font-weight: 900; font-size: 14px;">
                                    {{ $index + 1 }}
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 800;">{{ $u->name }}</div>
                                <div style="font-size: 11px; color: var(--text-muted);">{{ $u->email }}</div>
                            </td>
                            <td>
                                <span style="background: var(--soft-bg); padding: 5px 12px; border-radius: 10px; font-size: 11px; font-weight: 800; color: #1E88E5;">LVL {{ $u->level }}</span>
                            </td>
                            <td style="font-weight: 900; color: var(--text-main);">{{ $u->xp }} XP</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div>
                <h3 style="font-size: 20px; font-weight: 900; color: var(--text-main); margin-bottom: 25px;">Watchlist</h3>
                <div class="zen-card">
                    @forelse($riskUsers as $u)
                    <div style="padding: 15px 0; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; color: var(--text-main);">{{ $u->name }}</span>
                        <span style="background: #FFEBEB; color: #EF4444; font-size: 10px; font-weight: 900; padding: 4px 10px; border-radius: 50px;">{{ $u->schedules_count }} MISS</span>
                    </div>
                    @empty
                    <div style="text-align: center; padding: 20px 0;">
                        <div style="font-size: 40px; margin-bottom: 10px;">🌈</div>
                        <p style="font-size: 12px; color: var(--text-muted); font-weight: 700;">Semua user aman dari risiko burnout.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
