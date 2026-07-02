@extends('layouts.app')

@section('content')
{{--
    KALENDER "ARCTIC BREEZE"
    Tema orisinal: glassmorphism, palet biru-putih. Tiga tampilan
    (Bulan/Minggu/Hari) yang bisa di-switch, navigasi periode, CRUD event
    lewat modal, color-coding prioritas, dan Floating Action Button.
    Seluruh data berasal dari Schedule milik user (lihat CalendarController).
--}}
<style>
    .arctic-cal {
        /* Palet Arctic Breeze (sesuai spesifikasi) */
        --bg:#EAF4FB; --bg2:#F5FAFE; --ink:#2E4057; --ink-soft:#5E7689;
        --line:rgba(46,64,87,0.10); --glass:rgba(255,255,255,0.62);
        --primary:#3E88D6; --primary-deep:#2E6DA4;
        /* Warna prioritas */
        --p-low:#5BA3E0; --p-med:#F5A623; --p-high:#E8576B;

        min-height: calc(100vh - 64px);
        background:
            radial-gradient(1200px 600px at 110% -10%, rgba(62,136,214,0.18), transparent 60%),
            radial-gradient(900px 500px at -10% 110%, rgba(91,163,224,0.16), transparent 55%),
            linear-gradient(180deg, var(--bg2), var(--bg));
        color: var(--ink);
        padding: 28px;
        font-family: var(--font-family);
    }

    /* Kartu kaca */
    .glass {
        background: var(--glass);
        backdrop-filter: blur(18px) saturate(150%);
        -webkit-backdrop-filter: blur(18px) saturate(150%);
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow: 0 10px 40px rgba(46,64,87,0.10);
        border-radius: 22px;
    }

    /* ---------- Header / Toolbar ---------- */
    .cal-header {
        display: flex; align-items: center; justify-content: space-between;
        gap: 16px; flex-wrap: wrap;
        padding: 16px 20px; margin-bottom: 20px;
        position: sticky; top: 76px; z-index: 50;
    }
    .cal-title { display:flex; flex-direction:column; }
    .cal-title small { font-size: 11px; font-weight:800; letter-spacing:1.5px; text-transform:uppercase; color: var(--primary-deep); }
    .cal-period { font-size: 24px; font-weight: 900; letter-spacing: -1px; color: var(--ink); }

    .cal-nav { display:flex; align-items:center; gap:8px; }
    .ico-btn {
        width: 40px; height: 40px; border-radius: 12px; cursor:pointer;
        border: 1px solid var(--line); background: rgba(255,255,255,0.7);
        color: var(--ink); font-size: 18px; font-weight: 800;
        display:flex; align-items:center; justify-content:center; transition: .2s;
    }
    .ico-btn:hover { background: var(--primary); color:#fff; transform: translateY(-1px); }
    .today-btn {
        padding: 0 16px; height:40px; border-radius:12px; cursor:pointer;
        border:1px solid var(--line); background: rgba(255,255,255,0.7);
        font-weight:800; color: var(--ink); font-size:13px; transition:.2s;
    }
    .today-btn:hover { background: var(--primary); color:#fff; }

    /* Switch tampilan */
    .view-switch { display:flex; background: rgba(255,255,255,0.6); border:1px solid var(--line); border-radius: 14px; padding: 4px; gap:2px; }
    .view-switch button {
        border:none; background:transparent; cursor:pointer; padding: 8px 16px;
        border-radius: 10px; font-weight: 800; font-size: 13px; color: var(--ink-soft); transition:.2s;
    }
    .view-switch button.active { background: linear-gradient(135deg, var(--primary), var(--primary-deep)); color:#fff; box-shadow: 0 6px 16px rgba(62,136,214,0.35); }

    /* ---------- Tampilan BULAN ---------- */
    .month-grid { display:grid; grid-template-columns: repeat(7,1fr); }
    .month-dow { padding: 12px 0; text-align:center; font-size:11px; font-weight:900; letter-spacing:1px; text-transform:uppercase; color: var(--ink-soft); border-bottom:1px solid var(--line); }
    .month-cells { display:grid; grid-template-columns: repeat(7,1fr); }
    .day-cell {
        min-height: 116px; border-right:1px solid var(--line); border-bottom:1px solid var(--line);
        padding: 8px; cursor:pointer; transition: background .2s; position:relative;
    }
    .day-cell:hover { background: rgba(62,136,214,0.06); }
    .day-cell.is-other { opacity: 0.42; }
    .day-num { font-size: 13px; font-weight: 800; color: var(--ink); width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:50%; }
    .day-cell.is-today .day-num { background: var(--primary); color:#fff; }
    .ev-pill {
        font-size: 11px; font-weight:700; color:#fff; padding: 3px 8px; border-radius: 7px;
        margin-top: 4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; cursor:pointer;
        box-shadow: 0 2px 6px rgba(46,64,87,0.15);
    }
    .ev-pill.done { opacity:.55; text-decoration: line-through; }
    .more-tag { font-size:10px; font-weight:800; color: var(--ink-soft); margin-top:4px; }

    /* ---------- Tampilan MINGGU & HARI (grid jam) ---------- */
    .time-wrap { display:grid; grid-template-columns: 64px 1fr; max-height: 70vh; overflow-y:auto; }
    .time-col { display:flex; flex-direction:column; }
    .time-label { height:56px; font-size:11px; font-weight:800; color:var(--ink-soft); text-align:right; padding-right:10px; position:relative; top:-7px; }
    .week-cols { display:grid; }
    .week-head { display:grid; position:sticky; top:0; z-index:5; background: var(--glass); backdrop-filter: blur(10px); }
    .week-head .wh-cell { padding:10px 4px; text-align:center; border-left:1px solid var(--line); }
    .week-head .wh-dow { font-size:11px; font-weight:900; text-transform:uppercase; color:var(--ink-soft); letter-spacing:1px; }
    .week-head .wh-num { font-size:18px; font-weight:900; color:var(--ink); margin-top:2px; }
    .week-head .wh-cell.is-today .wh-num { color: var(--primary); }
    .day-column { position:relative; border-left:1px solid var(--line); }
    .hour-slot { height:56px; border-bottom:1px solid var(--line); cursor:pointer; transition: background .15s; }
    .hour-slot:hover { background: rgba(62,136,214,0.07); }
    .time-event {
        position:absolute; left:4px; right:4px; border-radius:10px; padding:6px 9px; color:#fff;
        font-size:12px; font-weight:800; overflow:hidden; cursor:pointer; z-index:3;
        box-shadow: 0 4px 14px rgba(46,64,87,0.22); border:1px solid rgba(255,255,255,0.35);
    }
    .time-event small { display:block; font-weight:600; font-size:10px; opacity:.9; }
    .time-event.done { opacity:.6; }

    /* ---------- Tampilan HARI (timeline + agenda) ---------- */
    .day-layout { display:grid; grid-template-columns: 1fr 300px; gap:18px; }
    .agenda { padding: 18px; max-height:70vh; overflow-y:auto; }
    .agenda h3 { margin:0 0 14px; font-size:15px; font-weight:900; color:var(--ink); }
    .agenda-item { display:flex; gap:10px; align-items:flex-start; padding:12px; border-radius:14px; background:rgba(255,255,255,0.55); margin-bottom:10px; cursor:pointer; border:1px solid var(--line); transition:.2s; }
    .agenda-item:hover { transform: translateX(3px); box-shadow:0 6px 16px rgba(46,64,87,0.10); }
    .agenda-dot { width:10px; height:10px; border-radius:50%; margin-top:5px; flex-shrink:0; }
    .agenda-item .ai-title { font-size:13px; font-weight:800; color:var(--ink); }
    .agenda-item .ai-time { font-size:11px; font-weight:700; color:var(--ink-soft); }
    .agenda-empty { text-align:center; color:var(--ink-soft); padding:30px 10px; font-weight:700; font-size:13px; }

    /* ---------- Floating Action Button ---------- */
    .fab {
        position: fixed; right: 28px; bottom: 28px; z-index: 9000;
        width: 60px; height: 60px; border-radius: 50%; cursor:pointer; border:none;
        background: linear-gradient(135deg, var(--primary), var(--primary-deep));
        color:#fff; font-size: 30px; font-weight:300; line-height:1;
        box-shadow: 0 12px 30px rgba(62,136,214,0.45); transition:.25s;
        display:flex; align-items:center; justify-content:center;
    }
    .fab:hover { transform: translateY(-3px) scale(1.05) rotate(90deg); }

    /* ---------- Modal ---------- */
    .modal-overlay {
        position:fixed; inset:0; z-index:10000; display:flex; align-items:center; justify-content:center; padding:20px;
        background: rgba(46,64,87,0.35); backdrop-filter: blur(8px);
    }
    .modal-card { width:100%; max-width: 440px; padding: 28px; }
    .modal-card h2 { margin:0 0 18px; font-size:20px; font-weight:900; color:var(--ink); }
    .field { margin-bottom:14px; }
    .field label { display:block; font-size:11px; font-weight:900; letter-spacing:.8px; text-transform:uppercase; color:var(--ink-soft); margin-bottom:6px; }
    .a-input {
        width:100%; padding:12px 14px; border-radius:12px; border:1.5px solid var(--line);
        background: rgba(255,255,255,0.7); font-size:14px; font-weight:600; color:var(--ink);
        font-family:inherit; outline:none; transition:.2s; box-sizing:border-box;
    }
    .a-input:focus { border-color: var(--primary); box-shadow:0 0 0 4px rgba(62,136,214,0.15); }
    .prio-row { display:flex; gap:8px; }
    .prio-chip { flex:1; text-align:center; padding:10px; border-radius:12px; border:2px solid var(--line); cursor:pointer; font-weight:800; font-size:13px; color:var(--ink-soft); transition:.2s; background:rgba(255,255,255,0.5); }
    .prio-chip.active { color:#fff; border-color:transparent; box-shadow:0 6px 14px rgba(46,64,87,0.2); }
    .modal-actions { display:flex; gap:10px; margin-top:22px; }
    .btn-save { flex:1; padding:13px; border:none; border-radius:12px; cursor:pointer; font-weight:900; font-size:14px; color:#fff; background:linear-gradient(135deg, var(--primary), var(--primary-deep)); transition:.2s; }
    .btn-save:hover { transform:translateY(-2px); box-shadow:0 8px 20px rgba(62,136,214,0.4); }
    .btn-del { padding:13px 16px; border:none; border-radius:12px; cursor:pointer; font-weight:900; font-size:14px; color:#fff; background: var(--p-high); transition:.2s; }
    .btn-del:hover { transform:translateY(-2px); }
    .modal-close { position:absolute; top:18px; right:18px; background:none; border:none; font-size:24px; cursor:pointer; color:var(--ink-soft); }

    @media (max-width:768px) {
        .arctic-cal { padding:14px; }
        .cal-header { top:64px; }
        .day-layout { grid-template-columns: 1fr; }
        .day-cell { min-height: 84px; }
    }
</style>

{{-- PENTING: atribut x-data pakai kutip TUNGGAL karena @json menghasilkan
     tanda kutip ganda (") — kalau atributnya juga kutip ganda, HTML-nya putus
     begitu ada event, dan seluruh kalender gagal render. --}}
<div class="arctic-cal" x-data='calendarApp(@json($events))' x-cloak>

    {{-- ===== Header: navigasi + label periode + switch tampilan ===== --}}
    <div class="cal-header glass">
        <div class="cal-title">
            <small>❄️ Arctic Breeze</small>
            <span class="cal-period" x-text="periodLabel()"></span>
        </div>

        <div class="cal-nav">
            <button class="ico-btn" @click="prev()" title="Sebelumnya">‹</button>
            <button class="today-btn" @click="goToday()">Hari ini</button>
            <button class="ico-btn" @click="next()" title="Berikutnya">›</button>
        </div>

        <div class="view-switch">
            <button :class="{ active: view==='month' }" @click="view='month'">Bulan</button>
            <button :class="{ active: view==='week' }"  @click="view='week'">Minggu</button>
            <button :class="{ active: view==='day' }"   @click="view='day'">Hari</button>
        </div>
    </div>

    {{-- ============ TAMPILAN BULAN ============ --}}
    <div class="glass" style="overflow:hidden;" x-show="view==='month'">
        <div class="month-grid">
            <template x-for="d in dayNames" :key="d">
                <div class="month-dow" x-text="d"></div>
            </template>
        </div>
        <div class="month-cells">
            <template x-for="cell in monthCells()" :key="cell.iso">
                <div class="day-cell" :class="{ 'is-other': !cell.inMonth, 'is-today': cell.iso===todayIso }"
                     @click="openCreate(cell.iso, 9)">
                    <div class="day-num" x-text="cell.day"></div>
                    {{-- Preview maksimal 3 event per hari --}}
                    <template x-for="ev in eventsOn(cell.iso).slice(0,3)" :key="ev.id">
                        <div class="ev-pill" :class="{ done: ev.is_completed }"
                             :style="`background:${ev.color}`"
                             @click.stop="openEdit(ev)"
                             x-text="`${pad(ev.start_hour)}:00 ${ev.title}`"></div>
                    </template>
                    <div class="more-tag" x-show="eventsOn(cell.iso).length > 3"
                         x-text="`+${eventsOn(cell.iso).length - 3} lagi`"></div>
                </div>
            </template>
        </div>
    </div>

    {{-- ============ TAMPILAN MINGGU ============ --}}
    <div class="glass" style="overflow:hidden;" x-show="view==='week'">
        {{-- Header hari --}}
        <div style="display:grid; grid-template-columns:64px 1fr;">
            <div></div>
            <div class="week-head" :style="`grid-template-columns: repeat(7,1fr)`">
                <template x-for="d in weekDays()" :key="d.iso">
                    <div class="wh-cell" :class="{ 'is-today': d.iso===todayIso }">
                        <div class="wh-dow" x-text="d.dow"></div>
                        <div class="wh-num" x-text="d.day"></div>
                    </div>
                </template>
            </div>
        </div>
        {{-- Grid jam 0-23 x 7 hari --}}
        <div class="time-wrap">
            <div class="time-col">
                <template x-for="h in hours" :key="h">
                    <div class="time-label" x-text="`${pad(h)}:00`"></div>
                </template>
            </div>
            <div class="week-cols" :style="`grid-template-columns: repeat(7,1fr)`">
                <template x-for="d in weekDays()" :key="d.iso">
                    <div class="day-column">
                        <template x-for="h in hours" :key="h">
                            <div class="hour-slot" @click="openCreate(d.iso, h)"></div>
                        </template>
                        {{-- Blok event diposisikan sesuai jam --}}
                        <template x-for="ev in eventsOn(d.iso)" :key="ev.id">
                            <div class="time-event" :class="{ done: ev.is_completed }"
                                 :style="blockStyle(ev) + `background:${ev.color};`"
                                 @click.stop="openEdit(ev)">
                                <span x-text="ev.title"></span>
                                <small x-text="`${pad(ev.start_hour)}:00 - ${pad(ev.end_hour)}:00`"></small>
                            </div>
                        </template>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- ============ TAMPILAN HARI (timeline + agenda) ============ --}}
    <div x-show="view==='day'" class="day-layout">
        {{-- Timeline detail --}}
        <div class="glass" style="overflow:hidden;">
            <div class="time-wrap">
                <div class="time-col">
                    <template x-for="h in hours" :key="h">
                        <div class="time-label" x-text="`${pad(h)}:00`"></div>
                    </template>
                </div>
                <div class="day-column" style="border-left:1px solid var(--line);">
                    <template x-for="h in hours" :key="h">
                        <div class="hour-slot" @click="openCreate(cursorIso(), h)"></div>
                    </template>
                    <template x-for="ev in eventsOn(cursorIso())" :key="ev.id">
                        <div class="time-event" :class="{ done: ev.is_completed }"
                             :style="blockStyle(ev) + `background:${ev.color};`"
                             @click.stop="openEdit(ev)">
                            <span x-text="ev.title"></span>
                            <small x-text="`${pad(ev.start_hour)}:00 - ${pad(ev.end_hour)}:00`"></small>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        {{-- Panel agenda di samping --}}
        <div class="glass agenda">
            <h3>Agenda Hari Ini</h3>
            <template x-for="ev in eventsOn(cursorIso())" :key="ev.id">
                <div class="agenda-item" @click="openEdit(ev)">
                    <div class="agenda-dot" :style="`background:${ev.color}`"></div>
                    <div>
                        <div class="ai-title" :style="ev.is_completed ? 'text-decoration:line-through;opacity:.6' : ''" x-text="ev.title"></div>
                        <div class="ai-time" x-text="`${pad(ev.start_hour)}:00 - ${pad(ev.end_hour)}:00 · ${prioLabel(ev.priority)}`"></div>
                        <div class="ai-time" x-show="ev.notes" x-text="`${ev.notes}`"></div>
                    </div>
                </div>
            </template>
            <div class="agenda-empty" x-show="eventsOn(cursorIso()).length===0">
                Tidak ada agenda. Klik slot waktu atau tombol + untuk menambah.
            </div>
        </div>
    </div>

    {{-- ============ Floating Action Button ============ --}}
    <button class="fab" @click="openCreate(cursorIso(), 9)" title="Tambah cepat">+</button>

    {{-- ============ Modal CRUD ============ --}}
    <div class="modal-overlay" x-show="modalOpen" x-transition @click.self="closeModal()" style="display:none;">
        <div class="glass modal-card" style="position:relative;">
            <button class="modal-close" @click="closeModal()">&times;</button>
            <h2 x-text="form.id ? 'Edit Event' : 'Event Baru'"></h2>

            <div class="field">
                <label>Judul Kegiatan</label>
                <input type="text" class="a-input" x-model="form.title" placeholder="Apa yang akan kamu lakukan?">
                <div x-show="error" style="color:var(--p-high); font-size:11px; font-weight:700; margin-top:5px;" x-text="error"></div>
            </div>

            <div class="field">
                <label>Tanggal</label>
                <input type="date" class="a-input" x-model="form.date">
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                <div class="field">
                    <label>Jam Mulai</label>
                    <select class="a-input" x-model.number="form.start_hour">
                        <template x-for="h in hours" :key="h">
                            <option :value="h" x-text="`${pad(h)}:00`"></option>
                        </template>
                    </select>
                </div>
                <div class="field">
                    <label>Jam Selesai</label>
                    <select class="a-input" x-model.number="form.end_hour">
                        <template x-for="h in hours" :key="h">
                            <option :value="h" x-text="`${pad(h)}:00`"></option>
                        </template>
                    </select>
                </div>
            </div>

            <div class="field">
                <label>Prioritas</label>
                <div class="prio-row">
                    <div class="prio-chip" :class="{ active: form.priority==='low' }"
                         :style="form.priority==='low' ? 'background:var(--p-low)' : ''"
                         @click="form.priority='low'">Low</div>
                    <div class="prio-chip" :class="{ active: form.priority==='med' }"
                         :style="form.priority==='med' ? 'background:var(--p-med)' : ''"
                         @click="form.priority='med'">Medium</div>
                    <div class="prio-chip" :class="{ active: form.priority==='high' }"
                         :style="form.priority==='high' ? 'background:var(--p-high)' : ''"
                         @click="form.priority='high'">High</div>
                </div>
            </div>

            <div class="field">
                <label>Catatan</label>
                <textarea class="a-input" x-model="form.notes" style="min-height:70px; resize:vertical;" placeholder="Catatan tambahan (opsional)..."></textarea>
            </div>

            <div class="modal-actions">
                <button class="btn-del" x-show="form.id" @click="remove()" :disabled="saving">Hapus</button>
                <button class="btn-save" @click="save()" :disabled="saving"
                        x-text="saving ? 'Menyimpan...' : (form.id ? 'Simpan Perubahan' : 'Tambah Event')"></button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('calendarApp', (initialEvents) => ({
        view: 'month',
        cursor: new Date(),
        events: initialEvents || [],
        hours: Array.from({ length: 24 }, (_, i) => i),
        dayNames: ['Min','Sen','Sel','Rab','Kam','Jum','Sab'],
        hourHeight: 56,
        todayIso: '',
        csrf: '{{ csrf_token() }}',

        // State modal
        modalOpen: false,
        saving: false,
        error: '',
        form: { id: null, title: '', date: '', start_hour: 9, end_hour: 10, priority: 'med', notes: '' },

        init() {
            this.todayIso = this.fmt(new Date());
        },

        pad(n) { return String(n).padStart(2, '0'); },
        fmt(d) { return `${d.getFullYear()}-${this.pad(d.getMonth() + 1)}-${this.pad(d.getDate())}`; },
        cursorIso() { return this.fmt(this.cursor); },

        prioLabel(p) { return p === 'high' ? 'High' : (p === 'low' ? 'Low' : 'Medium'); },

        periodLabel() {
            if (this.view === 'month') {
                return this.cursor.toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
            }
            if (this.view === 'week') {
                const days = this.weekDays();
                const a = new Date(days[0].iso), b = new Date(days[6].iso);
                const fa = a.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                const fb = b.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                return `${fa} - ${fb}`;
            }
            return this.cursor.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        },

        prev() { this.shift(-1); },
        next() { this.shift(1); },
        goToday() { this.cursor = new Date(); },
        shift(dir) {
            const d = new Date(this.cursor);
            if (this.view === 'month') d.setMonth(d.getMonth() + dir);
            else if (this.view === 'week') d.setDate(d.getDate() + 7 * dir);
            else d.setDate(d.getDate() + dir);
            this.cursor = d;
        },

        eventsOn(iso) {
            return this.events
                .filter(e => e.date === iso)
                .sort((a, b) => a.start_hour - b.start_hour);
        },
        blockStyle(ev) {
            const top = ev.start_hour * this.hourHeight;
            const h = Math.max(ev.end_hour - ev.start_hour, 1) * this.hourHeight;
            return `top:${top}px; height:${h - 4}px;`;
        },

        monthCells() {
            const year = this.cursor.getFullYear(), month = this.cursor.getMonth();
            const first = new Date(year, month, 1);
            const start = new Date(first);
            start.setDate(first.getDate() - first.getDay());
            const cells = [];
            for (let i = 0; i < 42; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                cells.push({ iso: this.fmt(d), day: d.getDate(), inMonth: d.getMonth() === month });
            }
            return cells;
        },

        weekDays() {
            const start = new Date(this.cursor);
            start.setDate(this.cursor.getDate() - this.cursor.getDay());
            const out = [];
            for (let i = 0; i < 7; i++) {
                const d = new Date(start);
                d.setDate(start.getDate() + i);
                out.push({ iso: this.fmt(d), day: d.getDate(), dow: this.dayNames[d.getDay()] });
            }
            return out;
        },

        openCreate(iso, hour) {
            this.error = '';
            this.form = {
                id: null, title: '', date: iso,
                start_hour: hour, end_hour: Math.min(hour + 1, 23),
                priority: 'med', notes: ''
            };
            this.modalOpen = true;
        },
        openEdit(ev) {
            if (!ev.editable) { return; }
            this.error = '';
            this.form = {
                id: ev.id, title: ev.title, date: ev.date,
                start_hour: ev.start_hour, end_hour: ev.end_hour,
                priority: ev.priority, notes: ev.notes || ''
            };
            this.modalOpen = true;
        },
        closeModal() { this.modalOpen = false; },

        async save() {
            if (!this.form.title || this.form.title.trim().length < 3) {
                this.error = 'Judul minimal 3 karakter.';
                return;
            }
            this.saving = true;
            const isEdit = !!this.form.id;
            const url = isEdit ? `/calendar/events/${this.form.id}` : '/calendar/events';
            const payload = {
                activity_name: this.form.title,
                event_date: this.form.date,
                start_hour: this.form.start_hour,
                end_hour: this.form.end_hour,
                priority: this.form.priority,
                notes: this.form.notes,
            };
            try {
                const res = await fetch(url, {
                    method: isEdit ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': this.csrf,
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Gagal menyimpan.');

                if (isEdit) {
                    const idx = this.events.findIndex(e => e.id === data.event.id);
                    if (idx !== -1) this.events.splice(idx, 1, data.event);
                } else {
                    this.events.push(data.event);
                }
                this.closeModal();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.saving = false;
            }
        },

        async remove() {
            if (!confirm('Hapus event ini?')) return;
            this.saving = true;
            try {
                const res = await fetch(`/calendar/events/${this.form.id}`, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                });
                const data = await res.json();
                if (!res.ok || !data.success) throw new Error(data.message || 'Gagal menghapus.');
                this.events = this.events.filter(e => e.id !== this.form.id);
                this.closeModal();
            } catch (e) {
                this.error = e.message;
            } finally {
                this.saving = false;
            }
        },
    }));
});
</script>
@endsection
