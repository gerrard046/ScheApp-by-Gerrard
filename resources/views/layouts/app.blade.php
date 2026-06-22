<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1E88E5">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="description" content="ScheApp Pro - Aplikasi manajemen jadwal modern dengan AI-powered scheduling, Zen Mode, dan gamifikasi.">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ScheApp Pro - Smart Schedule Manager</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1E88E5;
            --primary-dark: #1565C0;
            --primary-gradient: linear-gradient(135deg, #1E88E5, #1565C0);
            --secondary-gradient: linear-gradient(135deg, #42A5F5, #1E88E5);
            --accent-gradient: linear-gradient(135deg, #6366f1, #8b5cf6);
            --soft-bg: #F0F4F8;
            --card-bg: rgba(255, 255, 255, 0.95);
            --border-color: rgba(30, 136, 229, 0.08);
            --text-main: #1a2332;
            --text-muted: #64748b;
            --success: #10B981;
            --danger: #EF4444;
            --warning: #F59E0B;
            --vibrant-shadow: 0 8px 32px rgba(30, 136, 229, 0.08);
            --font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --radius: 20px;
        }

        [x-cloak] { display: none !important; }

        .dark {
            --soft-bg: #0c1222;
            --card-bg: rgba(22, 33, 55, 0.95);
            --border-color: rgba(148, 163, 184, 0.08);
            --text-main: #e8ecf1;
            --text-muted: #8899aa;
            --vibrant-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            --glass-bg: rgba(22, 33, 55, 0.85);
        }

        html, body { 
            margin: 0 !important; 
            padding: 0 !important; 
            font-family: var(--font-family) !important;
            background: var(--soft-bg) !important;
            color: var(--text-main);
            transition: background 0.4s ease, color 0.4s ease;
            -webkit-font-smoothing: antialiased;
        }
        * { box-sizing: border-box; }
        
        /* Navbar */
        .navbar-custom {
            background: var(--glass-bg) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            padding: 0 24px;
            height: 64px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 9999;
            transition: all 0.3s ease;
        }

        .dark .navbar-custom { background: rgba(12, 18, 34, 0.85) !important; }

        /* Logo */
        .nav-brand {
            font-weight: 900;
            font-size: 22px;
            color: var(--text-main);
            letter-spacing: -1.5px;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .nav-brand .logo-icon {
            width: 32px;
            height: 32px;
            background: var(--primary-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            font-weight: 900;
        }
        .nav-brand .pro-tag {
            background: var(--primary-gradient);
            color: white;
            font-size: 9px;
            font-weight: 900;
            padding: 3px 7px;
            border-radius: 6px;
            letter-spacing: 1.5px;
            margin-left: 2px;
        }

        .nav-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        /* Notification Bell */
        .nav-icon-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--soft-bg);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.25s ease;
        }
        .nav-icon-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .notif-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            border: 2px solid var(--card-bg);
            animation: pulse-badge 2s infinite;
        }
        @keyframes pulse-badge {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Theme Toggle */
        .btn-theme-toggle {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--soft-bg);
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        .btn-theme-toggle:hover { 
            transform: rotate(20deg) scale(1.05);
        }

        /* User Avatar Mini */
        .user-avatar-mini {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 900;
            font-size: 14px;
            text-decoration: none;
            transition: 0.3s;
        }
        .user-avatar-mini:hover { transform: scale(1.1); }

        /* Notification Dropdown */
        .notif-dropdown {
            position: absolute;
            top: 50px;
            right: 0;
            width: 340px;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
            padding: 20px;
            z-index: 10000;
            color: var(--text-main);
        }

        /* Animations */
        .animate-cheerful {
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        /* Design System Components */
        .btn-arctic {
            background: var(--primary-gradient);
            border: none;
            border-radius: 14px;
            color: white;
            font-weight: 800;
            padding: 12px 24px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(30, 136, 229, 0.25);
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
        }
        .btn-arctic:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(30, 136, 229, 0.35);
        }
        .btn-arctic:active { transform: translateY(0); }

        .arctic-input {
            width: 100%;
            padding: 14px 18px;
            border-radius: 14px;
            border: 1.5px solid var(--border-color);
            background: var(--soft-bg);
            font-size: 14px;
            font-weight: 600;
            color: var(--text-main);
            transition: all 0.25s ease;
            box-sizing: border-box;
            outline: none;
            font-family: inherit;
        }
        .arctic-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.1);
        }
        .arctic-input::placeholder { color: var(--text-muted); font-weight: 500; }

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
            padding: 20px;
        }

        /* Mobile Bottom Nav */
        .mobile-bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border-top: 1px solid var(--border-color);
            padding: 8px 0;
            padding-bottom: calc(8px + env(safe-area-inset-bottom));
            z-index: 9998;
            justify-content: space-around;
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3px;
            text-decoration: none;
            font-size: 10px;
            font-weight: 700;
            color: var(--text-muted);
            padding: 6px 12px;
            border-radius: 12px;
            transition: 0.2s;
        }
        .mobile-nav-item.active {
            color: var(--primary);
        }
        .mobile-nav-item .nav-icon { font-size: 22px; }

        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(30, 136, 229, 0.15); border-radius: 10px; }

        /* Responsive */
        @media (max-width: 768px) {
            .mobile-bottom-nav { display: flex; }
            body { padding-bottom: 70px !important; }
            .navbar-custom { padding: 0 16px; }
        }

        /* Notification permission button */
        #enable-notifications {
            display: none;
            background: var(--warning);
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            cursor: pointer;
            color: white;
        }

        /* Page transition */
        .page-enter {
            animation: pageSlideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        }
        @keyframes pageSlideIn {
            0% { opacity: 0; transform: translateY(12px); }
            100% { opacity: 1; transform: translateY(0); }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
    <nav class="navbar-custom">
        <a href="/schedules" class="nav-brand">
            <div class="logo-icon">S</div>
            SCHEAPP
            <span class="pro-tag">PRO</span>
        </a>
        
        <div class="nav-actions" x-data="{ openNotifications: false }">
            <!-- Notification Button -->
            <button id="enable-notifications">🔔 Enable</button>

            <div style="position: relative;" @click="openNotifications = !openNotifications">
                <div class="nav-icon-btn" title="Notifikasi">
                    <span>🔔</span>
                    @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                    <span class="notif-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                    @endif
                </div>
                
                <!-- Notifications Dropdown -->
                <div class="notif-dropdown" x-show="openNotifications" @click.away="openNotifications = false" x-transition x-cloak>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0; font-size: 14px; font-weight: 800;">🔔 Notifikasi</h4>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                        <form action="/notifications/read-all" method="POST">
                            @csrf
                            <button type="submit" style="background: none; border: none; font-size: 11px; color: var(--primary); font-weight: 800; cursor: pointer;">Tandai Dibaca</button>
                        </form>
                        @endif
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 8px; max-height: 320px; overflow-y: auto;">
                        @if(auth()->check())
                            @forelse(auth()->user()->unreadNotifications as $notification)
                            <div style="padding: 12px; border-radius: 12px; background: var(--soft-bg); font-size: 12px; border-left: 3px solid var(--primary);">
                                <strong>{{ $notification->data['icon'] ?? '🔔' }} {{ $notification->data['title'] ?? 'Notifikasi' }}</strong>
                                <p style="margin: 5px 0 0; color: var(--text-muted); line-height: 1.4; font-size: 11px;">{{ $notification->data['message'] ?? '' }}</p>
                                <small style="display: block; margin-top: 4px; opacity: 0.5; font-size: 10px;">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            @empty
                            <div style="padding: 30px 10px; text-align: center; color: var(--text-muted);">
                                <p style="font-size: 28px; margin-bottom: 8px;">📭</p>
                                <p style="font-size: 11px; font-weight: 700;">Belum ada notifikasi</p>
                            </div>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>
            
            <button @click="darkMode = !darkMode" class="btn-theme-toggle">
                <span x-show="!darkMode">☀️</span>
                <span x-show="darkMode">🌙</span>
            </button>

            @if(auth()->check())
            <a href="/wallet" class="nav-icon-btn" title="Dompet Digital" style="text-decoration:none; font-size:18px;">💰</a>
            <a href="/profile" class="user-avatar-mini" title="Profil">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </a>
            @endif
        </div>
    </nav>

    <div class="page-enter">
        @yield('content')
    </div>

    <!-- Mobile Bottom Navigation -->
    <div class="mobile-bottom-nav">
        <a href="/schedules" class="mobile-nav-item {{ request()->is('schedules') ? 'active' : '' }}">
            <span class="nav-icon">🏠</span>
            <span>Home</span>
        </a>
        <a href="/calendar" class="mobile-nav-item {{ request()->is('calendar') ? 'active' : '' }}">
            <span class="nav-icon">📅</span>
            <span>Kalender</span>
        </a>
        <a href="/kanban" class="mobile-nav-item {{ request()->is('kanban') ? 'active' : '' }}">
            <span class="nav-icon">📋</span>
            <span>Kanban</span>
        </a>
        <a href="/groups" class="mobile-nav-item {{ request()->is('groups') ? 'active' : '' }}">
            <span class="nav-icon">🤝</span>
            <span>Grup</span>
        </a>
        <a href="/wallet" class="mobile-nav-item {{ request()->is('wallet') ? 'active' : '' }}">
            <span class="nav-icon">💰</span>
            <span>Dompet</span>
        </a>
        <a href="/profile" class="mobile-nav-item {{ request()->is('profile') ? 'active' : '' }}">
            <span class="nav-icon">👤</span>
            <span>Profil</span>
        </a>
    </div>

    <script>
        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(reg => console.log('SW Registered!', reg))
                    .catch(err => console.log('SW registration failed!', err));
            });
        }

        // Web Notification Logic
        const notifyBtn = document.getElementById('enable-notifications');
        if (notifyBtn && 'Notification' in window) {
            if (Notification.permission === 'default') {
                notifyBtn.style.display = 'block';
                notifyBtn.addEventListener('click', () => {
                    Notification.requestPermission().then(permission => {
                        if (permission === 'granted') {
                            notifyBtn.style.display = 'none';
                            new Notification("ScheApp Pro", {
                                body: "Notifikasi berhasil diaktifkan!",
                                icon: "/icons/icon-192x192.png"
                            });
                        }
                    });
                });
            }
        }
    </script>
</body>
</html>