@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Master Features</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Define the capabilities that can be assigned to subscription plans</p>
        </div>
        <button onclick="openCreateModal()" class="gradient-btn" style="padding: 1rem 2rem; border-radius: 1rem; display: flex; align-items: center; gap: 0.8rem;">
            <i data-lucide="plus-circle" style="width: 20px;"></i>
            Add New Feature
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="alert-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="premium-glass" style="padding: 0; overflow: hidden; border-radius: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.05);">
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em;">Feature Name</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em;">Usage in Plans</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($features as $feature)
                <tr style="border-bottom: 1px solid var(--glass-border); transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem 2rem;">
                        <span style="color: var(--text-primary); font-weight: 600;">{{ $feature->feature }}</span>
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        <span class="badge" style="background: rgba(99, 102, 241, 0.1); color: var(--accent-primary); padding: 0.4rem 0.8rem; border-radius: 2rem; font-size: 0.8rem;">
                            {{ $feature->planFeatures->count() }} Plans
                        </span>
                    </td>
                    <td style="padding: 1.5rem 2rem; text-align: right;">
                        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                            <button onclick="openEditModal({{ $feature->id }}, '{{ $feature->feature }}')" class="glass-icon-btn" style="background: rgba(255,255,255,0.05); border: none; padding: 0.6rem; border-radius: 0.8rem; color: var(--text-secondary); cursor: pointer;">
                                <i data-lucide="edit-3" style="width: 18px;"></i>
                            </button>
                            <form action="{{ route('admin.features.destroy', $feature->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this feature?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="glass-icon-btn" style="background: rgba(239, 68, 68, 0.1); border: none; padding: 0.6rem; border-radius: 0.8rem; color: #f87171; cursor: pointer;">
                                    <i data-lucide="trash-2" style="width: 18px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" style="padding: 4rem; text-align: center; color: var(--text-secondary);">
                        <i data-lucide="layers" style="width: 48px; height: 48px; margin-bottom: 1.5rem; opacity: 0.3;"></i>
                        <p>No features defined yet. Click "Add New Feature" to get started.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="featureModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 2rem;">
    <div class="premium-glass" style="max-width: 500px; width: 100%; padding: 2.5rem; border-radius: 2rem; position: relative;">
        <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-secondary); cursor: pointer;">
            <i data-lucide="x" style="width: 24px;"></i>
        </button>
        
        <h2 id="modalTitle" class="gradient-text" style="font-size: 1.8rem; margin-bottom: 2rem;">Add New Feature</h2>
        
        <form id="featureForm" action="{{ route('admin.features.store') }}" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 1rem; color: var(--text-secondary);">Feature Name</label>
                <input type="text" name="feature" id="featureInput" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 1rem; border-radius: 0.8rem; color: white;" placeholder="e.g. Profile QR, Premium Flyer Editor" required>
            </div>
            
            <button type="submit" class="gradient-btn" style="width: 100%; padding: 1.2rem; border-radius: 1rem; font-size: 1.1rem;">
                Save Feature
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Add New Feature';
        document.getElementById('featureForm').action = "{{ route('admin.features.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('featureInput').value = '';
        document.getElementById('featureModal').style.display = 'flex';
    }

    function openEditModal(id, name) {
        document.getElementById('modalTitle').innerText = 'Edit Feature';
        document.getElementById('featureForm').action = "/features/" + id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('featureInput').value = name;
        document.getElementById('featureModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('featureModal').style.display = 'none';
    }

    // Close on outside click
    window.onclick = function(event) {
        const modal = document.getElementById('featureModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection
