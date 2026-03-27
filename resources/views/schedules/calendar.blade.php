@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
<style>
    /* Arctic Breeze Calendar Theme */
    .main-wrapper { display: flex; min-height: 100vh; background: var(--soft-bg); }
    
    aside {
        width: 260px;
        background: var(--card-bg);
        border-right: 1px solid var(--border-color);
        padding: 30px 20px;
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 64px;
        height: calc(100vh - 64px);
        backdrop-filter: blur(10px);
    }

    .nav-link {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 16px;
        border-radius: 14px;
        color: var(--text-main);
        text-decoration: none;
        font-weight: 700;
        transition: all 0.25s ease;
        margin-bottom: 4px;
        font-size: 14px;
    }
    .nav-link:hover { background: var(--soft-bg); transform: translateX(4px); }
    .nav-link.active { background: var(--primary-gradient); color: white; box-shadow: 0 6px 18px rgba(30, 136, 229, 0.25); }

    main { flex-grow: 1; padding: 40px; }

    .calendar-card {
        background: var(--card-bg);
        padding: 40px;
        border-radius: 24px;
        box-shadow: var(--vibrant-shadow);
        border: 1px solid var(--border-color);
    }

    /* Calendar Legend */
    .calendar-legend {
        display: flex;
        gap: 20px;
        justify-content: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        color: var(--text-muted);
    }

    .legend-dot {
        width: 12px;
        height: 12px;
        border-radius: 4px;
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
    .fc-daygrid-event { 
        border-radius: 8px !important; 
        padding: 4px 10px !important; 
        font-size: 11px !important; 
        border: none !important; 
        font-weight: 700 !important; 
        cursor: pointer !important;
        transition: transform 0.2s ease !important;
    }
    .fc-daygrid-event:hover { transform: scale(1.02) !important; }
    .fc-toolbar-title { font-weight: 900 !important; letter-spacing: -1.5px !important; color: var(--text-main) !important; }
    .fc-col-header-cell-cushion { color: var(--text-muted) !important; font-weight: 800 !important; text-transform: uppercase; font-size: 12px !important; }

    /* Event Detail Popup */
    .event-popup {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(12px);
        z-index: 20000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .event-popup-card {
        background: var(--card-bg);
        border-radius: 24px;
        padding: 35px;
        max-width: 420px;
        width: 100%;
        box-shadow: 0 30px 80px rgba(0,0,0,0.15);
        border: 1px solid var(--border-color);
    }

    @media (max-width: 768px) {
        aside { display: none; }
        main { padding: 20px !important; }
        .calendar-card { padding: 20px; }
    }
</style>

<div class="main-wrapper">
    <aside>
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 22px; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                <span style="color: #1E88E5;">⚡</span> ScheApp
            </h1>
        </div>
        
        <nav>
            <a href="/schedules" class="nav-link"><span>🏠</span> Dashboard</a>
            <a href="/calendar" class="nav-link active"><span>📅</span> Kalender</a>
            <a href="/kanban" class="nav-link"><span>📋</span> Kanban Board</a>
            <a href="/groups" class="nav-link"><span>🤝</span> Tim Grup</a>
            <a href="/profile" class="nav-link"><span>👤</span> Profil</a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-link"><span>📈</span> Admin Insights</a>
            @endif
        </nav>

        <div style="margin-top: auto; padding: 20px; background: var(--soft-bg); border-radius: 18px; text-align: center; border: 1px solid var(--border-color);">
            <div style="font-size: 28px; margin-bottom: 8px;">📅</div>
            <h4 style="font-size: 12px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px;">Tips</h4>
            <p style="font-size: 10px; color: var(--text-muted); margin-top: 4px; line-height: 1.5;">Klik event di kalender untuk melihat detail atau menandai selesai.</p>
        </div>
    </aside>

    <main class="animate-cheerful">
        <div class="calendar-card">
            <h1 style="font-weight: 900; letter-spacing: -2px; margin-bottom: 15px; color: var(--text-main); text-align: center;">📅 Kalender Perjuangan</h1>
            <p style="text-align: center; font-size: 13px; color: var(--text-muted); font-weight: 600; margin-bottom: 25px;">Semua jadwal yang ditambahkan otomatis muncul di kalender ini.</p>
            
            <!-- Legend -->
            <div class="calendar-legend">
                <div class="legend-item">
                    <div class="legend-dot" style="background: #6366f1;"></div>
                    <span>Belum Selesai</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background: #EF4444;"></div>
                    <span>High Priority / Terlewat</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background: #10B981;"></div>
                    <span>Selesai</span>
                </div>
                <div class="legend-item">
                    <div class="legend-dot" style="background: #F59E0B;"></div>
                    <span>🏃 Selesai Sebelum Tenggat</span>
                </div>
            </div>
            
            <div id="calendar"></div>
        </div>
    </main>
</div>

<!-- Event Detail Popup -->
<div id="eventPopup" class="event-popup" style="display: none;" x-data>
    <div class="event-popup-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 id="popupTitle" style="font-size: 20px; font-weight: 900; color: var(--text-main); margin: 0;"></h2>
            <button onclick="closePopup()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: var(--text-muted);">&times;</button>
        </div>
        
        <div id="popupDetails" style="display: grid; gap: 12px;"></div>
        
        <div style="margin-top: 25px; display: flex; gap: 10px;">
            <form id="popupToggleForm" method="POST" style="flex: 1;">
                @csrf
                <button type="submit" id="popupToggleBtn" class="btn-arctic" style="width: 100%; padding: 14px;">
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
    function closePopup() {
        document.getElementById('eventPopup').style.display = 'none';
    }

    document.getElementById('eventPopup').addEventListener('click', function(e) {
        if (e.target === this) closePopup();
    });

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
                alert('Jadwal "' + info.event.title + '" dipindahkan ke ' + info.event.start.toISOString().split('T')[0]);
            },
            eventClick: function(info) {
                var event = info.event;
                var props = event.extendedProps;
                var popup = document.getElementById('eventPopup');
                
                // Set title
                document.getElementById('popupTitle').textContent = event.title.replace(/^[🏃✅⚠️] /, '');
                
                // Build details
                var details = '';
                details += '<div style="padding: 12px 16px; background: var(--soft-bg); border-radius: 12px;">';
                details += '<div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Kategori</div>';
                details += '<div style="font-size: 14px; font-weight: 700; color: var(--text-main);">' + (props.category || '-') + '</div>';
                details += '</div>';
                
                details += '<div style="padding: 12px 16px; background: var(--soft-bg); border-radius: 12px;">';
                details += '<div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Waktu</div>';
                details += '<div style="font-size: 14px; font-weight: 700; color: var(--text-main);">' + (event.start ? event.start.toLocaleString('id-ID') : '-') + '</div>';
                details += '</div>';
                
                if (props.group_name) {
                    details += '<div style="padding: 12px 16px; background: var(--soft-bg); border-radius: 12px;">';
                    details += '<div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Grup</div>';
                    details += '<div style="font-size: 14px; font-weight: 700; color: var(--text-main);">🤝 ' + props.group_name + '</div>';
                    details += '</div>';
                }
                
                if (props.notes) {
                    details += '<div style="padding: 12px 16px; background: var(--soft-bg); border-radius: 12px;">';
                    details += '<div style="font-size: 11px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 4px;">Catatan</div>';
                    details += '<div style="font-size: 13px; font-weight: 600; color: var(--text-main); line-height: 1.5;">📝 ' + props.notes + '</div>';
                    details += '</div>';
                }

                // Status badge
                var statusHtml = '';
                if (props.is_completed && props.is_early) {
                    statusHtml = '<div style="padding: 10px 16px; background: rgba(245, 158, 11, 0.1); border-radius: 12px; text-align: center; font-size: 13px; font-weight: 800; color: #D97706;">🏃 Selesai Sebelum Tenggat!</div>';
                } else if (props.is_completed) {
                    statusHtml = '<div style="padding: 10px 16px; background: rgba(16, 185, 129, 0.1); border-radius: 12px; text-align: center; font-size: 13px; font-weight: 800; color: #10B981;">✅ Sudah Selesai</div>';
                } else {
                    var priorityText = props.priority === 'high' ? '🔴 Prioritas Tinggi' : (props.priority === 'low' ? '🟢 Prioritas Rendah' : '🟡 Prioritas Sedang');
                    statusHtml = '<div style="padding: 10px 16px; background: var(--soft-bg); border-radius: 12px; text-align: center; font-size: 13px; font-weight: 800; color: var(--text-muted);">' + priorityText + '</div>';
                }
                details += statusHtml;
                
                document.getElementById('popupDetails').innerHTML = details;
                
                // Toggle form
                var toggleForm = document.getElementById('popupToggleForm');
                toggleForm.action = '/schedules/' + event.id + '/toggle';
                var toggleBtn = document.getElementById('popupToggleBtn');
                toggleBtn.textContent = props.is_completed ? '↩️ Batalkan Selesai' : '✅ Tandai Selesai';
                
                popup.style.display = 'flex';
            }
        });
        calendar.render();
    });
</script>
@endsection
