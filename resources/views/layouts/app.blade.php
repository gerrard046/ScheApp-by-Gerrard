<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ScheApp - Laravel</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <style>
        /* Tambahan CSS darurat agar layout tidak berantakan jika style.css belum terbaca */
        .main-layout { display: flex; gap: 20px; padding: 20px; font-family: sans-serif; }
        .sidebar { flex: 1; border: 1px solid #ddd; padding: 20px; border-radius: 10px; background: #f9f9f9; }
        .content { flex: 3; }
        .stats-container { display: flex; gap: 15px; margin-bottom: 20px; }
        .stat-card { flex: 1; padding: 15px; background: #007bff; color: white; border-radius: 8px; text-align: center; }
        .schedule-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 15px; }
        .schedule-card { border: 1px solid #eee; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .btn-delete { background: #ff4d4d; color: white; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>