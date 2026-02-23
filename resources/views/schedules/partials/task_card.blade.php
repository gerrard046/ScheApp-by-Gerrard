@php $done = $item->is_completed; @endphp
<div class="zen-card card-item" data-text="{{ strtolower($item->activity_name.' '.$item->group_name) }}" style="padding: 20px; opacity: {{ $done ? '0.6' : '1' }}; border-left: 6px solid {{ $item->priority === 'high' ? '#EF4444' : '#1E88E5' }};">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <div style="display: flex; gap: 10px; align-items: center; margin-bottom: 10px;">
                <span style="font-size: 10px; font-weight: 800; background: var(--soft-bg); color: var(--text-muted); padding: 4px 10px; border-radius: 5px;">{{ $item->category }}</span>
                @if($item->priority === 'high')
                <span style="font-size: 10px; font-weight: 800; background: #FFEFEE; color: #EF4444; padding: 4px 10px; border-radius: 5px;">URGENT</span>
                @endif
                @if(isset($is_future) && $is_future)
                <span style="font-size: 10px; font-weight: 800; background: #E0F2FE; color: #0369A1; padding: 4px 10px; border-radius: 5px;">FUTURE: {{ \Carbon\Carbon::parse($item->date)->format('d M') }}</span>
                @endif
                @if($item->parentTask)
                <span style="font-size: 10px; font-weight: 800; background: #FEF3C7; color: #B45309; padding: 4px 10px; border-radius: 5px;">⛓️ Prasyarat: {{ $item->parentTask->activity_name }}</span>
                @endif
            </div>
            <h4 style="font-size: 18px; font-weight: 700; color: var(--text-main); margin-bottom: 5px; {{ $done ? 'text-decoration: line-through;' : '' }}">{{ $item->activity_name }}</h4>
            <p style="font-size: 13px; color: var(--text-muted); font-weight: 600;">🤝 {{ $item->group_name }} • ⏰ {{ $item->time }}</p>
        </div>
        
        <div style="display: flex; align-items: center; gap: 15px;">
            @if(auth()->check() && $item->user_id === auth()->id() && !$done)
            <form action="/schedules/{{ $item->id }}/toggle" method="POST" enctype="multipart/form-data" id="form-proof-{{ $item->id }}">
                @csrf
                <label for="proof-{{ $item->id }}" style="cursor: pointer; font-size: 20px;" title="Upload Bukti Selesai">📸</label>
                <input type="file" name="proof_image" id="proof-{{ $item->id }}" style="display: none;" onchange="this.form.submit()">
            </form>
            @endif
            
            <form action="/schedules/{{ $item->id }}/toggle" method="POST">
                @csrf
                <button type="submit" style="background: none; border: none; cursor: pointer; font-size: 28px;" title="{{ $done ? 'Batalkan Selesai' : 'Tandai Selesai' }}">
                    {{ $done ? '✅' : '⬜' }}
                </button>
            </form>
        </div>
    </div>

    <!-- Sub-tasks Section -->
    @if($item->subTasks->count() > 0)
    <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
        @foreach($item->subTasks as $sub)
        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
            <form action="/sub-tasks/{{ $sub->id }}/toggle" method="POST">
                @csrf
                <input type="checkbox" {{ $sub->is_completed ? 'checked' : '' }} onchange="this.form.submit()" style="cursor: pointer;">
            </form>
            <span style="font-size: 13px; font-weight: 600; color: var(--text-main); {{ $sub->is_completed ? 'text-decoration: line-through; opacity: 0.5;' : '' }}">{{ $sub->title }}</span>
        </div>
        @endforeach
    </div>
    @endif
    
    @if(!auth()->check() || (auth()->check() && $item->user_id === auth()->id()) || auth()->user()->role === 'admin')
        @if(!$done)
        <form action="/schedules/{{ $item->id }}/sub-tasks" method="POST" style="margin-top: 15px; display: flex; gap: 10px;">
            @csrf
            <input type="text" name="title" placeholder="Checklist baru..." required style="flex-grow: 1; min-height: 0; padding: 8px 15px; border-radius: 10px; font-size: 12px;" class="arctic-input">
            <button type="submit" class="btn-arctic" style="width: auto; padding: 0 15px; height: 35px; font-size: 18px; margin-top: 0;">+</button>
        </form>
        @endif
    @endif
</div>
