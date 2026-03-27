@extends('layouts.app')

@section('content')
<style>
    .kanban-wrapper {
        display: flex;
        min-height: 100vh;
        background: var(--soft-bg);
    }

    .kanban-sidebar {
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

    .kanban-main {
        flex-grow: 1;
        padding: 40px;
        overflow-x: auto;
    }

    .kanban-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .kanban-board {
        display: flex;
        gap: 24px;
        min-height: calc(100vh - 200px);
    }

    .kanban-column {
        min-width: 340px;
        max-width: 340px;
        background: var(--card-bg);
        border-radius: 24px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 12px;
        border: 1px solid var(--border-color);
        backdrop-filter: blur(10px);
        box-shadow: var(--vibrant-shadow);
        transition: all 0.3s ease;
    }

    .kanban-column:hover {
        box-shadow: 0 12px 40px rgba(30, 136, 229, 0.12);
    }

    .column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px 16px 12px;
        border-bottom: 2px solid var(--border-color);
        margin-bottom: 4px;
    }

    .column-title {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--text-muted);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .column-count {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 900;
        color: white;
        min-width: 28px;
        text-align: center;
    }

    /* Kanban Task Card */
    .kanban-task {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 16px;
        transition: all 0.25s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .kanban-task:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    }

    .kanban-task::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .kanban-task.priority-high::before { background: #EF4444; }
    .kanban-task.priority-med::before { background: #1E88E5; }
    .kanban-task.priority-low::before { background: #10B981; }

    .kanban-task.completed {
        opacity: 0.7;
        background: var(--soft-bg);
    }

    .kanban-task.completed::before {
        background: #10B981;
    }

    .kanban-task.early-complete::before {
        background: linear-gradient(180deg, #F59E0B, #EF4444);
    }

    .kanban-task.missed-task {
        border-color: rgba(239, 68, 68, 0.3);
    }

    .task-tags {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 10px;
    }

    .task-tag {
        font-size: 10px;
        font-weight: 800;
        padding: 3px 8px;
        border-radius: 6px;
        background: var(--soft-bg);
        color: var(--text-muted);
    }

    .task-tag.urgent { background: rgba(239, 68, 68, 0.1); color: #EF4444; }
    .task-tag.early { background: rgba(245, 158, 11, 0.1); color: #D97706; }
    .task-tag.low { background: rgba(16, 185, 129, 0.1); color: #10B981; }

    .task-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 6px;
        line-height: 1.4;
    }

    .task-meta {
        font-size: 11px;
        color: var(--text-muted);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        flex-wrap: wrap;
    }

    .task-notes {
        font-size: 11px;
        color: var(--text-muted);
        background: var(--soft-bg);
        padding: 8px 12px;
        border-radius: 10px;
        margin-top: 8px;
        line-height: 1.4;
        border-left: 3px solid var(--border-color);
    }

    .task-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 12px;
        padding-top: 10px;
        border-top: 1px solid var(--border-color);
    }

    .task-action-btn {
        background: none;
        border: 1px solid var(--border-color);
        border-radius: 10px;
        padding: 6px 12px;
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
        color: var(--text-main);
        transition: 0.2s;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .task-action-btn:hover {
        background: var(--soft-bg);
        transform: scale(1.02);
    }

    /* Subtask progress bar */
    .subtask-progress {
        margin-top: 10px;
    }

    .subtask-bar {
        width: 100%;
        height: 4px;
        background: var(--soft-bg);
        border-radius: 10px;
        overflow: hidden;
    }

    .subtask-fill {
        height: 100%;
        background: var(--primary-gradient);
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .subtask-text {
        font-size: 10px;
        color: var(--text-muted);
        font-weight: 700;
        margin-top: 4px;
        display: flex;
        justify-content: space-between;
    }

    /* Empty state */
    .kanban-empty {
        text-align: center;
        padding: 30px 20px;
        color: var(--text-muted);
    }

    .kanban-empty-icon {
        font-size: 40px;
        margin-bottom: 10px;
    }

    .kanban-empty-text {
        font-size: 12px;
        font-weight: 700;
    }

    /* Stats Summary */
    .kanban-stats {
        display: flex;
        gap: 16px;
        margin-bottom: 30px;
    }

    .kanban-stat-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--vibrant-shadow);
        transition: 0.3s;
    }

    .kanban-stat-card:hover { transform: translateY(-3px); }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-number {
        font-size: 24px;
        font-weight: 900;
        color: var(--text-main);
    }

    .stat-label {
        font-size: 10px;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .kanban-sidebar { display: none; }
        .kanban-main { padding: 20px; }
        .kanban-column { min-width: 300px; }
        .kanban-stats { flex-wrap: wrap; }
    }
</style>

<div class="kanban-wrapper">
    <!-- Sidebar -->
    <aside class="kanban-sidebar">
        <div style="margin-bottom: 30px;">
            <h1 style="font-size: 22px; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main); display: flex; align-items: center; gap: 8px;">
                <span style="color: #1E88E5;">⚡</span> ScheApp
            </h1>
        </div>
        <nav>
            <a href="/schedules" class="nav-link"><span>🏠</span> Dashboard</a>
            <a href="/calendar" class="nav-link"><span>📅</span> Kalender</a>
            <a href="/kanban" class="nav-link active"><span>📋</span> Kanban Board</a>
            <a href="/groups" class="nav-link"><span>🤝</span> Tim Grup</a>
            <a href="/profile" class="nav-link"><span>👤</span> Profil</a>
            @if(auth()->user()->role === 'admin')
            <a href="/admin/insights" class="nav-link"><span>📈</span> Insights</a>
            @endif
        </nav>
        <div style="margin-top: auto; padding: 20px; background: var(--soft-bg); border-radius: 18px; text-align: center; border: 1px solid var(--border-color);">
            <div style="font-size: 28px; margin-bottom: 8px;">📋</div>
            <h4 style="font-size: 12px; font-weight: 800; color: var(--text-main); text-transform: uppercase; letter-spacing: 1px;">Kanban Board</h4>
            <p style="font-size: 10px; color: var(--text-muted); margin-top: 4px; line-height: 1.5;">Visualisasikan semua tugas secara efisien.</p>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="kanban-main">
        <div class="kanban-header">
            <div>
                <h1 style="font-size: 28px; font-weight: 900; letter-spacing: -1.5px; color: var(--text-main); margin-bottom: 6px;">📋 Kanban Board</h1>
                <p style="font-size: 13px; color: var(--text-muted); font-weight: 600;">Semua jadwal dari Dashboard, Kalender, dan Kanban terintegrasi otomatis.</p>
            </div>
        </div>

        <!-- Stats Summary -->
        <div class="kanban-stats">
            <div class="kanban-stat-card">
                <div class="stat-icon" style="background: rgba(30, 136, 229, 0.1);">🎯</div>
                <div>
                    <div class="stat-number">{{ $board['todo']->count() }}</div>
                    <div class="stat-label">To Do</div>
                </div>
            </div>
            <div class="kanban-stat-card">
                <div class="stat-icon" style="background: rgba(239, 68, 68, 0.1);">⚠️</div>
                <div>
                    <div class="stat-number" style="color: #EF4444;">{{ $board['missed']->count() }}</div>
                    <div class="stat-label">Terlewat</div>
                </div>
            </div>
            <div class="kanban-stat-card">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1);">✅</div>
                <div>
                    <div class="stat-number" style="color: #10B981;">{{ $board['done']->count() }}</div>
                    <div class="stat-label">Selesai</div>
                </div>
            </div>
            @php
                $earlyCount = $board['done']->where('completed_early', true)->count();
            @endphp
            @if($earlyCount > 0)
            <div class="kanban-stat-card">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1);">🏃</div>
                <div>
                    <div class="stat-number" style="color: #F59E0B;">{{ $earlyCount }}</div>
                    <div class="stat-label">Selesai Awal</div>
                </div>
            </div>
            @endif
        </div>

        <!-- Kanban Columns -->
        <div class="kanban-board">
            <!-- 1. TODO -->
            <div class="kanban-column">
                <div class="column-header">
                    <span class="column-title">
                        🎯 To Do
                    </span>
                    <span class="column-count" style="background: #1E88E5;">{{ $board['todo']->count() }}</span>
                </div>
                @forelse($board['todo'] as $item)
                    @include('schedules.partials.kanban_card', ['item' => $item, 'type' => 'todo'])
                @empty
                    <div class="kanban-empty">
                        <div class="kanban-empty-icon">🧊</div>
                        <div class="kanban-empty-text">Semua tugas sudah selesai!</div>
                    </div>
                @endforelse
            </div>

            <!-- 2. MISSED -->
            <div class="kanban-column">
                <div class="column-header">
                    <span class="column-title">
                        ⚠️ Terlewat
                    </span>
                    <span class="column-count" style="background: #EF4444;">{{ $board['missed']->count() }}</span>
                </div>
                @forelse($board['missed'] as $item)
                    @include('schedules.partials.kanban_card', ['item' => $item, 'type' => 'missed'])
                @empty
                    <div class="kanban-empty">
                        <div class="kanban-empty-icon">🎉</div>
                        <div class="kanban-empty-text">Tidak ada yang terlewat. Mantap!</div>
                    </div>
                @endforelse
            </div>

            <!-- 3. DONE -->
            <div class="kanban-column">
                <div class="column-header">
                    <span class="column-title">
                        ✅ Selesai
                    </span>
                    <span class="column-count" style="background: #10B981;">{{ $board['done']->count() }}</span>
                </div>
                @forelse($board['done'] as $item)
                    @include('schedules.partials.kanban_card', ['item' => $item, 'type' => 'done'])
                @empty
                    <div class="kanban-empty">
                        <div class="kanban-empty-icon">📝</div>
                        <div class="kanban-empty-text">Belum ada tugas yang selesai.</div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
