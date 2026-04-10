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
                                <a href="{{ $user->bni_card_url }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--accent-primary); text-decoration: none;">
                                    <i data-lucide="image" style="width: 16px;"></i>
                                    View
                                </a>
                            @else
                                <span style="color: var(--text-secondary);">Not uploaded</span>
                            @endif
                        </td>
                        <td style="padding: 1.5rem 1rem; width: 180px;">
                            @if($user->jci_card_url)
                                <a href="{{ $user->jci_card_url }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--accent-primary); text-decoration: none;">
                                    <i data-lucide="image" style="width: 16px;"></i>
                                    View
                                </a>
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
@endsection
