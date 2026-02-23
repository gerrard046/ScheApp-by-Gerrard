<!DOCTYPE html>
<html>
<head>
    <title>ScheApp Pro - Admin Insights Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #2c3e50;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #3498db;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            font-style: italic;
            color: #7f8c8d;
        }
        .stats-container {
            width: 100%;
            margin-bottom: 30px;
        }
        .stat-box {
            width: 23%;
            float: left;
            padding: 15px 1%;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: center;
            margin-right: 1%;
        }
        .stat-value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        .stat-label {
            display: block;
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        .clear {
            clear: both;
        }
        .section {
            margin-bottom: 40px;
        }
        .section-title {
            font-size: 18px;
            border-left: 4px solid #3498db;
            padding-left: 10px;
            margin-bottom: 15px;
            color: #2c3e50;
            background: #ecf0f1;
            padding-top: 5px;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f2f2f2;
            color: #7f8c8d;
            font-size: 12px;
            text-transform: uppercase;
        }
        .rank-badge {
            background: #3498db;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 11px;
        }
        .risk-badge {
            background: #e74c3c;
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 11px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #bdc3c7;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>ScheApp Pro Insights</h1>
        <p>Laporan Performansi Global & Analisis Produktivitas</p>
        <p style="font-size: 12px; color: #95a5a6; font-style: normal; margin-top: 10px;">Dicetak pada: {{ $export_date }}</p>
    </div>

    <div class="section">
        <div class="section-title">Ringkasan Statistik Global</div>
        <div class="stats-container">
            <div class="stat-box">
                <span class="stat-value">{{ $totalUsers }}</span>
                <span class="stat-label">Total Pengguna</span>
            </div>
            <div class="stat-box">
                <span class="stat-value">{{ $globalCompletionRate }}%</span>
                <span class="stat-label">Tingkat Sukses</span>
            </div>
            <div class="stat-box">
                <span class="stat-value">{{ $todaySchedules }}</span>
                <span class="stat-label">Tugas Hari Ini</span>
            </div>
            <div class="stat-box" style="margin-right: 0;">
                <span class="stat-value">{{ $todayDone }}</span>
                <span class="stat-label">Selesai Hari Ini</span>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Peringkat 10 Performa Tertinggi (Top XP)</div>
        <table>
            <thead>
                <tr>
                    <th width="50">Peringkat</th>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Level</th>
                    <th>Total XP</th>
                </tr>
            </thead>
            <tbody>
                @foreach($topUsers as $index => $user)
                <tr>
                    <td><span class="rank-badge">{{ $index + 1 }}</span></td>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td>LVL {{ $user->level }}</td>
                    <td>{{ $user->xp }} XP</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($riskUsers->count() > 0)
    <div class="section">
        <div class="section-title">Detect Burnout Risk (Watchlist)</div>
        <p style="font-size: 12px; color: #7f8c8d; margin-bottom: 10px;">Daftar penguna dengan lebih dari 3 tugas terbengkalai dalam 7 hari terakhir.</p>
        <table>
            <thead>
                <tr>
                    <th>Nama Pengguna</th>
                    <th>Email</th>
                    <th>Tugas Missed (7 Hari)</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($riskUsers as $user)
                <tr>
                    <td><strong>{{ $user->name }}</strong></td>
                    <td>{{ $user->email }}</td>
                    <td style="color: #e74c3c; font-weight: bold;">{{ $user->schedules_count }} Tugas</td>
                    <td><span class="risk-badge">Resiko Burnout</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="section">
        <div class="section-title">Kesehatan Produktivitas</div>
        <p style="text-align: center; color: #27ae60; font-weight: bold; padding: 20px; background: #f0fff4; border-radius: 8px;">
            Seluruh pengguna berada dalam zona produktivitas aman. Tidak terdeteksi resiko burnout massal.
        </p>
    </div>
    @endif

    <div class="footer">
        Dokumen ini dibuat secara otomatis oleh Sistem Manajemen Penjadwalan ScheApp Pro.<br>
        &copy; {{ date('Y') }} Reiza Gerrard - Departemen Pengembangan Sistem Dinamis Poltek SSN.
    </div>
</body>
</html>
