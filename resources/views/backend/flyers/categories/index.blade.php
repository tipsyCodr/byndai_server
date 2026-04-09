@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Flyer Categories</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Manage festive occasions and miscellaneous categories for flyers</p>
        </div>
        <button onclick="openCreateModal()" class="gradient-btn" style="padding: 1rem 2rem; border-radius: 1rem; display: flex; align-items: center; gap: 0.8rem;">
            <i data-lucide="plus-circle" style="width: 20px;"></i>
            Add Category
        </button>
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
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Category Name</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Type</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Date / Status</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase;">Flyers</th>
                    <th style="padding: 1.5rem 2rem; color: var(--text-secondary); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr style="border-bottom: 1px solid var(--glass-border); transition: background 0.3s;" onmouseover="this.style.background='rgba(255,255,255,0.02)'" onmouseout="this.style.background='transparent'">
                    <td style="padding: 1.5rem 2rem;">
                        <span style="color: var(--text-primary); font-weight: 600;">{{ $category->name }}</span>
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        <span class="badge" style="background: {{ $category->type == 'festive' ? 'rgba(99, 102, 241, 0.1)' : 'rgba(34, 197, 94, 0.1)' }}; color: {{ $category->type == 'festive' ? 'var(--accent-primary)' : '#4ade80' }}; padding: 0.4rem 0.8rem; border-radius: 2rem; font-size: 0.8rem; text-transform: capitalize;">
                            {{ $category->type }}
                        </span>
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        @if($category->type == 'festive')
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">
                                <i data-lucide="calendar" style="width: 14px; margin-right: 0.3rem;"></i>
                                {{ $category->date }}
                            </span>
                        @else
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">Permanent</span>
                        @endif
                    </td>
                    <td style="padding: 1.5rem 2rem;">
                        <a href="{{ route('admin.flyers.index', ['category_id' => $category->id]) }}" style="color: var(--accent-primary); text-decoration: none; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="image" style="width: 16px;"></i>
                            {{ $category->flyers_count }} Flyers
                        </a>
                    </td>
                    <td style="padding: 1.5rem 2rem; text-align: right;">
                        <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                            <button onclick='openEditModal({!! $category->toJson() !!})' class="glass-icon-btn" style="background: rgba(255,255,255,0.05); border: none; padding: 0.6rem; border-radius: 0.8rem; color: var(--text-secondary); cursor: pointer;">
                                <i data-lucide="edit-3" style="width: 18px;"></i>
                            </button>
                            <form action="{{ route('admin.flyer-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Deleting category will also delete all its flyers. Continue?')">
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
                    <td colspan="5" style="padding: 4rem; text-align: center; color: var(--text-secondary);">
                        <i data-lucide="layers" style="width: 48px; height: 48px; margin-bottom: 1.5rem; opacity: 0.3;"></i>
                        <p>No categories found. Start by adding one!</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 2rem;">
    <div class="premium-glass" style="max-width: 500px; width: 100%; padding: 2.5rem; border-radius: 2rem; position: relative;">
        <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-secondary); cursor: pointer;">
            <i data-lucide="x" style="width: 24px;"></i>
        </button>
        
        <h2 id="modalTitle" class="gradient-text" style="font-size: 1.8rem; margin-bottom: 2rem;">Add Category</h2>
        
        <form id="categoryForm" action="{{ route('admin.flyer-categories.store') }}" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Category Name</label>
                <input type="text" name="name" id="nameInput" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 1rem; border-radius: 0.8rem; color: white;" placeholder="e.g. Independence Day, Motivation" required>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Type</label>
                <select name="type" id="typeInput" onchange="toggleDateField()" class="form-control" style="width: 100%; background: rgba(25, 33, 50, 0.9); border: 1px solid var(--glass-border); padding: 1rem; border-radius: 0.8rem; color: white;" required>
                    <option value="festive" selected>Festive / Occasional</option>
                    <option value="general">General (Miscellaneous)</option>
                </select>
            </div>

            <div id="dateGroup" class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Occasion Date</label>
                <input type="date" name="date_picker" id="datePicker" onchange="updateDateValue()" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 1rem; border-radius: 0.8rem; color: white;">
                <input type="hidden" name="date" id="dateInput">
                <small style="color: var(--text-secondary); margin-top: 0.5rem; display: block;">Select the date for the occasion (stored as recurring annually).</small>
            </div>
            
            <button type="submit" class="gradient-btn" style="width: 100%; padding: 1.2rem; border-radius: 1rem; font-size: 1.1rem;">
                Save Category
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function updateDateValue() {
        const picker = document.getElementById('datePicker');
        if (picker.value) {
            const date = new Date(picker.value);
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const day = date.getDate().toString().padStart(2, '0');
            document.getElementById('dateInput').value = `${month}-${day}`;
        } else {
            document.getElementById('dateInput').value = '';
        }
    }

    function toggleDateField() {
        const type = document.getElementById('typeInput').value;
        document.getElementById('dateGroup').style.display = type === 'festive' ? 'block' : 'none';
        if(type === 'general') {
            document.getElementById('datePicker').value = '';
            document.getElementById('dateInput').value = '';
        }
    }

    function openCreateModal() {
        document.getElementById('modalTitle').innerText = 'Add Category';
        document.getElementById('categoryForm').action = "{{ route('admin.flyer-categories.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('nameInput').value = '';
        document.getElementById('typeInput').value = 'festive';
        document.getElementById('datePicker').value = '';
        document.getElementById('dateInput').value = '';
        toggleDateField();
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function openEditModal(category) {
        document.getElementById('modalTitle').innerText = 'Edit Category';
        document.getElementById('categoryForm').action = "/admin/flyer-categories/" + category.id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('nameInput').value = category.name;
        document.getElementById('typeInput').value = category.type;
        
        if (category.date) {
            const year = new Date().getFullYear();
            document.getElementById('datePicker').value = `${year}-${category.date}`;
            document.getElementById('dateInput').value = category.date;
        } else {
            document.getElementById('datePicker').value = '';
            document.getElementById('dateInput').value = '';
        }
        
        toggleDateField();
        document.getElementById('categoryModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('categoryModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('categoryModal');
        if (event.target == modal) {
            closeModal();
        }
    }
</script>
@endsection
