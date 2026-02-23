@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<style>
    /* Arctic Breeze Calendar Theme */
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

    .nav-link {
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
    .nav-link:hover { background: var(--soft-bg); transform: translateX(5px); }
    .nav-link.active { background: var(--primary-gradient); color: white; box-shadow: 0 10px 20px rgba(30, 136, 229, 0.2); }

    main { flex-grow: 1; padding: 50px; }

    .calendar-card {
        background: var(--card-bg);
        padding: 40px;
        border-radius: 24px;
        box-shadow: var(--vibrant-shadow);
        border: 1px solid var(--border-color);
    }

    /* FullCalendar Overrides */
    .fc-header-toolbar { margin-bottom: 2.5em !important; }
    .fc-button-primary { 
        background: var(--primary-gradient) !important; 
        border: none !important; 
        border-radius: 12px !important;
        font-weight: 800 !important;
        padding: 10px 20px !important;
        box-shadow: 0 8px 15px rgba(30, 136, 229, 0.15) !important;
    }
    .fc-button-primary:hover { transform: scale(1.05) !important; }
    .fc-daygrid-event { border-radius: 8px !important; padding: 4px 10px !important; font-size: 11px !important; border: none !important; background: var(--secondary-gradient) !important; color: white !important; font-weight: 700 !important; }
    .fc-toolbar-title { font-weight: 900 !important; letter-spacing: -1.5px !important; color: var(--text-main) !important; }
    .fc-col-header-cell-cushion { color: var(--text-muted) !important; font-weight: 800 !important; text-transform: uppercase; font-size: 12px !important; }
</style>

<div class="main-wrapper">
    <aside>
        <div style="margin-bottom: 50px;">
            <h1 style="font-size: 28px; font-weight: 900; letter-spacing: -2px; color: var(--text-main); display: flex; align-items: center; gap: 10px;">
                <span style="color: #1E88E5;">⚡</span> ScheApp
            </h1>
        </div>
        
        <nav>
            <a href="/schedules" class="nav-link">
                <span>🏠</span> Dashboard
            </a>
            <a href="/calendar" class="nav-link active">
                <span>📅</span> Kalender
            </a>
            <a href="/groups" class="nav-link">
                <span>🤝</span> Tim Grup
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-link">
                <span>📈</span> Admin Insights
            </a>
            @endif
        </nav>

        <div style="margin-top: auto; padding: 25px; background: var(--soft-bg); border-radius: 24px; text-align: center; border: 1px solid var(--border-color);">
            <div style="font-size: 32px; margin-bottom: 10px;">🤖</div>
            <h4 style="font-size: 13px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px;">Tips AI</h4>
            <p style="font-size: 11px; color: var(--text-muted); margin-top: 5px; line-height: 1.5;">Tarik (drag) tugas untuk mengganti tanggal dengan mudah!</p>
        </div>
    </aside>

    <main class="animate-cheerful">
        <div class="calendar-card">
            <h1 style="font-weight: 900; letter-spacing: -2px; margin-bottom: 35px; color: var(--text-main); text-align: center;">📅 Kalender Perjuangan</h1>
            <div id="calendar"></div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: '/calendar',
            editable: true,
            eventDrop: function(info) {
                // Feature for future update: AJAX update date
                alert('Jadwal "' + info.event.title + '" dipindahkan ke ' + info.event.start.toISOString().split('T')[0]);
            }
        });
        calendar.render();
    });
</script>
@endsection
