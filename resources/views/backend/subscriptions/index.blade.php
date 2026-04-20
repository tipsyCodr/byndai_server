@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">User Subscriptions</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Monitor and manage active plans for your community</p>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <form action="{{ route('admin.subscriptions.index') }}" method="GET" style="display: flex; gap: 1rem;">
                <input type="text" name="search" class="form-control" placeholder="Search User or ID..." value="{{ request('search') }}" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1.5rem; border-radius: 0.8rem; color: white; width: 300px;">
                <button type="submit" class="gradient-btn" style="padding: 0 1.5rem; border-radius: 0.8rem;">
                    <i data-lucide="search" style="width: 20px;"></i>
                </button>
            </form>
            <a href="{{ route('admin.subscriptions.create') }}" class="gradient-btn" style="padding: 0.8rem 1.5rem; border-radius: 0.8rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="plus" style="width: 20px;"></i>
                New Subscription
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="premium-glass" style="padding: 0; overflow: hidden; border-radius: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.05);">
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">User</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Current Plan</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Status</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Validity</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                <tr style="border-bottom: 1px solid var(--glass-border); transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem 2rem;">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white;">
                                {{ substr($sub->user->user_name ?? 'U', 0, 1) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $sub->user->user_name ?? 'Unknown User' }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-secondary);">ID: {{ $sub->user_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        <span style="font-weight: 600;">{{ $sub->plan->name ?? 'N/A' }}</span>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ ucfirst($sub->plan->type ?? '') }}</div>
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        @if($sub->isActive())
                            <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 0.4rem 0.8rem; border-radius: 0.5rem; font-size: 0.75rem;">ACTIVE</span>
                        @else
                            <span class="badge" style="background: rgba(239, 68, 68, 0.1); color: #f87171; padding: 0.4rem 0.8rem; border-radius: 0.5rem; font-size: 0.75rem;">EXPIRED</span>
                        @endif
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        <div style="font-size: 0.85rem; color: var(--text-primary);">Till: {{ $sub->end_date ? $sub->end_date->format('d M, Y') : 'N/A' }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $sub->start_date ? $sub->start_date->format('d M, Y') : '' }}</div>
                    </td>
                    <td style="padding: 1.5rem 2rem; text-align: right;">
                        <div style="display: flex; gap: 0.5rem; justify-content: flex-end; flex-wrap: wrap;">
                            <a href="{{ route('admin.subscriptions.show', $sub->id) }}" class="gradient-btn" style="padding: 0.6rem 1.2rem; border-radius: 0.6rem; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i data-lucide="bar-chart-3" style="width: 14px;"></i>
                                Usage
                            </a>
                            <a href="{{ route('admin.subscriptions.edit', $sub->id) }}" style="padding: 0.6rem 1rem; border-radius: 0.6rem; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(59, 130, 246, 0.2); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.3);">
                                <i data-lucide="edit-2" style="width: 14px;"></i>
                                Edit
                            </a>
                            <button onclick="deleteSubscription({{ $sub->id }}, '{{ $sub->user->user_name }}')" style="padding: 0.6rem 1rem; border-radius: 0.6rem; font-size: 0.85rem; background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <i data-lucide="trash-2" style="width: 14px;"></i>
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 4rem; text-align: center; color: var(--text-secondary);">
                        No subscriptions found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding: 2rem;">
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 2rem;">
    <div class="premium-glass" style="max-width: 450px; width: 100%; padding: 2.5rem; border-radius: 2rem; position: relative;">
        <h2 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--text-primary);">Confirm Delete</h2>
        <p id="deleteMessage" style="color: var(--text-secondary); margin-bottom: 2rem;"></p>

        <div style="display: flex; gap: 1rem;">
            <button onclick="closeDeleteModal()" style="flex: 1; padding: 0.8rem; border-radius: 0.6rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: var(--text-primary); cursor: pointer; font-weight: 600;">
                Cancel
            </button>
            <form id="deleteForm" method="POST" style="flex: 1;">
                @csrf
                @method('DELETE')
                <button type="submit" style="width: 100%; padding: 0.8rem; border-radius: 0.6rem; background: #ef4444; border: none; color: white; cursor: pointer; font-weight: 600;">
                    Delete Subscription
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function deleteSubscription(id, userName) {
        document.getElementById('deleteMessage').innerText = `Are you sure you want to delete the subscription for "${userName}"? This action cannot be undone.`;
        document.getElementById('deleteForm').action = `/admin/subscriptions/${id}`;
        document.getElementById('deleteModal').style.display = 'flex';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
</script>
@endsection
