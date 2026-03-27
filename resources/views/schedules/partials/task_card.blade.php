@php 
    $done = $item->is_completed; 
    $isEarly = $item->completed_early ?? false;
    $hoursEarly = $item->hours_early ?? 0;
    $deadline = \Carbon\Carbon::parse($item->date . ' ' . $item->time);
    $isBeforeDeadline = !$done && $deadline->isFuture();
    $timeLeft = $isBeforeDeadline ? now()->diffForHumans($deadline, ['parts' => 1, 'short' => true]) : '';
@endphp
<div class="zen-card card-item" data-text="{{ strtolower($item->activity_name.' '.$item->group_name) }}" 
    style="padding: 20px; opacity: {{ $done ? '0.6' : '1' }}; border-left: 4px solid {{ $item->priority === 'high' ? '#EF4444' : ($item->priority === 'low' ? '#10B981' : '#1E88E5') }}; {{ $done ? 'background: var(--soft-bg);' : '' }} {{ $isEarly ? 'border-left-color: #F59E0B;' : '' }}">
    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
        <div style="flex: 1;">
            <!-- Tags Row -->
            <div style="display: flex; gap: 6px; align-items: center; margin-bottom: 10px; flex-wrap: wrap;">
                <span style="font-size: 10px; font-weight: 800; background: var(--soft-bg); color: var(--text-muted); padding: 3px 10px; border-radius: 6px;">{{ $item->category }}</span>
                @if($item->priority === 'high')
                <span style="font-size: 10px; font-weight: 800; background: rgba(239, 68, 68, 0.1); color: #EF4444; padding: 3px 10px; border-radius: 6px;">🔴 URGENT</span>
                @elseif($item->priority === 'low')
                <span style="font-size: 10px; font-weight: 800; background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 3px 10px; border-radius: 6px;">🟢 LOW</span>
                @endif
                @if(isset($is_future) && $is_future)
                <span style="font-size: 10px; font-weight: 800; background: rgba(30, 136, 229, 0.1); color: #1E88E5; padding: 3px 10px; border-radius: 6px;">📅 {{ \Carbon\Carbon::parse($item->date)->format('d M') }}</span>
                @endif
                @if($item->parentTask)
                <span style="font-size: 10px; font-weight: 800; background: rgba(245, 158, 11, 0.1); color: #B45309; padding: 3px 10px; border-radius: 6px;">⛓️ {{ $item->parentTask->activity_name }}</span>
                @endif
                @if($isEarly)
                <span style="font-size: 10px; font-weight: 800; background: rgba(245, 158, 11, 0.1); color: #D97706; padding: 3px 10px; border-radius: 6px; animation: pulse 2s infinite;">🏃 {{ $hoursEarly }}j lebih awal!</span>
                @endif
                @if($done && $item->is_verified)
                <span style="font-size: 10px; font-weight: 800; background: rgba(16, 185, 129, 0.1); color: #10B981; padding: 3px 10px; border-radius: 6px;">✓ VERIFIED</span>
                @endif
            </div>

            <!-- Task Name -->
            <h4 style="font-size: 16px; font-weight: 700; color: var(--text-main); margin-bottom: 4px; {{ $done ? 'text-decoration: line-through;' : '' }}">{{ $item->activity_name }}</h4>
            
            <!-- Meta -->
            <p style="font-size: 12px; color: var(--text-muted); font-weight: 600;">🤝 {{ $item->group_name }} • ⏰ {{ $item->time }}{{ $item->date != date('Y-m-d') && !isset($is_future) ? ' • 📅 '.\Carbon\Carbon::parse($item->date)->format('d M') : '' }}</p>

            <!-- Time remaining indicator -->
            @if($isBeforeDeadline && !$done)
            <div style="margin-top: 6px; display: flex; align-items: center; gap: 6px;">
                @php
                    $hoursToDeadline = now()->diffInHours($deadline, false);
                    $urgencyColor = $hoursToDeadline <= 2 ? '#EF4444' : ($hoursToDeadline <= 12 ? '#F59E0B' : '#10B981');
                @endphp
                <div style="width: 8px; height: 8px; border-radius: 50%; background: {{ $urgencyColor }}; {{ $hoursToDeadline <= 2 ? 'animation: pulse 1s infinite;' : '' }}"></div>
                <span style="font-size: 11px; font-weight: 700; color: {{ $urgencyColor }};">
                    @if($hoursToDeadline <= 2)
                        ⚡ Kurang dari 2 jam lagi!
                    @elseif($hoursToDeadline <= 12)
                        ⏳ Sisa {{ $hoursToDeadline }} jam
                    @else
                        📅 Sisa {{ round($hoursToDeadline / 24) }} hari
                    @endif
                </span>
            </div>
            @endif

            <!-- Notes -->
            @if($item->notes)
            <div style="margin-top: 8px; padding: 10px 14px; background: var(--soft-bg); border-radius: 10px; border-left: 3px solid var(--border-color);">
                <p style="font-size: 12px; color: var(--text-muted); line-height: 1.5; margin: 0;">📝 {{ $item->notes }}</p>
            </div>
            @endif
        </div>
        
        <!-- Action Buttons -->
        <div style="display: flex; align-items: center; gap: 10px; margin-left: 12px; flex-shrink: 0;">
            @if(auth()->check() && $item->user_id === auth()->id() && !$done)
            <form action="/schedules/{{ $item->id }}/toggle" method="POST" enctype="multipart/form-data" id="form-proof-{{ $item->id }}">
                @csrf
                <label for="proof-{{ $item->id }}" style="cursor: pointer; font-size: 18px; display: flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: var(--soft-bg); border: 1px solid var(--border-color); transition: 0.2s;" title="Upload Bukti"
                    onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">📸</label>
                <input type="file" name="proof_image" id="proof-{{ $item->id }}" style="display: none;" onchange="this.form.submit()">
            </form>
            @endif
            
            <form action="/schedules/{{ $item->id }}/toggle" method="POST">
                @csrf
                <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 24px; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 12px; transition: 0.2s; {{ !$done ? 'background: var(--soft-bg); border: 1px solid var(--border-color);' : '' }}" title="{{ $done ? 'Batalkan' : 'Checklist Selesai ✅' }}"
                    onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
                    {{ $done ? '✅' : '⬜' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Sub-tasks -->
    @if($item->subTasks->count() > 0)
    <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color);">
        @php
            $totalSubs = $item->subTasks->count();
            $doneSubs = $item->subTasks->where('is_completed', true)->count();
            $subPercent = $totalSubs > 0 ? round(($doneSubs / $totalSubs) * 100) : 0;
        @endphp
        
        <!-- Sub-task Progress Bar -->
        <div style="margin-bottom: 12px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <span style="font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px;">Checklist Progress</span>
                <span style="font-size: 11px; font-weight: 800; color: {{ $subPercent === 100 ? '#10B981' : 'var(--text-muted)' }};">{{ $doneSubs }}/{{ $totalSubs }} ({{ $subPercent }}%)</span>
            </div>
            <div style="width: 100%; height: 6px; background: var(--soft-bg); border-radius: 10px; overflow: hidden;">
                <div style="width: {{ $subPercent }}%; height: 100%; background: {{ $subPercent === 100 ? '#10B981' : 'var(--primary-gradient)' }}; border-radius: 10px; transition: width 0.5s ease;"></div>
            </div>
        </div>
    
        @foreach($item->subTasks as $sub)
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px; padding: 4px 0;">
            <form action="/sub-tasks/{{ $sub->id }}/toggle" method="POST">
                @csrf
                <input type="checkbox" {{ $sub->is_completed ? 'checked' : '' }} onchange="this.form.submit()" style="cursor: pointer; width: 16px; height: 16px; accent-color: #1E88E5;">
            </form>
            <span style="font-size: 13px; font-weight: 600; color: var(--text-main); {{ $sub->is_completed ? 'text-decoration: line-through; opacity: 0.5;' : '' }}">{{ $sub->title }}</span>
        </div>
        @endforeach
    </div>
    @endif
    
    @if(!auth()->check() || (auth()->check() && $item->user_id === auth()->id()) || auth()->user()->role === 'admin')
        @if(!$done)
        <form action="/schedules/{{ $item->id }}/sub-tasks" method="POST" style="margin-top: 12px; display: flex; gap: 8px;">
            @csrf
            <input type="text" name="title" placeholder="Tambah checklist..." required style="flex-grow: 1; min-height: 0; padding: 8px 14px; border-radius: 10px; font-size: 12px; font-family: inherit;" class="arctic-input">
            <button type="submit" class="btn-arctic" style="width: auto; padding: 0 14px; height: 35px; font-size: 16px; margin-top: 0;">+</button>
        </form>
        @endif
    @endif
</div>

<style>
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
</style>
