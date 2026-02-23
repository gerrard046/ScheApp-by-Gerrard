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
            --primary-gradient: linear-gradient(135deg, #FF8C00 0%, #FFD700 100%);
            --secondary-gradient: linear-gradient(135deg, #FF6B6B 0%, #FF8E53 100%);
            --accent-mint: #4ECDC4;
            --soft-bg: #FFF9F0;
            --card-radius: 1.5rem;
            --vibrant-shadow: 0 10px 30px rgba(255, 140, 0, 0.15);
            --card-bg: #FFFFFF;
            --text-main: #1E293B;
            --text-muted: #64748B;
            --border-color: #FFEDCC;
        }

        [x-cloak] { display: none !important; }

        .dark {
            --soft-bg: #0F172A;
            --card-bg: #1E293B;
            --text-main: #F8FAFC;
            --text-muted: #94A3B8;
            --border-color: #334155;
            --vibrant-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
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

        .btn-cheerful {
            background: var(--primary-gradient);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            padding: 10px 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
        }
        .btn-cheerful:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 20px rgba(255, 140, 0, 0.4);
        }
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
        <div style="font-weight: 800; font-size: 22px; color: #FF8C00; letter-spacing: -1.5px;">SCHEAPP <span style="color: var(--secondary-gradient)">PRO</span></div>
        
        <div style="display: flex; gap: 15px; align-items: center;" x-data="{ openNotifications: false }">
            <div style="position: relative; cursor: pointer;" title="Notifikasi" @click="openNotifications = !openNotifications">
                <span style="font-size: 22px;">🔔</span>
                <span style="position: absolute; top: -5px; right: -5px; background: #FF6B6B; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; border: 2px solid var(--card-bg);">3</span>
                
                <!-- Notifications Dropdown -->
                <div x-show="openNotifications" @click.away="openNotifications = false" x-transition x-cloak
                    style="position: absolute; top: 40px; right: 0; width: 300px; background: var(--card-bg); border: 2px solid var(--border-color); border-radius: 20px; box-shadow: var(--vibrant-shadow); padding: 15px; z-index: 10000; color: var(--text-main);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <h4 style="margin: 0; font-size: 14px; font-weight: 800; text-transform: uppercase;">Pusat Notifikasi</h4>
                        <span style="font-size: 10px; color: #FF8C00; font-weight: 800; cursor: pointer;">Tandai Dibaca</span>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 10px; max-height: 300px; overflow-y: auto;">
                        <div style="padding: 10px; border-radius: 12px; background: var(--soft-bg); font-size: 12px; border-left: 4px solid var(--primary-gradient);">
                            <strong>✅ Tugas Diverifikasi</strong>
                            <p style="margin: 5px 0 0; color: var(--text-muted);">Admin telah memverifikasi tugas 'Lari Pagi'. Kamu dapet +5 XP!</p>
                        </div>
                        <div style="padding: 10px; border-radius: 12px; background: var(--soft-bg); font-size: 12px; border-left: 4px solid var(--secondary-gradient);">
                            <strong>🤝 Tugas Grup Baru</strong>
                            <p style="margin: 5px 0 0; color: var(--text-muted);">Admin 'Reiza' mengirim tugas grup ke 'Tim Siber 1'.</p>
                        </div>
                        <div style="padding: 10px; border-radius: 12px; background: var(--soft-bg); font-size: 12px;">
                            <strong>🔥 Streak Bertahan!</strong>
                            <p style="margin: 5px 0 0; color: var(--text-muted);">Bagus! Streak produktivitas kamu mencapai 3 hari.</p>
                        </div>
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
</body>
</html>