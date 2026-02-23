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

    .arctic-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(8px);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-arctic {
        width: 100%;
        padding: 16px;
        border-radius: 18px;
        background: var(--primary-gradient);
        color: white;
        border: none;
        font-size: 16px;
        font-weight: 800;
        cursor: pointer;
        transition: 0.3s;
        box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2);
    }
    .btn-arctic:hover { transform: translateY(-2px); box-shadow: 0 15px 30px rgba(30, 136, 229, 0.3); }
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
        <header style="margin-bottom: 50px;">
            <p style="font-size: 13px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 3px; margin-bottom: 10px;">TEAM COLLABORATION</p>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 36px; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main);">Kerjasama Tim Seru 🤝</h2>
                @if(auth()->user()->role === 'admin')
                <button onclick="document.getElementById('createGroupModal').style.display='flex'" class="btn-arctic">
                    🚀 Buat Grup Baru
                </button>
                @endif
            </div>
        </header>

        @if(session('success'))
            <div style="background: #F0FDF4; color: #10B981; padding: 18px 25px; border-radius: 18px; margin-bottom: 30px; border: 1px solid rgba(16, 185, 129, 0.1); font-weight: 700;">
                <span style="margin-right: 10px;">✅</span> {{ session('success') }}
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

                <!-- Resource Section -->
                <div style="background: var(--soft-bg); padding: 15px; border-radius: 15px; margin-bottom: 20px; border: 1px solid var(--border-color);">
                    <h4 style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <span>📚</span> Materi & Referensi Akademik
                    </h4>
                    
                    <ul style="list-style: none; padding: 0; margin: 0 0 15px 0;">
                        @forelse($group->resources as $res)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 14px;">{{ $res->file_type == 'pdf' ? '📕' : '📄' }}</span>
                                <a href="{{ route('groups.resources.download', $res->id) }}" style="text-decoration: none; color: var(--text-main); font-size: 13px; font-weight: 700;">{{ $res->title }}</a>
                            </div>
                            <span style="font-size: 9px; color: var(--text-muted); background: white; padding: 2px 6px; border-radius: 4px;">{{ strtoupper($res->file_type) }}</span>
                        </li>
                        @empty
                        <li style="font-size: 12px; color: var(--text-muted); font-style: italic;">Belum ada materi instruksional.</li>
                        @endforelse
                    </ul>

                    <form action="{{ route('groups.resources.store', $group->id) }}" method="POST" enctype="multipart/form-data" style="border-top: 1px dashed var(--border-color); padding-top: 15px;">
                        @csrf
                        <input type="text" name="title" placeholder="Judul Materi..." class="arctic-input" style="padding: 10px; margin-bottom: 8px; font-size: 12px; height: auto;" required>
                        <div style="display: flex; gap: 10px;">
                            <input type="file" name="file" class="arctic-input" style="padding: 10px; margin-bottom: 0; font-size: 11px; height: auto;" required>
                            <button type="submit" class="btn-arctic" style="width: auto; padding: 0 20px; font-size: 12px;">Upload</button>
                        </div>
                    </form>
                </div>

                <form action="/groups/{{ $group->id }}/add-member" method="POST">
                    @csrf
                    <div style="display: flex; gap: 5px;">
                        <select name="user_id" class="input-style" style="margin: 0; padding: 8px; font-size: 12px;">
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
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <h2 style="margin: 0; font-size: 20px;">{{ $group->name }}</h2>
                    <span class="badge-member">Anggota</span>
                </div>
                <p style="color: #64748b; font-size: 13px; margin-bottom: 20px;">Admin: <b>{{ $group->admin->name }}</b></p>
                
                <!-- Resource Section for Members -->
                <div style="background: var(--soft-bg); padding: 15px; border-radius: 15px; border: 1px solid var(--border-color);">
                    <h4 style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                        <span>📚</span> Materi & Referensi Akademik
                    </h4>
                    
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @forelse($group->resources as $res)
                        <li style="display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid rgba(0,0,0,0.05);">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 14px;">{{ $res->file_type == 'pdf' ? '📕' : '📄' }}</span>
                                <a href="{{ route('groups.resources.download', $res->id) }}" style="text-decoration: none; color: var(--text-main); font-size: 13px; font-weight: 700;">{{ $res->title }}</a>
                            </div>
                            <span style="font-size: 9px; color: var(--text-muted);"> {{ strtoupper($res->file_type) }}</span>
                        </li>
                        @empty
                        <li style="font-size: 12px; color: var(--text-muted); font-style: italic;">Belum ada materi dari admin.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            @empty
            <p style="color: #94a3b8; font-style: italic;">Anda belum tergabung dalam grup manapun.</p>
            @endforelse
        </div>
    </main>
</div>

<!-- Creative Modal for Create Group -->
<div id="createGroupModal" class="arctic-overlay" style="display: none;">
    <div class="group-card" style="width: 400px; padding: 40px; border: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <h2 style="margin: 0; font-size: 24px; font-weight: 900; letter-spacing: -1px; color: var(--text-main);">Grup Baru</h2>
            <button onclick="document.getElementById('createGroupModal').style.display='none'" style="background: none; border: none; font-size: 30px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        <form action="/groups" method="POST">
            @csrf
            <input type="text" name="name" class="arctic-input" placeholder="Nama Grup (ex: Tim Proyek A)" required>
            <button type="submit" class="btn-arctic">Buat Grup Pro</button>
        </form>
    </div>
</div>
@endsection
