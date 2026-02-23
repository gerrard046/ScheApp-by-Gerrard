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
    .nav-item:hover { background: #f1f5f9; color: #1e293b; }
    .nav-item.active { background: #6366f1; color: white; }

    main { flex-grow: 1; padding: 40px; }

    .group-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px; }
    .group-card {
        background: white;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
        transition: transform 0.2s;
    }
    .group-card:hover { transform: translateY(-5px); }

    .input-style {
        width: 100%;
        padding: 12px 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        margin-bottom: 15px;
        font-size: 14px;
        outline: none;
    }
    .btn-submit {
        width: 100%;
        padding: 12px;
        border-radius: 12px;
        border: none;
        background: #6366f1;
        color: white;
        font-weight: bold;
        cursor: pointer;
    }

    .badge-admin { background: #6366f1; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
    .badge-member { background: #f1f5f9; color: #64748b; padding: 2px 8px; border-radius: 10px; font-size: 10px; }
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
            <a href="/groups" class="nav-item active">
                <span>🤝</span> Team Groups
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-item">
                <span>📈</span> Admin Insights
            </a>
            @endif
        </nav>
    </aside>

    <main>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="font-weight: 800; letter-spacing: -1px; margin: 0;">Kolaborasi Tim</h1>
            @if(auth()->user()->role === 'admin')
            <button onclick="document.getElementById('createGroupModal').style.display='flex'" style="background: #6366f1; color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: bold; cursor: pointer;">
                + Buat Grup Baru
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
