@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Occasions Calendar</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Manage festival and observance dates used for AI Today's Occasion flyer creation</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.ai-flyers.settings') }}" class="btn" style="background: rgba(255,255,255,0.06); color: var(--text-primary); padding: 0.8rem 1.5rem; border-radius: 0.8rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <i data-lucide="settings" style="width: 18px;"></i>
                AI Settings
            </a>
            <button onclick="openCreateOccasionModal()" class="gradient-btn" style="padding: 0.8rem 1.8rem; border-radius: 0.8rem; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.6rem;">
                <i data-lucide="plus-circle" style="width: 20px;"></i>
                Add Occasion
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters -->
    <div class="premium-glass" style="padding: 1.2rem 1.8rem; border-radius: 1.2rem; margin-bottom: 1.8rem;">
        <form method="GET" action="{{ route('admin.ai-flyers.occasions') }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search occasion name or region..." style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.7rem 1rem; border-radius: 0.8rem; color: #fff;">
            </div>
            <div style="width: 180px;">
                <select name="month" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.7rem 1rem; border-radius: 0.8rem; color: #fff;">
                    <option value="">All Months</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <button type="submit" class="btn" style="background: var(--accent-primary); color: #fff; padding: 0.7rem 1.4rem; border-radius: 0.8rem; border: none; font-weight: 600; cursor: pointer;">Filter</button>
            @if(request('search') || request('month'))
                <a href="{{ route('admin.ai-flyers.occasions') }}" style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem;">Clear</a>
            @endif
        </form>
    </div>

    <!-- Occasions Table -->
    <div class="premium-glass" style="padding: 0; overflow: hidden; border-radius: 1.5rem;">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: rgba(255, 255, 255, 0.05);">
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Date</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Occasion Name</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Region</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Language</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Recurring</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Status</th>
                    <th style="padding: 1.2rem 1.8rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($occasions as $item)
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 1.2rem 1.8rem;">
                        <span style="color: #60a5fa; font-weight: 700; font-size: 1rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i data-lucide="calendar" style="width: 15px;"></i>
                            {{ $item->date }}
                        </span>
                    </td>
                    <td style="padding: 1.2rem 1.8rem;">
                        <div style="font-weight: 700; color: var(--text-primary); font-size: 1.05rem;">{{ $item->name }}</div>
                        <div style="color: var(--text-secondary); font-size: 0.85rem; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $item->description }}
                        </div>
                    </td>
                    <td style="padding: 1.2rem 1.8rem;">
                        <span style="background: rgba(255,255,255,0.06); padding: 0.3rem 0.7rem; border-radius: 1rem; font-size: 0.8rem; color: #cbd5e1;">
                            {{ $item->region }}
                        </span>
                    </td>
                    <td style="padding: 1.2rem 1.8rem;">
                        <span style="text-transform: capitalize; color: #cbd5e1; font-size: 0.85rem;">{{ $item->language }}</span>
                    </td>
                    <td style="padding: 1.2rem 1.8rem;">
                        @if($item->year)
                            <span style="color: #facc15; font-size: 0.85rem;">Year {{ $item->year }} only</span>
                        @else
                            <span style="color: #4ade80; font-size: 0.85rem;">Every Year</span>
                        @endif
                    </td>
                    <td style="padding: 1.2rem 1.8rem;">
                        <span style="padding: 0.3rem 0.7rem; border-radius: 1rem; font-size: 0.8rem; background: {{ $item->is_active ? 'rgba(34, 197, 94, 0.15)' : 'rgba(239, 68, 68, 0.15)' }}; color: {{ $item->is_active ? '#4ade80' : '#f87171' }};">
                            {{ $item->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td style="padding: 1.2rem 1.8rem; text-align: right;">
                        <div style="display: flex; gap: 0.6rem; justify-content: flex-end;">
                            <button onclick='openEditOccasionModal(@json($item))' style="background: rgba(255,255,255,0.06); border: none; color: #cbd5e1; padding: 0.5rem; border-radius: 0.5rem; cursor: pointer;">
                                <i data-lucide="edit-3" style="width: 16px;"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.ai-flyers.occasions.destroy', $item->id) }}" onsubmit="return confirm('Delete this occasion?');" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: rgba(239, 68, 68, 0.15); border: none; color: #f87171; padding: 0.5rem; border-radius: 0.5rem; cursor: pointer;">
                                    <i data-lucide="trash-2" style="width: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="padding: 3rem; text-align: center; color: var(--text-secondary);">No occasions found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding: 1.5rem 2rem;">
            {{ $occasions->links() }}
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div id="occasionModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(8px); z-index: 999; align-items: center; justify-content: center;">
    <div class="premium-glass" style="width: 90%; max-width: 600px; padding: 2.5rem; border-radius: 1.5rem; max-height: 90vh; overflow-y: auto;">
        <h2 id="modalTitle" style="color: #fff; font-size: 1.6rem; font-weight: 700; margin-bottom: 1.5rem;">Add New Occasion</h2>
        <form id="occasionForm" method="POST" action="{{ route('admin.ai-flyers.occasions.store') }}">
            @csrf
            <input type="hidden" name="_method" id="formMethod" value="POST">

            <div style="margin-bottom: 1.2rem;">
                <label style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.4rem;">Occasion Name *</label>
                <input type="text" name="name" id="occName" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1rem; border-radius: 0.8rem; color: #fff;" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.4rem;">Date (MM-DD) *</label>
                    <input type="text" name="date" id="occDate" placeholder="e.g. 11-14" pattern="\d{2}-\d{2}" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1rem; border-radius: 0.8rem; color: #fff;" required>
                </div>
                <div>
                    <label style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.4rem;">Year (leave empty if yearly)</label>
                    <input type="number" name="year" id="occYear" placeholder="e.g. 2026 or leave blank" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1rem; border-radius: 0.8rem; color: #fff;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.4rem;">Region *</label>
                    <input type="text" name="region" id="occRegion" value="National" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1rem; border-radius: 0.8rem; color: #fff;" required>
                </div>
                <div>
                    <label style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.4rem;">Language</label>
                    <select name="language" id="occLang" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1rem; border-radius: 0.8rem; color: #fff;">
                        <option value="bilingual">Bilingual (Hindi + English)</option>
                        <option value="hi">Hindi</option>
                        <option value="en">English</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 1.2rem;">
                <label style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.4rem;">Description</label>
                <textarea name="description" id="occDesc" rows="2" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1rem; border-radius: 0.8rem; color: #fff;"></textarea>
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label style="color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 0.4rem;">Status</label>
                <select name="is_active" id="occActive" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem 1rem; border-radius: 0.8rem; color: #fff;">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <button type="button" onclick="closeOccasionModal()" class="btn" style="background: rgba(255,255,255,0.08); color: #fff; padding: 0.8rem 1.6rem; border-radius: 0.8rem; border: none; cursor: pointer;">Cancel</button>
                <button type="submit" class="gradient-btn" style="padding: 0.8rem 2rem; border-radius: 0.8rem; border: none; font-weight: 700; cursor: pointer;">Save Occasion</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCreateOccasionModal() {
    document.getElementById('modalTitle').innerText = 'Add New Occasion';
    document.getElementById('occasionForm').action = "{{ route('admin.ai-flyers.occasions.store') }}";
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('occName').value = '';
    document.getElementById('occDate').value = '';
    document.getElementById('occYear').value = '';
    document.getElementById('occRegion').value = 'National';
    document.getElementById('occLang').value = 'bilingual';
    document.getElementById('occDesc').value = '';
    document.getElementById('occActive').value = '1';
    document.getElementById('occasionModal').style.display = 'flex';
}

function openEditOccasionModal(item) {
    document.getElementById('modalTitle').innerText = 'Edit Occasion';
    document.getElementById('occasionForm').action = "/ai-flyers/occasions/" + item.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('occName').value = item.name || '';
    document.getElementById('occDate').value = item.date || '';
    document.getElementById('occYear').value = item.year || '';
    document.getElementById('occRegion').value = item.region || 'National';
    document.getElementById('occLang').value = item.language || 'bilingual';
    document.getElementById('occDesc').value = item.description || '';
    document.getElementById('occActive').value = item.is_active ? '1' : '0';
    document.getElementById('occasionModal').style.display = 'flex';
}

function closeOccasionModal() {
    document.getElementById('occasionModal').style.display = 'none';
}
</script>
@endsection

