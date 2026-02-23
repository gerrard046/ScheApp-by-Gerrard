@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<style>
    body { background: #f8fafc; font-family: 'Inter', sans-serif; }
    .main-wrapper { display: flex; min-height: 100vh; }
    
    aside {
        width: 280px;
        background: white;
        padding: 30px 20px;
        border-right: 1px solid #e2e8f0;
        position: sticky;
        top: 0;
        height: 100vh;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        border-radius: 12px;
        color: #64748b;
        text-decoration: none;
        font-weight: 500;
        margin-bottom: 5px;
        transition: all 0.2s;
    }
    .nav-item:hover { background: #FFF5E6; color: #FF8C00; }
    .nav-item.active { background: var(--primary-gradient); color: white; box-shadow: 0 4px 12px rgba(255,140,0,0.3); }

    main { flex-grow: 1; padding: 40px; }

    .calendar-card {
        background: white;
        padding: 35px;
        border-radius: var(--card-radius);
        box-shadow: var(--vibrant-shadow);
        border: 2px solid #FFEDCC;
    }

    /* FullCalendar Overrides */
    .fc-header-toolbar { margin-bottom: 2.5em !important; }
    .fc-button-primary { 
        background: var(--primary-gradient) !important; 
        border: none !important; 
        border-radius: 10px !important;
        font-weight: 800 !important;
        box-shadow: 0 4px 10px rgba(255,140,0,0.2) !important;
    }
    .fc-button-primary:hover { transform: scale(1.05); }
    .fc-daygrid-event { border-radius: 8px !important; padding: 4px 8px !important; font-size: 11px !important; border: none !important; background: var(--secondary-gradient) !important; }
</style>

<div class="main-wrapper">
    <aside>
        <h2 style="font-weight: 800; letter-spacing: -1px; margin-bottom: 30px; color: #1e293b;">ScheApp Pro</h2>
        
        <nav>
            <a href="/schedules" class="nav-item">
                <span>🏠</span> Dashboard
            </a>
            <a href="/groups" class="nav-item">
                <span>🤝</span> Team Groups
            </a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-item">
                <span>📈</span> Admin Insights
            </a>
            @endif
        </nav>

        <div style="margin-top: 50px; padding: 25px; background: #FFF5E6; border-radius: 20px; border: 2px solid #FFEDCC;">
            <p style="font-size: 12px; color: #FF8C00; font-weight: 800; text-transform: uppercase;">Tips AI 🤖</p>
            <p style="font-size: 13px; color: #734E26; margin: 10px 0; line-height: 1.5;">Geser (drag) jadwal untuk memindahkan tanggal secara otomatis!</p>
        </div>
    </aside>

    <main class="animate-cheerful">
        <div class="calendar-card">
            <h1 style="font-weight: 800; letter-spacing: -1.5px; margin-bottom: 30px; color: #FF8C00; text-align: center;">📅 Kalender Tugas Seru</h1>
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
