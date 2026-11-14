@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">AI Flyer Usage Monitoring</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Track image generation volume, success rates and user activity</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.ai-flyers.settings') }}" class="btn" style="background: rgba(255,255,255,0.06); color: var(--text-primary); padding: 0.8rem 1.5rem; border-radius: 0.8rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <i data-lucide="settings" style="width: 18px;"></i>
                Settings
            </a>
            <a href="{{ route('admin.ai-flyers.occasions') }}" class="btn" style="background: rgba(255,255,255,0.06); color: var(--text-primary); padding: 0.8rem 1.5rem; border-radius: 0.8rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <i data-lucide="calendar" style="width: 18px;"></i>
                Occasions
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
        <div class="premium-glass" style="padding: 1.8rem; border-radius: 1.2rem;">
            <div style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Total Generated</div>
            <div style="color: #60a5fa; font-size: 2.2rem; font-weight: 800;">{{ number_format($stats['total_generations']) }}</div>
        </div>

        <div class="premium-glass" style="padding: 1.8rem; border-radius: 1.2rem;">
            <div style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Today</div>
            <div style="color: #4ade80; font-size: 2.2rem; font-weight: 800;">{{ number_format($stats['today_generations']) }}</div>
        </div>

        <div class="premium-glass" style="padding: 1.8rem; border-radius: 1.2rem;">
            <div style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">This Month</div>
            <div style="color: #c084fc; font-size: 2.2rem; font-weight: 800;">{{ number_format($stats['month_generations']) }}</div>
        </div>

        <div class="premium-glass" style="padding: 1.8rem; border-radius: 1.2rem;">
            <div style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Successful</div>
            <div style="color: #34d399; font-size: 2.2rem; font-weight: 800;">{{ number_format($stats['successful_generations']) }}</div>
        </div>

        <div class="premium-glass" style="padding: 1.8rem; border-radius: 1.2rem;">
            <div style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Failed</div>
            <div style="color: #f87171; font-size: 2.2rem; font-weight: 800;">{{ number_format($stats['failed_generations']) }}</div>
        </div>

        <div class="premium-glass" style="padding: 1.8rem; border-radius: 1.2rem;">
            <div style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; margin-bottom: 0.5rem;">Active Users</div>
            <div style="color: #facc15; font-size: 2.2rem; font-weight: 800;">{{ number_format($stats['active_users']) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="premium-glass" style="padding: 1.2rem 1.8rem; border-radius: 1.2rem; margin-bottom: 1.8rem;">
        <form method="GET" action="{{ route('admin.ai-flyers.monitoring') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="width: 160px;">
                <select name="status" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.7rem 1rem; border-radius: 0.8rem; color: #fff;">
                    <option value="">All Statuses</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div style="width: 160px;">
                <select name="type" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.7rem 1rem; border-radius: 0.8rem; color: #fff;">
                    <option value="">All Types</option>
                    <option value="occasion" {{ request('type') == 'occasion' ? 'selected' : '' }}>Occasion</option>
                    <option value="special" {{ request('type') == 'special' ? 'selected' : '' }}>Special</option>
                    <option value="custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Custom</option>
                </select>
            </div>
            <div style="width: 180px;">
                <input type="date" name="date" value="{{ request('date') }}" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.7rem 1rem; border-radius: 0.8rem; color: #fff;">
            </div>
            <button type="submit" class="btn" style="background: var(--accent-primary); color: #fff; padding: 0.7rem 1.4rem; border-radius: 0.8rem; border: none; font-weight: 600; cursor: pointer;">Filter</button>
            @if(request('status') || request('type') || request('date'))
                <a href="{{ route('admin.ai-flyers.monitoring') }}" style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Logs Table -->
    <div class="premium-glass" style="padding: 0; overflow: hidden; border-radius: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.05);">
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">ID & Preview</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">User</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Category / Title</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Style & Ratio</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($generations as $g)
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 1.2rem 1.8rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            @if($g->image_url)
                                <img src="{{ $g->image_url }}" alt="Flyer" style="width: 48px; height: 48px; border-radius: 8px; object-fit: cover; border: 1px solid var(--glass-border);">
                            @else
                                <div style="width: 48px; height: 48px; border-radius: 8px; background: rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; color: var(--text-secondary);">
                                    <i data-lucide="image" style="width: 20px;"></i>
                                </div>
                            @endif
                            <span style="color: var(--text-secondary); font-weight: 600;">#{{ $g->id }}</span>
                        </div>
                    </td>
                    <td style="padding: 1.2rem 1.8rem;">
                        <div style="color: var(--text-primary); font-weight: 600;">{{ $g->user?->name ?: 'User #' . $g->user_id }}</div>
                        <div style="color: var(--text-secondary); font-size: 0.85rem;">{{ $g->user?->mobile ?: $g->user?->email }}</div>
                    </td>
                    <td style="padding: 1.2rem 1.8rem;">
                        <div style="color: var(--text-primary); font-weight: 600;">{{ $g->category }}</div>
                        <span style="text-transform: capitalize; font-size: 0.8rem; color: #a5b4fc;">{{ $g->type }}</span>
                    </td>
                    <td style="padding: 1.2rem 1.8rem;">
                        <span style="color: #cbd5e1; font-size: 0.85rem;">{{ $g->design_style }} ({{ $g->aspect_ratio }})</span>
                    </td>
                    <td style="padding: 1.2rem 1.8rem;">
                        @php
                            $badgeColor = match($g->status) {
                                'completed' => ['bg' => 'rgba(34, 197, 94, 0.15)', 'text' => '#4ade80'],
                                'processing' => ['bg' => 'rgba(59, 130, 246, 0.15)', 'text' => '#60a5fa'],
                                'pending' => ['bg' => 'rgba(234, 179, 8, 0.15)', 'text' => '#facc15'],
                                default => ['bg' => 'rgba(239, 68, 68, 0.15)', 'text' => '#f87171'],
                            };
                        @endphp
                        <span style="padding: 0.3rem 0.7rem; border-radius: 1rem; font-size: 0.8rem; background: {{ $badgeColor['bg'] }}; color: {{ $badgeColor['text'] }}; text-transform: capitalize;">
                            {{ $g->status }}
                        </span>
                        @if($g->error_message)
                            <div style="color: #f87171; font-size: 0.75rem; max-width: 180px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-top: 0.3rem;" title="{{ $g->error_message }}">
                                {{ $g->error_message }}
                            </div>
                        @endif
                    </td>
                    <td style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-size: 0.85rem;">
                        {{ $g->created_at->format('d M Y, h:i A') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-secondary);">No flyer generations logged yet.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding: 1.5rem 2rem;">
            {{ $generations->links() }}
        </div>
    </div>
</div>
@endsection

