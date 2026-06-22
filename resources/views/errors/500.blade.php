<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | ScheApp Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus Jakarta+Sans:wght@400;600;700;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #F0F4F8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #1a2332;
        }
        .error-card {
            text-align: center;
            background: white;
            padding: 60px 50px;
            border-radius: 28px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.06);
            max-width: 460px;
            width: 90%;
        }
        .error-code {
            font-size: 96px;
            font-weight: 900;
            background: linear-gradient(135deg, #F59E0B, #D97706);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
            margin-bottom: 16px;
        }
        .error-title {
            font-size: 22px;
            font-weight: 800;
            margin-bottom: 12px;
            color: #1a2332;
        }
        .error-desc {
            font-size: 14px;
            color: #64748b;
            font-weight: 600;
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .btn-back {
            display: inline-block;
            padding: 14px 32px;
            background: linear-gradient(135deg, #1E88E5, #1565C0);
            color: white;
            border-radius: 14px;
            text-decoration: none;
            font-weight: 800;
            font-size: 14px;
            transition: 0.2s;
        }
        .btn-back:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(30,136,229,0.3); }
        .icon { font-size: 48px; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="icon">⚠️</div>
        <div class="error-code">500</div>
        <div class="error-title">Server Bermasalah</div>
        <div class="error-desc">
            Terjadi kesalahan di sisi server. Tim kami sedang menanganinya.<br>
            Coba lagi dalam beberapa menit.
        </div>
        <a href="/schedules" class="btn-back">Kembali ke Dashboard</a>
    </div>
</body>
</html>
