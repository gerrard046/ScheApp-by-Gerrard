@extends('layouts.app')

@section('content')
{{-- FullCalendar v5 all-in-one (termasuk Interaction plugin untuk drag & resize) --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />

<style>
/* ═══════════════════════════════════════════════════════════════════════════
   Arctic Breeze Glassmorphism — Time-Block Calendar
═══════════════════════════════════════════════════════════════════════════ */
.main-wrapper { display:flex; min-height:100vh; background:var(--soft-bg); }

aside {
    width:260px; background:var(--card-bg); border-right:1px solid var(--border-color);
    padding:30px 20px; display:flex; flex-direction:column; position:sticky;
    top:64px; height:calc(100vh - 64px); backdrop-filter:blur(10px); overflow-y:auto;
}
.nav-link {
    display:flex; align-items:center; gap:12px; padding:11px 16px;
    border-radius:14px; color:var(--text-main); text-decoration:none;
    font-weight:700; transition:all .25s; margin-bottom:4px; font-size:14px;
}
.nav-link:hover { background:var(--soft-bg); transform:translateX(4px); }
.nav-link.active { background:var(--primary-gradient); color:#fff; box-shadow:0 6px 18px rgba(30,136,229,.25); }

main { flex:1; padding:30px; overflow-y:auto; }

/* ── Calendar Card ────────────────────────────────────────────────────── */
.cal-card {
    background:var(--card-bg); border-radius:24px;
    box-shadow:var(--vibrant-shadow); border:1px solid var(--border-color);
    padding:28px; backdrop-filter:blur(8px);
}
.cal-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.cal-header h2 { font-size:20px; font-weight:900; color:var(--text-main); margin:0; }

/* ── FullCalendar Global Overrides ───────────────────────────────────── */
.fc { font-family:var(--font-family) !important; }
.fc-header-toolbar { margin-bottom:1.6em !important; }

.fc-button-primary {
    background:var(--primary-gradient) !important; border:none !important;
    border-radius:10px !important; font-weight:800 !important;
    padding:8px 16px !important; font-size:13px !important;
    box-shadow:0 4px 12px rgba(30,136,229,.2) !important; transition:all .2s !important;
}
.fc-button-primary:hover { opacity:.9 !important; transform:translateY(-1px) !important; }
.fc-button-primary:not(.fc-button-active):not(:disabled) { opacity:1 !important; }
.fc-button-active { background:linear-gradient(135deg,#1565C0,#0D47A1) !important; }
.fc-button-group { gap:4px !important; }

.fc-view-harness { border-radius:16px; overflow:hidden; }

/* Timegrid styling */
.fc-timegrid-slot { height:3em !important; }
.fc-timegrid-slot-label { font-size:11px; font-weight:700; color:var(--text-muted); }
.fc-col-header-cell { background:rgba(30,136,229,.04); font-weight:800; font-size:13px; }
.fc-col-header-cell-cushion { padding:10px 4px; color:var(--text-main); text-decoration:none; }
.fc-today .fc-col-header-cell-cushion { color:var(--primary); }
.fc-daygrid-day.fc-day-today, .fc-timegrid-col.fc-day-today {
    background:rgba(30,136,229,.04) !important;
}
.fc-scrollgrid { border-color:var(--border-color) !important; border-radius:12px; }
.fc-scrollgrid td, .fc-scrollgrid th { border-color:var(--border-color) !important; }

/* Now indicator */
.fc-timegrid-now-indicator-line { border-color:#EF4444 !important; border-width:2px !important; }
.fc-timegrid-now-indicator-arrow { border-top-color:#EF4444 !important; border-bottom-color:#EF4444 !important; }

/* Event blocks — glassmorphism pill style */
.fc-event {
    border-radius:8px !important; border:none !important; font-size:12px !important;
    font-weight:700 !important; padding:2px 6px !important;
    box-shadow:0 2px 8px rgba(0,0,0,.12) !important;
    transition:transform .15s, box-shadow .15s !important;
    cursor:grab !important;
}
.fc-event:hover { transform:scale(1.02) !important; box-shadow:0 4px 16px rgba(0,0,0,.2) !important; }
.fc-event:active { cursor:grabbing !important; }
.fc-event.event-completed { opacity:.65 !important; }
.fc-daygrid-event { border-radius:6px !important; }
.fc-h-event .fc-event-main { padding:2px 6px !important; }

/* Selection highlight */
.fc-highlight { background:rgba(30,136,229,.15) !important; border:2px dashed rgba(30,136,229,.4) !important; border-radius:8px !important; }

/* Popover */
.fc-popover { border-radius:14px !important; box-shadow:0 8px 32px rgba(0,0,0,.15) !important; border:1px solid var(--border-color) !important; background:var(--card-bg) !important; }
.fc-popover-header { background:var(--primary-gradient) !important; color:#fff !important; border-radius:14px 14px 0 0 !important; font-weight:800 !important; }

/* Legend */
.legend-row { display:flex; gap:16px; flex-wrap:wrap; margin-bottom:18px; }
.legend-item { display:flex; align-items:center; gap:6px; font-size:11px; font-weight:700; color:var(--text-muted); }
.legend-dot { width:10px; height:10px; border-radius:4px; }

/* Google sync badge */
.google-badge {
    display:inline-flex; align-items:center; gap:6px; padding:6px 14px;
    border-radius:20px; font-size:12px; font-weight:700; cursor:pointer;
    transition:all .2s;
}
.google-badge.connected { background:rgba(16,185,129,.1); color:#059669; border:1px solid rgba(16,185,129,.3); }
.google-badge.disconnected { background:rgba(99,102,241,.1); color:#6366f1; border:1px solid rgba(99,102,241,.3); }
.google-badge:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,.1); }

/* ── Modal ─────────────────────────────────────────────────────────────── */
.modal-backdrop {
    position:fixed; inset:0; background:rgba(0,0,0,.45); backdrop-filter:blur(6px);
    display:flex; align-items:center; justify-content:center; z-index:9999; padding:20px;
}
.modal-box {
    background:var(--card-bg); border-radius:24px; padding:32px; width:100%; max-width:480px;
    box-shadow:0 24px 64px rgba(0,0,0,.18); border:1px solid var(--border-color);
    animation:slideUp .25s ease;
}
@keyframes slideUp { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }

.modal-title { font-size:18px; font-weight:900; color:var(--text-main); margin:0 0 20px; }
.form-group { margin-bottom:16px; }
.form-label { display:block; font-size:12px; font-weight:800; color:var(--text-muted); margin-bottom:6px; text-transform:uppercase; letter-spacing:.5px; }
.form-control {
    width:100%; padding:10px 14px; border-radius:12px; font-size:14px;
    border:1px solid var(--border-color); background:var(--soft-bg); color:var(--text-main);
    font-family:var(--font-family); font-weight:600; box-sizing:border-box; transition:border-color .2s;
}
.form-control:focus { outline:none; border-color:var(--primary); box-shadow:0 0 0 3px rgba(30,136,229,.12); }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.btn { display:inline-flex; align-items:center; gap:6px; padding:10px 20px; border-radius:12px; font-weight:800; font-size:13px; cursor:pointer; border:none; transition:all .2s; }
.btn-primary { background:var(--primary-gradient); color:#fff; box-shadow:0 4px 12px rgba(30,136,229,.25); }
.btn-primary:hover { transform:translateY(-1px); box-shadow:0 6px 18px rgba(30,136,229,.35); }
.btn-danger { background:linear-gradient(135deg,#EF4444,#DC2626); color:#fff; }
.btn-danger:hover { transform:translateY(-1px); }
.btn-ghost { background:transparent; color:var(--text-muted); border:1px solid var(--border-color); }
.btn-ghost:hover { background:var(--soft-bg); }
.modal-footer { display:flex; gap:10px; justify-content:flex-end; margin-top:20px; padding-top:16px; border-top:1px solid var(--border-color); }

/* Color swatches */
.color-swatches { display:flex; gap:8px; flex-wrap:wrap; margin-top:6px; }
.swatch {
    width:28px; height:28px; border-radius:8px; cursor:pointer; border:2px solid transparent;
    transition:all .15s;
}
.swatch:hover, .swatch.active { border-color:#fff; box-shadow:0 0 0 2px var(--primary); transform:scale(1.15); }

/* Toast notification */
.toast-container { position:fixed; top:80px; right:20px; z-index:10000; display:flex; flex-direction:column; gap:10px; }
.toast {
    padding:12px 18px; border-radius:14px; font-weight:700; font-size:13px;
    box-shadow:0 8px 24px rgba(0,0,0,.15); animation:slideIn .3s ease;
    min-width:260px; backdrop-filter:blur(8px);
}
.toast.success { background:rgba(16,185,129,.9); color:#fff; }
.toast.error { background:rgba(239,68,68,.9); color:#fff; }
@keyframes slideIn { from { opacity:0; transform:translateX(40px); } to { opacity:1; transform:translateX(0); } }

@media (max-width:768px) {
    aside { display:none; }
    main { padding:16px; }
    .form-row { grid-template-columns:1fr; }
}
</style>

{{-- Alpine.js state controller --}}
<div class="main-wrapper"
     x-data="calendarApp()"
     x-init="init()"
     @keydown.escape.window="closeModal()">

    {{-- ── Sidebar ── --}}
    <aside>
        <div style="font-size:11px;font-weight:900;color:var(--text-muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:20px;">NAVIGASI</div>
        <a href="/schedules" class="nav-link">
            <span style="font-size:18px;">📋</span> Daftar Tugas
        </a>
        <a href="/calendar" class="nav-link active">
            <span style="font-size:18px;">🗓️</span> Kalender
        </a>
        <a href="/kanban" class="nav-link">
            <span style="font-size:18px;">🧩</span> Kanban
        </a>
        <a href="/wallet" class="nav-link">
            <span style="font-size:18px;">💳</span> Dompet
        </a>
        <a href="/groups" class="nav-link">
            <span style="font-size:18px;">👥</span> Grup
        </a>
        <a href="/profile" class="nav-link">
            <span style="font-size:18px;">👤</span> Profil
        </a>

        <div style="margin-top:auto;padding-top:20px;border-top:1px solid var(--border-color);">
            {{-- Google Calendar Sync Status --}}
            @if($googleConnected)
            <form action="{{ route('google.sync') }}" method="POST" style="margin-bottom:8px;">
                @csrf
                <button type="submit" class="google-badge connected" style="width:100%;justify-content:center;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0C5.372 0 0 5.373 0 12s5.372 12 12 12 12-5.373 12-12S18.628 0 12 0zm5.562 8.248l-5.625 8.25a.75.75 0 01-1.248.002L7.94 12.75a.75.75 0 111.12-.998l2.19 2.463 5.067-7.426a.75.75 0 011.245.835v.624z"/></svg>
                    Google Tersinkron
                </button>
            </form>
            <form action="{{ route('google.disconnect') }}" method="POST">
                @csrf
                <button type="submit" class="google-badge disconnected" style="width:100%;justify-content:center;font-size:11px;">
                    Putus Koneksi Google
                </button>
            </form>
            @else
            <a href="{{ route('google.redirect') }}" class="google-badge disconnected" style="display:flex;justify-content:center;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 11v2h4.24A6 6 0 1 1 6.34 7.34L7.76 8.76A4 4 0 1 0 14.24 13H12z"/></svg>
                Hubungkan Google
            </a>
            @endif
        </div>
    </aside>

    {{-- ── Main Content ── --}}
    <main>
        {{-- Toast container --}}
        <div class="toast-container">
            <template x-for="t in toasts" :key="t.id">
                <div class="toast" :class="t.type" x-text="t.msg"
                     x-init="setTimeout(() => toasts.splice(toasts.indexOf(t), 1), 3000)"></div>
            </template>
        </div>

        <div class="cal-card">
            <div class="cal-header">
                <h2>Kalender Time-Block</h2>
                <div style="display:flex;gap:10px;align-items:center;">
                    <button class="btn btn-primary" @click="openCreateModal()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 5v14M5 12h14"/></svg>
                        Jadwal Baru
                    </button>
                </div>
            </div>

            {{-- Legend --}}
            <div class="legend-row">
                <div class="legend-item"><div class="legend-dot" style="background:#6366F1;"></div>Aktif</div>
                <div class="legend-item"><div class="legend-dot" style="background:#EF4444;"></div>Prioritas Tinggi / Terlewat</div>
                <div class="legend-item"><div class="legend-dot" style="background:#F59E0B;"></div>Selesai Awal</div>
                <div class="legend-item"><div class="legend-dot" style="background:#10B981;"></div>Selesai</div>
                <div class="legend-item"><div class="legend-dot" style="background:#8B5CF6;"></div>Kustom</div>
            </div>

            {{-- FullCalendar mount target --}}
            <div id="arctic-calendar"></div>
        </div>
    </main>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Buat / Edit Jadwal
    ══════════════════════════════════════════════════════════════════ --}}
    <div class="modal-backdrop" x-show="showModal" x-cloak @click.self="closeModal()">
        <div class="modal-box" @click.stop>
            <h3 class="modal-title" x-text="modalMode === 'create' ? 'Jadwal Baru' : 'Edit Jadwal'"></h3>

            <div class="form-group">
                <label class="form-label">Nama Kegiatan *</label>
                <input type="text" class="form-control" x-model="form.title"
                       placeholder="Contoh: Rapat Tim Mingguan" />
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Mulai *</label>
                    <input type="datetime-local" class="form-control" x-model="form.start_datetime" />
                </div>
                <div class="form-group">
                    <label class="form-label">Selesai *</label>
                    <input type="datetime-local" class="form-control" x-model="form.end_datetime" />
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select class="form-control" x-model="form.category">
                        <option value="Pekerjaan">Pekerjaan</option>
                        <option value="Belajar">Belajar</option>
                        <option value="Pribadi">Pribadi</option>
                        <option value="Kesehatan">Kesehatan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas</label>
                    <select class="form-control" x-model="form.priority">
                        <option value="low">Rendah</option>
                        <option value="med">Sedang</option>
                        <option value="high">Tinggi</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Warna Blok</label>
                <div class="color-swatches">
                    <template x-for="c in colorSwatches" :key="c">
                        <div class="swatch" :style="`background:${c}`"
                             :class="{ active: form.color === c }"
                             @click="form.color = c"></div>
                    </template>
                    <div class="swatch" style="background:transparent;border:2px dashed var(--border-color);display:flex;align-items:center;justify-content:center;"
                         :class="{ active: !colorSwatches.includes(form.color) && form.color }"
                         title="Kustom">
                        <input type="color" x-model="form.color"
                               style="opacity:0;position:absolute;width:28px;height:28px;cursor:pointer;border:none;" />
                        <span style="font-size:14px;color:var(--text-muted);">+</span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Catatan</label>
                <textarea class="form-control" x-model="form.notes" rows="2"
                          placeholder="Catatan tambahan..."></textarea>
            </div>

            <div class="form-group">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px;font-weight:700;color:var(--text-main);">
                    <input type="checkbox" x-model="form.is_all_day" style="width:16px;height:16px;" />
                    Sepanjang hari
                </label>
            </div>

            <div class="modal-footer">
                <template x-if="modalMode === 'edit'">
                    <button class="btn btn-danger" @click="deleteEvent()" :disabled="isLoading">
                        Hapus
                    </button>
                </template>
                <button class="btn btn-ghost" @click="closeModal()" :disabled="isLoading">Batal</button>
                <button class="btn btn-primary" @click="submitForm()" :disabled="isLoading">
                    <span x-show="isLoading">Menyimpan...</span>
                    <span x-show="!isLoading" x-text="modalMode === 'create' ? 'Simpan' : 'Update'"></span>
                </button>
            </div>
        </div>
    </div>

</div>{{-- /main-wrapper --}}

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
// Configure Axios CSRF token
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;
axios.defaults.headers.common['Accept']        = 'application/json';

function calendarApp() {
    return {
        // ── State ──────────────────────────────────────────────────────────
        calendar: null,
        showModal: false,
        modalMode: 'create', // 'create' | 'edit'
        editingId: null,
        isLoading: false,
        toasts: [],

        colorSwatches: [
            '#6366F1', '#8B5CF6', '#EC4899',
            '#EF4444', '#F59E0B', '#10B981',
            '#14B8A6', '#3B82F6', '#0EA5E9',
            '#64748B',
        ],

        form: {
            title: '',
            start_datetime: '',
            end_datetime: '',
            is_all_day: false,
            color: '#6366F1',
            category: 'Lainnya',
            priority: 'low',
            notes: '',
        },

        // ── Init ───────────────────────────────────────────────────────────
        init() {
            const self = this;
            this.calendar = new FullCalendar.Calendar(document.getElementById('arctic-calendar'), {

                // ── View & locale ──────────────────────────────────────────
                initialView: 'timeGridWeek',
                locale: 'id',
                firstDay: 1, // Senin
                nowIndicator: true,
                scrollTime: '07:00:00',
                slotMinTime: '05:00:00',
                slotMaxTime: '23:59:00',
                slotDuration: '00:30:00',
                slotLabelInterval: '01:00',
                height: 'auto',
                expandRows: true,
                dayMaxEvents: 3,

                headerToolbar: {
                    left:   'prev,next today',
                    center: 'title',
                    right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
                },

                buttonText: {
                    today:   'Hari Ini',
                    month:   'Bulan',
                    week:    'Minggu',
                    day:     'Hari',
                    list:    'Agenda',
                },

                // ── Interaction ────────────────────────────────────────────
                editable: true,       // drag & resize
                selectable: true,     // klik-seret di slot kosong
                selectMirror: true,   // tampilkan placeholder saat seleksi
                unselectAuto: true,

                // ── Data source ────────────────────────────────────────────
                events: {
                    url: '/calendar/events',
                    method: 'GET',
                    failure() {
                        self.toast('Gagal memuat jadwal. Coba refresh halaman.', 'error');
                    },
                },

                // ── Handler: klik pada slot kosong (membuat jadwal baru) ───
                select(info) {
                    self.openCreateModal(info.startStr, info.endStr, info.allDay);
                    self.calendar.unselect();
                },

                // ── Handler: klik pada event (buka modal edit) ────────────
                eventClick(info) {
                    const p = info.event.extendedProps;
                    self.editingId = parseInt(info.event.id);
                    self.form = {
                        title:          info.event.title.replace(/^[✅⚠️🏃]\s/, ''),
                        start_datetime: self.toLocalDatetimeInput(info.event.start),
                        end_datetime:   self.toLocalDatetimeInput(info.event.end || info.event.start),
                        is_all_day:     info.event.allDay,
                        color:          p.color || info.event.backgroundColor,
                        category:       p.category || 'Lainnya',
                        priority:       p.priority || 'low',
                        notes:          p.notes || '',
                    };
                    self.modalMode = 'edit';
                    self.showModal = true;
                },

                // ── Handler: drag & drop selesai ──────────────────────────
                eventDrop(info) {
                    const ev = info.event;
                    self.updateEventTimes(
                        parseInt(ev.id),
                        ev.startStr,
                        ev.endStr || ev.startStr,
                        ev.allDay,
                        info.revert
                    );
                },

                // ── Handler: resize selesai ───────────────────────────────
                eventResize(info) {
                    const ev = info.event;
                    self.updateEventTimes(
                        parseInt(ev.id),
                        ev.startStr,
                        ev.endStr,
                        ev.allDay,
                        info.revert
                    );
                },

                // ── Render event tooltip saat hover ───────────────────────
                eventDidMount(info) {
                    const p = info.event.extendedProps;
                    const lines = [
                        p.category ? `Kategori: ${p.category}` : null,
                        p.priority ? `Prioritas: ${p.priority.toUpperCase()}` : null,
                        p.notes    ? `Catatan: ${p.notes.substring(0, 80)}` : null,
                        p.google_event_id ? '☁ Tersinkron Google' : null,
                    ].filter(Boolean).join('\n');

                    info.el.title = lines;
                },

                loading(isLoading) {
                    // bisa pasang spinner di sini jika perlu
                },
            });

            this.calendar.render();
        },

        // ── Open modal untuk buat jadwal baru ─────────────────────────────
        openCreateModal(startStr = null, endStr = null, allDay = false) {
            const now = new Date();
            const start = startStr
                ? new Date(startStr)
                : new Date(now.getFullYear(), now.getMonth(), now.getDate(),
                           now.getHours() + 1, 0, 0);
            const end = endStr
                ? new Date(endStr)
                : new Date(start.getTime() + 60 * 60 * 1000);

            this.editingId = null;
            this.modalMode = 'create';
            this.form = {
                title:          '',
                start_datetime: this.toLocalDatetimeInput(start),
                end_datetime:   this.toLocalDatetimeInput(end),
                is_all_day:     allDay,
                color:          '#6366F1',
                category:       'Lainnya',
                priority:       'low',
                notes:          '',
            };
            this.showModal = true;
            this.$nextTick(() => document.querySelector('.modal-box input[type="text"]')?.focus());
        },

        closeModal() {
            this.showModal = false;
            this.isLoading = false;
        },

        // ── Submit form (create atau update) ──────────────────────────────
        async submitForm() {
            if (!this.form.title.trim()) {
                this.toast('Nama kegiatan wajib diisi.', 'error');
                return;
            }
            if (!this.form.start_datetime || !this.form.end_datetime) {
                this.toast('Waktu mulai dan selesai wajib diisi.', 'error');
                return;
            }

            this.isLoading = true;
            try {
                let response;
                if (this.modalMode === 'create') {
                    response = await axios.post('/calendar/events', this.form);
                    this.toast('Jadwal berhasil ditambahkan!', 'success');
                } else {
                    response = await axios.patch(`/calendar/events/${this.editingId}`, this.form);
                    this.toast('Jadwal berhasil diperbarui!', 'success');
                }
                this.calendar.refetchEvents();
                this.closeModal();
            } catch (err) {
                const msg = err.response?.data?.message
                    || Object.values(err.response?.data?.errors || {})[0]?.[0]
                    || 'Terjadi kesalahan, coba lagi.';
                this.toast(msg, 'error');
            } finally {
                this.isLoading = false;
            }
        },

        // ── Hapus event ───────────────────────────────────────────────────
        async deleteEvent() {
            if (!confirm('Hapus jadwal ini?')) return;
            this.isLoading = true;
            try {
                await axios.delete(`/calendar/events/${this.editingId}`);
                this.toast('Jadwal dihapus.', 'success');
                this.calendar.refetchEvents();
                this.closeModal();
            } catch {
                this.toast('Gagal menghapus jadwal.', 'error');
            } finally {
                this.isLoading = false;
            }
        },

        // ── Update waktu setelah drag/resize ──────────────────────────────
        async updateEventTimes(id, startStr, endStr, allDay, revert) {
            try {
                await axios.patch(`/calendar/events/${id}`, {
                    start_datetime: startStr,
                    end_datetime:   endStr || startStr,
                    is_all_day:     allDay,
                });
                this.toast('Jadwal dipindahkan.', 'success');
            } catch {
                revert();
                this.toast('Gagal memindahkan jadwal.', 'error');
            }
        },

        // ── Helper: format Date → <input type=datetime-local> value ───────
        toLocalDatetimeInput(date) {
            if (!date) return '';
            const d = typeof date === 'string' ? new Date(date) : date;
            if (isNaN(d.getTime())) return '';
            const pad = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}` +
                   `T${pad(d.getHours())}:${pad(d.getMinutes())}`;
        },

        // ── Toast ─────────────────────────────────────────────────────────
        toast(msg, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, msg, type });
        },
    };
}
</script>
@endsection
