@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Event Management</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Monitor and manage all user-created events across the platform.</p>
        </div>

        <form action="{{ route('admin.events.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <select name="status" class="form-control" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1rem; border-radius: 0.8rem; color: white;">
                <option value="">All Status</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
            </select>
            <input type="text" name="search" class="form-control" placeholder="Search events..." value="{{ request('search') }}" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1.5rem; border-radius: 0.8rem; color: white; width: 250px;">
            <button type="submit" class="gradient-btn" style="padding: 0 1.5rem; border-radius: 0.8rem; display: inline-flex; align-items: center; gap: 0.5rem; height: 48px;">
                <i data-lucide="search" style="width: 20px;"></i>
                Search
            </button>
        </form>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; border-radius: 1rem; background: rgba(34, 197, 94, 0.12); color: #bbf7d0; border: 1px solid rgba(34, 197, 94, 0.25); display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="check-circle" style="width: 20px;"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="premium-glass" style="padding: 0; overflow: hidden; border-radius: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.05);">
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Event Info</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Industry</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Created By</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Schedule</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Type</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Status</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                    <tr style="border-bottom: 1px solid var(--glass-border); transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1.5rem 1rem;">
                            <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">{{ $event->title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-secondary); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $event->description }}
                            </div>
                        </td>
                        <td style="padding: 1.5rem 1rem;">
                            <div style="font-size: 0.9rem; color: var(--text-primary);">{{ $event->Industry->title ?? 'N/A' }}</div>
                        </td>
                        <td style="padding: 1.5rem 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; font-size: 0.8rem;">
                                    {{ strtoupper(substr($event->createdByUser->user_name ?? ($event->createdByUser->masterUser->fname ?? 'U'), 0, 1)) }}
                                </div>
                                <div style="font-size: 0.9rem;">
                                    <div style="color: var(--text-primary);">{{ $event->createdByUser->user_name ?? 'Unknown' }}</div>
                                    <div style="color: var(--text-secondary); font-size: 0.75rem;">{{ $event->createdByUser->masterUser->fname ?? '' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1.5rem 1rem;">
                            <div style="font-size: 0.9rem; color: var(--text-primary);">
                                <i data-lucide="calendar" style="width: 14px; margin-right: 0.25rem;"></i>
                                {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y') }}
                            </div>
                            @if($event->end_date)
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">to {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}</div>
                            @endif
                            @if($event->timing)
                                <div style="font-size: 0.8rem; color: var(--text-secondary);">
                                    <i data-lucide="clock" style="width: 12px; margin-right: 0.25rem;"></i>
                                    {{ $event->timing }}
                                </div>
                            @endif
                        </td>
                        <td style="padding: 1.5rem 1rem;">
                            <span style="font-size: 0.8rem; padding: 0.25rem 0.6rem; border-radius: 0.5rem; background: rgba(99, 102, 241, 0.1); color: #818cf8; border: 1px solid rgba(99, 102, 241, 0.2);">
                                {{ $event->is_online ? 'Online' : 'In-Person' }}
                            </span>
                            @if($event->is_audience)
                                <div style="font-size: 0.75rem; color: var(--text-secondary); margin-top: 0.4rem;">
                                    <i data-lucide="users" style="width: 12px;"></i> Audience Restricted
                                </div>
                            @endif
                        </td>
                        <td style="padding: 1.5rem 1rem;">
                            <form action="{{ route('admin.events.toggle-status', $event->id) }}" method="POST">
                                @csrf
                                <button type="submit" style="background: none; border: none; cursor: pointer; padding: 0;">
                                    @if($event->status == 1)
                                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; padding: 0.25rem 0.6rem; border-radius: 0.5rem; background: rgba(34, 197, 94, 0.1); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #4ade80;"></span>
                                            Active
                                        </span>
                                    @else
                                        <span style="display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; padding: 0.25rem 0.6rem; border-radius: 0.5rem; background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2);">
                                            <span style="width: 6px; height: 6px; border-radius: 50%; background: #f87171;"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </button>
                            </form>
                        </td>
                        <td style="padding: 1.5rem 1rem; text-align: right;">
                            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                                <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this event? This will also remove all associated images.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; padding: 0.6rem; border-radius: 0.75rem; cursor: pointer; transition: all 0.3s;" title="Delete Event">
                                        <i data-lucide="trash-2" style="width: 18px;"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 4rem; text-align: center; color: var(--text-secondary);">
                            <i data-lucide="calendar-off" style="width: 48px; height: 48px; margin-bottom: 1rem; opacity: 0.2;"></i>
                            <p>No events found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($events->hasPages())
            <div style="padding: 1.5rem; border-top: 1px solid var(--glass-border);">
                {{ $events->links() }}
            </div>
        @endif
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
