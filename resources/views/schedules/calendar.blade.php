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
    .nav-item:hover { background: #f1f5f9; color: #1e293b; }
    .nav-item.active { background: #6366f1; color: white; }

    main { flex-grow: 1; padding: 40px; }

    .calendar-card {
        background: white;
        padding: 25px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
    }

    /* FullCalendar Overrides for Glassmorphism */
    .fc-header-toolbar { margin-bottom: 2em !important; }
    .fc-button-primary { background-color: #6366f1 !important; border-color: #6366f1 !important; }
    .fc-daygrid-event { border-radius: 6px !important; padding: 2px 5px !important; font-size: 11px !important; }
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

        <div style="margin-top: 50px; padding: 20px; background: #f1f5f9; border-radius: 16px;">
            <p style="font-size: 11px; color: #64748b; font-weight: bold; text-transform: uppercase;">Tips AI 🤖</p>
            <p style="font-size: 12px; color: #475569; margin: 10px 0;">Geser (drag) jadwal untuk memindahkan tanggal secara otomatis!</p>
        </div>
    </aside>

    <main>
        <div class="calendar-card">
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
