<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#6366f1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>ScheApp - Modern</title>
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #1E88E5, #1565C0); /* Arctic Blue */
            --secondary-gradient: linear-gradient(135deg, #42A5F5, #1E88E5); /* Light Blue */
            --soft-bg: #F0F7FF; /* Ice Tint */
            --card-bg: rgba(255, 255, 255, 0.95);
            --border-color: rgba(30, 136, 229, 0.1);
            --text-main: #2D3E50;
            --text-muted: #64748b;
            --success: #00BFA5;
            --vibrant-shadow: 0 10px 40px rgba(30, 136, 229, 0.08);
            --font-family: 'Inter', system-ui, -apple-system, sans-serif;
            --glass-bg: rgba(255, 255, 255, 0.8);
        }

        [x-cloak] { display: none !important; }

        .dark {
            --soft-bg: #0F172A; /* Deep Slate Blue */
            --card-bg: #1E293B;
            --border-color: rgba(148, 163, 184, 0.1);
            --text-main: #F1F5F9;
            --text-muted: #94A3B8;
            --vibrant-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            --glass-bg: rgba(30, 41, 59, 0.8);
        }

        html, body { 
            margin: 0 !important; 
            padding: 0 !important; 
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background: var(--soft-bg) !important;
            color: var(--text-main);
            transition: background 0.3s ease, color 0.3s ease;
        }
        * { box-sizing: border-box; }
        
        .navbar-custom {
            background: var(--card-bg) !important;
            backdrop-filter: blur(15px);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: var(--vibrant-shadow);
            transition: all 0.3s ease;
        }
        
        .dark .navbar-custom { background: rgba(30, 41, 59, 0.8) !important; }

        .animate-cheerful {
            animation: bounceIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.9); }
            70% { transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }

        /* Arctic Breeze Consolidated Design System */
        .btn-arctic {
            background: var(--primary-gradient);
            border: none;
            border-radius: 18px;
            color: white;
            font-weight: 800;
            padding: 12px 25px;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2);
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .btn-arctic:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 15px 30px rgba(30, 136, 229, 0.3);
        }

        .arctic-input {
            width: 100%;
            padding: 15px 20px;
            border-radius: 18px;
            border: 1px solid var(--border-color);
            background: var(--soft-bg);
            font-size: 15px;
            font-weight: 600;
            color: var(--text-main);
            transition: 0.3s;
            box-sizing: border-box;
            outline: none;
        }
        .arctic-input:focus {
            border-color: #1E88E5;
            box-shadow: 0 0 0 4px rgba(30, 136, 229, 0.1);
        }

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

        /* Legacy compatibility alias */
        .btn-cheerful { @extend .btn-arctic; }

        .btn-theme-toggle {
            background: var(--soft-bg);
            border: 2px solid var(--border-color);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            transition: 0.3s;
        }
        .btn-theme-toggle:hover { transform: rotate(15deg) scale(1.1); }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" :class="{ 'dark': darkMode }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))">
    <nav class="navbar-custom">
        <div style="font-weight: 900; font-size: 24px; color: var(--text-main); letter-spacing: -2px; display: flex; align-items: center; gap: 8px;">
            <span style="color: #1E88E5;">⚡</span> SCHEAPP <span style="color: #1E88E5; font-weight: 500; font-size: 14px; letter-spacing: 2px; margin-left: 5px;">PRO</span>
        </div>
        
        <div style="display: flex; gap: 15px; align-items: center;" x-data="{ openNotifications: false }">
            <!-- Notification Permission Button -->
            <button id="enable-notifications" style="display: none; background: #FFD700; border: none; padding: 5px 10px; border-radius: 10px; font-size: 10px; font-weight: 900; cursor: pointer; color: #2D3E50;">
                🔔 Enable Alerts
            </button>

            <div style="position: relative; cursor: pointer;" title="Notifikasi" @click="openNotifications = !openNotifications">
                <span style="font-size: 22px;">🔔</span>
                @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                <span style="position: absolute; top: -5px; right: -5px; background: #FF6B6B; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; border: 2px solid var(--card-bg);">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
                @endif
                
                <!-- Notifications Dropdown -->
                <div x-show="openNotifications" @click.away="openNotifications = false" x-transition x-cloak
                    style="position: absolute; top: 40px; right: 0; width: 320px; background: var(--card-bg); border: 2px solid var(--border-color); border-radius: 20px; box-shadow: var(--vibrant-shadow); padding: 15px; z-index: 10000; color: var(--text-main);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0; font-size: 14px; font-weight: 800; text-transform: uppercase;">Pusat Notifikasi</h4>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                        <form action="/notifications/read-all" method="POST">
                            @csrf
                            <button type="submit" style="background: none; border: none; font-size: 10px; color: #FF8C00; font-weight: 800; cursor: pointer; padding: 0;">Tandai Dibaca</button>
                        </form>
                        @endif
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 10px; max-height: 350px; overflow-y: auto;">
                        @if(auth()->check())
                            @forelse(auth()->user()->unreadNotifications as $notification)
                            <div style="padding: 12px; border-radius: 12px; background: var(--soft-bg); font-size: 12px; border-left: 4px solid var(--primary-gradient);">
                                <strong>{{ $notification->data['icon'] ?? '🔔' }} {{ $notification->data['title'] ?? 'Notifikasi' }}</strong>
                                <p style="margin: 5px 0 0; color: var(--text-muted); line-height: 1.4;">{{ $notification->data['message'] ?? '' }}</p>
                                <small style="display: block; margin-top: 5px; opacity: 0.6; font-size: 10px;">{{ $notification->created_at->diffForHumans() }}</small>
                            </div>
                            @empty
                            <div style="padding: 30px 10px; text-align: center; color: var(--text-muted);">
                                <p style="font-size: 24px; margin-bottom: 10px;">📭</p>
                                <p style="font-size: 11px; font-weight: 800; text-transform: uppercase;">Belum ada kabar baru</p>
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

            <div style="font-size: 11px; color: var(--text-muted); font-weight: 800; text-transform: uppercase; background: var(--soft-bg); padding: 5px 12px; border-radius: 20px; border: 1px solid var(--border-color);">Titan Expansion 🚀</div>
        </div>
    </nav>
    @yield('content')

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
                                body: "Notifikasi berhasil diaktifkan! Anda akan menerima update jadwal di sini.",
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