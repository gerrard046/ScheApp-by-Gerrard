@extends('layouts.app')

@section('content')
<style>
    .kanban-board {
        display: flex;
        gap: 25px;
        padding: 40px;
        overflow-x: auto;
        min-height: calc(100vh - 100px);
    }
    .kanban-column {
        min-width: 350px;
        max-width: 350px;
        background: rgba(255,255,255,0.4);
        border-radius: 24px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        border: 1px solid var(--border-color);
        backdrop-filter: blur(10px);
    }
    .column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 10px 10px 10px;
    }
    .column-title {
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: var(--text-muted);
    }
</style>

<div class="main-wrapper" style="background: var(--soft-bg);">
    <div class="kanban-board">
        <!-- 1. TODO -->
        <div class="kanban-column">
            <div class="column-header">
                <span class="column-title">🎯 To Do</span>
                <span style="background: #1E88E5; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 900;">{{ $board['todo']->count() }}</span>
            </div>
            @foreach($board['todo'] as $item)
                @include('schedules.partials.task_card', ['item' => $item])
            @endforeach
        </div>

        <!-- 2. MISSED (Backlog) -->
        <div class="kanban-column">
            <div class="column-header">
                <span class="column-title">⚠️ Missed / Overdue</span>
                <span style="background: #EF4444; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 900;">{{ $board['missed']->count() }}</span>
            </div>
            @foreach($board['missed'] as $item)
                <div style="border: 2px solid #EF4444; border-radius: 20px;">
                    @include('schedules.partials.task_card', ['item' => $item])
                </div>
            @endforeach
        </div>

        <!-- 3. DONE -->
        <div class="kanban-column">
            <div class="column-header">
                <span class="column-title">✅ Completed</span>
                <span style="background: #10B981; color: white; padding: 2px 8px; border-radius: 10px; font-size: 11px; font-weight: 900;">{{ $board['done']->count() }}</span>
            </div>
            @foreach($board['done'] as $item)
                @include('schedules.partials.task_card', ['item' => $item])
            @endforeach
        </div>
    </div>
</div>
@endsection
