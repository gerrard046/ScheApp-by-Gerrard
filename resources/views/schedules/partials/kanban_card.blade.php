@php 
    $done = $item->is_completed; 
    $isEarly = $item->completed_early ?? false;
    $hoursEarly = $item->hours_early ?? 0;
@endphp
<div class="kanban-task {{ 'priority-' . ($item->priority ?? 'med') }} {{ $done ? 'completed' : '' }} {{ $isEarly ? 'early-complete' : '' }} {{ ($type ?? '') === 'missed' ? 'missed-task' : '' }}">
    <!-- Tags -->
    <div class="task-tags">
        <span class="task-tag">{{ $item->category }}</span>
        @if($item->priority === 'high')
            <span class="task-tag urgent">🔴 URGENT</span>
        @elseif($item->priority === 'low')
            <span class="task-tag low">🟢 LOW</span>
        @endif
        @if($isEarly)
            <span class="task-tag early">🏃 {{ $hoursEarly }}j lebih awal</span>
        @endif
        @if($item->group)
            <span class="task-tag" style="background: rgba(139, 92, 246, 0.1); color: #8B5CF6;">🤝 {{ $item->group->name }}</span>
        @endif
        @if($item->parentTask)
            <span class="task-tag" style="background: rgba(245, 158, 11, 0.1); color: #B45309;">⛓️ {{ $item->parentTask->activity_name }}</span>
        @endif
    </div>

    <!-- Title -->
    <div class="task-title" style="{{ $done ? 'text-decoration: line-through; opacity: 0.7;' : '' }}">
        {{ $item->activity_name }}
    </div>

    <!-- Meta -->
    <div class="task-meta">
        <span>📅 {{ \Carbon\Carbon::parse($item->date)->format('d M Y') }}</span>
        <span>⏰ {{ $item->time }}</span>
        @if($item->group_name)
            <span>🤝 {{ $item->group_name }}</span>
        @endif
    </div>

    <!-- Notes -->
    @if($item->notes)
    <div class="task-notes">
        📝 {{ Str::limit($item->notes, 80) }}
    </div>
    @endif

    <!-- Subtask Progress -->
    @if($item->subTasks && $item->subTasks->count() > 0)
    @php
        $totalSubs = $item->subTasks->count();
        $doneSubs = $item->subTasks->where('is_completed', true)->count();
        $percentage = $totalSubs > 0 ? round(($doneSubs / $totalSubs) * 100) : 0;
    @endphp
    <div class="subtask-progress">
        <div class="subtask-bar">
            <div class="subtask-fill" style="width: {{ $percentage }}%;"></div>
        </div>
        <div class="subtask-text">
            <span>Checklist</span>
            <span>{{ $doneSubs }}/{{ $totalSubs }} ({{ $percentage }}%)</span>
        </div>
    </div>
    @endif

    <!-- Actions -->
    <div class="task-actions">
        <form action="/schedules/{{ $item->id }}/toggle" method="POST" style="margin: 0;">
            @csrf
            <button type="submit" class="task-action-btn" title="{{ $done ? 'Batalkan' : 'Selesai' }}">
                {{ $done ? '↩️ Batal' : '✅ Selesai' }}
            </button>
        </form>
        
        @if(!$done)
        <form action="/schedules/{{ $item->id }}" method="POST" style="margin: 0;" onsubmit="return confirm('Hapus tugas ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="task-action-btn" style="color: #EF4444; border-color: rgba(239,68,68,0.3);">
                🗑️ Hapus
            </button>
        </form>
        @endif
    </div>
</div>
