@extends('layouts.app')

@section('content')
<style>
    /* Arctic Breeze Groups Theme */
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

    .nav-item {
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
    .nav-item:hover { background: var(--soft-bg); transform: translateX(5px); }
    .nav-item.active { background: var(--primary-gradient); color: white; box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2); }

    main { flex-grow: 1; padding: 50px; }

    .group-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px; }
    .group-card {
        background: var(--card-bg);
        padding: 30px;
        border-radius: 24px;
        box-shadow: var(--vibrant-shadow);
        border: 1px solid var(--border-color);
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .group-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(30, 136, 229, 0.1); }

    .arctic-input {
        width: 100%;
        padding: 14px 18px;
        border-radius: 15px;
        border: 1px solid var(--border-color);
        background: var(--soft-bg);
        margin-bottom: 15px;
        font-size: 14px;
        outline: none;
        color: var(--text-main);
        font-weight: 600;
    }

    .badge-admin { background: var(--primary-gradient); color: white; padding: 5px 12px; border-radius: 50px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
    .badge-member { background: var(--soft-bg); color: var(--text-muted); padding: 5px 12px; border-radius: 50px; font-size: 10px; font-weight: 700; border: 1px solid var(--border-color); }
</style>

<div class="main-wrapper">
    <aside>
        <div style="margin-bottom: 50px;">
            <h1 style="font-size: 28px; font-weight: 900; letter-spacing: -2px; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="color: #1E88E5;">⚡</span> ScheApp
            </h1>
        </div>
        
        <nav>
            <a href="/schedules" class="nav-item">
                <span>🏠</span> Dashboard
            </a>
            <a href="/calendar" class="nav-item">
                <span>📅</span> Kalender
            </a>
            <a href="/groups" class="nav-item active">
                <span>🤝</span> Tim Grup
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-item">
                <span>📈</span> Admin Insights
            </a>
            @endif
        </nav>

        <div style="margin-top: auto; padding: 25px; background: var(--soft-bg); border-radius: 24px; text-align: center; border: 1px solid var(--border-color);">
            <div style="font-size: 32px; margin-bottom: 10px;">🌟</div>
            <h4 style="font-size: 13px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px;">Kolaborasi</h4>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px; line-height: 1.5;">Bersama kita lebih kuat dan produktif.</p>
        </div>
    </aside>

    <main>
        <div class="animate-cheerful" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; background: white; padding: 25px 35px; border-radius: var(--card-radius); box-shadow: var(--vibrant-shadow); border: 2px solid #FFEDCC;">
            <h1 style="font-weight: 800; letter-spacing: -1.5px; margin: 0; color: #FF8C00;">🤝 Kerjasama Tim Seru</h1>
            @if(auth()->user()->role === 'admin')
            <button onclick="document.getElementById('createGroupModal').style.display='flex'" class="btn-cheerful" style="padding: 12px 25px; font-size: 15px;">
                🚀 Buat Grup Baru
            </button>
            @endif
        </div>

        @if(session('success'))
            <div style="background: #dcfce7; color: #15803d; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                ✅ {{ session('success') }}
            </div>
        @endif

        <h3 style="color: #64748b; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">Grup yang Saya Kelola</h3>
        <div class="group-grid" style="margin-bottom: 40px;">
            @forelse($administeredGroups as $group)
            <div class="group-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                    <h2 style="margin: 0; font-size: 20px;">{{ $group->name }}</h2>
                    <span class="badge-admin">Admin</span>
                </div>
                
                <p style="color: #64748b; font-size: 13px;">Anggota ({{ $group->members->count() }}):</p>
                <div style="display: flex; flex-wrap: wrap; gap: 5px; margin-bottom: 20px;">
                    @foreach($group->members as $member)
                    <span class="badge-member" title="{{ $member->email }}">{{ $member->name }}</span>
                    @endforeach
                </div>

                <form action="/groups/{{ $group->id }}/add-member" method="POST">
                    @csrf
                    <div style="display: flex; gap: 5px;">
                        <select name="user_id" class="input-style" style="margin: 0; padding: 8px;">
                            <option value="">Tambah Anggota...</option>
                            @foreach($allUsers as $u)
                                @if(!$group->members->contains($u->id))
                                <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" style="background: #10b981; color: white; border: none; border-radius: 8px; padding: 0 15px; cursor: pointer;">+</button>
                    </div>
                </form>
            </div>
            @empty
            <p style="color: #94a3b8; font-style: italic;">Anda belum membuat grup apapun.</p>
            @endforelse
        </div>

        <h3 style="color: #64748b; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 20px;">Grup yang Saya Ikuti</h3>
        <div class="group-grid">
            @forelse($joinedGroups as $group)
            <div class="group-card">
                <h2 style="margin: 0 0 10px; font-size: 20px;">{{ $group->name }}</h2>
                <p style="color: #64748b; font-size: 13px;">Admin: <b>{{ $group->admin->name }}</b></p>
                <p style="color: #94a3b8; font-size: 12px;">Data jadwal dari grup ini akan muncul otomatis di dashboard Anda.</p>
            </div>
            @empty
            <p style="color: #94a3b8; font-style: italic;">Anda belum tergabung dalam grup manapun.</p>
            @endforelse
        </div>
    </main>
</div>

<!-- Simple Modal for Create Group -->
<div id="createGroupModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 20px; width: 400px; box-shadow: 0 20px 50px rgba(0,0,0,0.2);">
        <h2 style="margin-top: 0;">Buat Grup Baru</h2>
        <form action="/groups" method="POST">
            @csrf
            <input type="text" name="name" class="input-style" placeholder="Nama Grup (ex: Tim Proyek A)" required>
            <div style="display: flex; gap: 10px;">
                <button type="button" onclick="this.parentElement.parentElement.parentElement.parentElement.style.display='none'" style="flex: 1; padding: 12px; border-radius: 12px; border: 1px solid #e2e8f0; background: white; cursor: pointer;">Batal</button>
                <button type="submit" class="btn-submit" style="flex: 1;">Buat Grup</button>
            </div>
        </form>
    </div>
</div>
@endsection
