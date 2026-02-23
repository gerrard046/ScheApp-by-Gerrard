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
        }

        html, body { 
            margin: 0 !important; 
            padding: 0 !important; 
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background: var(--soft-bg) !important;
            color: #4A4A4A;
        }
        * { box-sizing: border-box; }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.9) !important;
            backdrop-filter: blur(15px);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #FFD700;
            position: sticky;
            top: 0;
            z-index: 9999;
            box-shadow: var(--vibrant-shadow);
        }

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
    </style>
</head>
<body>
    <nav class="navbar-custom">
        <div style="font-weight: 800; font-size: 22px; color: #6366f1;">SCHEAPP</div>
        <div style="font-size: 12px; color: #64748b; font-weight: 600;">DASHBOARD V2</div>
    </nav>
    @yield('content')
</body>
</html>