@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; flex-wrap: wrap;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Users Management</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Review uploaded ID cards and approve BNI/JCI badges for your users.</p>
        </div>

        <form action="{{ route('admin.users.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email or ID..." value="{{ request('search') }}" style="background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1.5rem; border-radius: 0.8rem; color: white; width: 300px;">
            <button type="submit" class="gradient-btn" style="padding: 0 1.5rem; border-radius: 0.8rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="search" style="width: 20px;"></i>
                Search
            </button>
        </form>
    </div>

    @if(session('success'))
        <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; border-radius: 1rem; background: rgba(34, 197, 94, 0.12); color: #bbf7d0; border: 1px solid rgba(34, 197, 94, 0.25);">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="margin-bottom: 1.5rem; padding: 1rem 1.25rem; border-radius: 1rem; background: rgba(239, 68, 68, 0.12); color: #fecaca; border: 1px solid rgba(239, 68, 68, 0.25);">
            {{ session('error') }}
        </div>
    @endif

    <div class="premium-glass" style="padding: 0; overflow: hidden; border-radius: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.05);">
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">User</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Email</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">BNI ID Card</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">JCI ID Card</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase;">Badges</th>
                    <th style="padding: 1.5rem 1rem; color: var(--text-secondary); font-size: 0.85rem; text-transform: uppercase; text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr style="border-bottom: 1px solid var(--glass-border); transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                        <td style="padding: 1.5rem 1rem; width: 240px;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-gradient); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white;">
                                    {{ strtoupper(substr($user->user_name ?? ($user->masterUser->fname ?? 'U'), 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 600; color: var(--text-primary);">{{ $user->user_name ?? 'Unknown' }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">ID: {{ $user->id }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-secondary);">{{ $user->masterUser->fname ?? 'No details' }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="padding: 1.5rem 1rem; width: 180px;">
                            <div style="font-weight: 600; color: var(--text-primary);">{{ $user->email ?? '-' }}</div>
                        </td>
                        <td style="padding: 1.5rem 1rem; width: 180px;">
                            @if($user->bni_card_url)
                                <button onclick="openImageModal('{{ $user->bni_card_url }}', 'BNI ID Card - {{ $user->user_name }}')" style="background: none; border: none; color: var(--accent-primary); cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-size: inherit;">
                                    <i data-lucide="image" style="width: 16px;"></i>
                                    View
                                </button>
                            @else
                                <span style="color: var(--text-secondary);">Not uploaded</span>
                            @endif
                        </td>
                        <td style="padding: 1.5rem 1rem; width: 180px;">
                            @if($user->jci_card_url)
                                <button onclick="openImageModal('{{ $user->jci_card_url }}', 'JCI ID Card - {{ $user->user_name }}')" style="background: none; border: none; color: var(--accent-primary); cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-size: inherit;">
                                    <i data-lucide="image" style="width: 16px;"></i>
                                    View
                                </button>
                            @else
                                <span style="color: var(--text-secondary);">Not uploaded</span>
                            @endif
                        </td>
                        <td style="padding: 1.5rem 1rem; width: 190px;">
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" style="display: flex; flex-direction: column; gap: 0.75rem;">
                                @csrf
                                @method('PUT')
                                <label style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; color: var(--text-primary);">
                                    <input type="checkbox" name="bni_member" value="1" {{ optional($user->masterUser)->bni_member ? 'checked' : '' }}>
                                    Approve BNI
                                </label>
                                <label style="display: flex; align-items: center; gap: 0.75rem; font-size: 0.95rem; color: var(--text-primary);">
                                    <input type="checkbox" name="jci_member" value="1" {{ optional($user->masterUser)->jci_member ? 'checked' : '' }}>
                                    Approve JCI
                                </label>
                        </td>
                        <td style="padding: 1.5rem 1rem; text-align: right; width: 140px;">
                                <button type="submit" class="gradient-btn" style="font-size: 0.85rem; padding: 0.65rem 1rem; width: 100%;">
                                    Save
                                </button>
                            </form>
                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This will permanently remove all their data, subscriptions, links, and events.')" style="margin-top: 0.75rem;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #f87171; width: 100%; border-radius: 0.8rem; padding: 0.65rem; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                                    <i data-lucide="user-minus" style="width: 16px; margin-right: 0.5rem;"></i>
                                    Delete User
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 4rem; text-align: center; color: var(--text-secondary);">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div style="padding: 2rem;">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div id="imageModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 10000; align-items: center; justify-content: center;">
    <div style="position: relative; max-width: 90vw; max-height: 90vh; background: var(--bg-primary); border-radius: 1rem; padding: 1rem; box-shadow: 0 20px 60px rgba(0,0,0,0.5);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 id="modalTitle" style="color: var(--text-primary); margin: 0; font-size: 1.25rem;"></h3>
            <button onclick="closeImageModal()" style="background: none; border: none; color: var(--text-secondary); cursor: pointer; font-size: 1.5rem; padding: 0.25rem;">
                <i data-lucide="x" style="width: 24px;"></i>
            </button>
        </div>
        <div style="text-align: center;">
            <img id="modalImage" src="" alt="ID Card" style="max-width: 100%; max-height: 70vh; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.3);">
        </div>
    </div>
</div>

<script>
lucide.createIcons();

function openImageModal(imageUrl, title) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('imageModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.body.style.overflow = 'auto';
    document.getElementById('modalImage').src = '';
}

// Close modal when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && document.getElementById('imageModal').style.display === 'flex') {
        closeImageModal();
    }
});
</script>
@endsection
