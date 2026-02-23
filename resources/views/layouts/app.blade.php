<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScheApp - Modern</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* CSS RESET UNTUK MEMATIKAN BOOTSTRAP LAMA */
        html, body { 
            margin: 0 !important; 
            padding: 0 !important; 
            font-family: 'Plus Jakarta Sans', sans-serif !important;
            background-color: #f4f7fe !important;
        }
        * { box-sizing: border-box; }
        
        .navbar-custom {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(10px);
            padding: 15px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 9999;
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