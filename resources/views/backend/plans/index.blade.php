@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Subscription Plans</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Manage pricing tiers and quotas for your users</p>
        </div>
        <button onclick="openPlanModal()" class="gradient-btn" style="padding: 1rem 2rem; border-radius: 1rem; display: flex; align-items: center; gap: 0.8rem;">
            <i data-lucide="plus-circle" style="width: 20px;"></i>
            Create New Plan
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        @foreach($plans as $plan)
        <div class="premium-glass" style="padding: 2.5rem; border-radius: 2rem; position: relative; {{ $plan->status == 0 ? 'opacity: 0.6; filter: grayscale(0.5);' : '' }}">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
                <span class="badge" style="background: rgba(255, 255, 255, 0.05); color: var(--text-secondary); padding: 0.3rem 0.8rem; border-radius: 0.5rem; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">
                    {{ $plan->type }}
                </span>
                @if($plan->is_free)
                    <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 0.3rem 0.8rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700;">FREE</span>
                @endif
            </div>
            
            <h2 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $plan->name }}</h2>
            <div style="margin-bottom: 2rem;">
                <span style="font-size: 2.2rem; font-weight: 800;" class="gradient-text">{{ $plan->currency }}{{ number_format($plan->price, 0) }}</span>
                <span style="color: var(--text-secondary);">/ {{ $plan->type }}</span>
            </div>

            <div style="margin-bottom: 2.5rem;">
                <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 1rem; letter-spacing: 0.05em;">Plan Features</h4>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @forelse($plan->features as $pf)
                    <li style="display: flex; align-items: center; gap: 0.8rem; margin-bottom: 0.8rem; color: var(--text-primary);">
                        <i data-lucide="check" style="width: 16px; color: #4ade80;"></i>
                        <span>{{ $pf->feature->feature }}: <strong>{{ $pf->value }} {{ $pf->unit }}</strong></span>
                    </li>
                    @empty
                    <li style="color: var(--text-secondary); font-size: 0.9rem; font-style: italic;">No features assigned yet.</li>
                    @endforelse
                </ul>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <a href="{{ route('admin.plans.show', $plan->id) }}" class="nav-link" style="text-align: center; background: rgba(255,255,255,0.05); padding: 0.8rem; border-radius: 0.8rem; color: var(--text-primary); text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i data-lucide="settings" style="width: 16px;"></i>
                    Configure
                </a>
                <button onclick='openPlanModal(@json($plan))' class="nav-link" style="background: rgba(99, 102, 241, 0.1); border: none; padding: 0.8rem; border-radius: 0.8rem; color: var(--accent-primary); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i data-lucide="edit-3" style="width: 16px;"></i>
                    Edit Plan
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Plan Modal -->
<div id="planModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 2rem;">
    <div class="premium-glass" style="max-width: 600px; width: 100%; padding: 2.5rem; border-radius: 2rem; position: relative;">
        <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-secondary); cursor: pointer;">
            <i data-lucide="x" style="width: 24px;"></i>
        </button>
        
        <h2 id="modalTitle" class="gradient-text" style="font-size: 1.8rem; margin-bottom: 2rem;">Create New Plan</h2>
        
        <form id="planForm" action="{{ route('admin.plans.store') }}" method="POST">
            @csrf
            <div id="methodField"></div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Plan Name</label>
                    <input type="text" name="name" id="nameInput" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;" placeholder="e.g. Pro Plan" required>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Type</label>
                    <select name="type" id="typeInput" class="form-control" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;" required>
                        <option value="monthly">Monthly</option>
                        <option value="yearly">Yearly</option>
                        <option value="lifetime">Lifetime</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Price</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <input type="text" value="₹" style="width: 40px; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: var(--text-secondary); text-align: center;" readonly>
                        <input type="number" name="price" id="priceInput" class="form-control" style="flex: 1; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;" placeholder="0" required>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Currency</label>
                    <input type="text" name="currency" id="currencyInput" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;" value="₹">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem;">
                <div class="form-group" style="display: flex; align-items: center; gap: 1rem;">
                    <label style="color: var(--text-secondary); margin: 0;">Is Free Plan?</label>
                    <div style="position: relative; display: inline-block; width: 50px; height: 24px;">
                        <input type="hidden" name="is_free" value="0">
                        <input type="checkbox" name="is_free" id="isFreeInput" value="1" style="width: 50px; height: 24px; opacity: 0; cursor: pointer; position: absolute; z-index: 2;">
                        <span style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.1); border-radius: 24px; transition: 0.4s;"></span>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Status</label>
                    <select name="status" id="statusInput" class="form-control" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Description</label>
                <textarea name="description" id="descInput" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;" rows="3" placeholder="Describe the plan benefits..."></textarea>
            </div>
            
            <button type="submit" class="gradient-btn" style="width: 100%; padding: 1.2rem; border-radius: 1rem; font-size: 1.1rem;">
                Save Subscription Plan
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openPlanModal(plan = null) {
        const form = document.getElementById('planForm');
        const title = document.getElementById('modalTitle');
        const method = document.getElementById('methodField');
        
        if (plan) {
            title.innerText = 'Edit Subscription Plan';
            form.action = `/admin/plans/${plan.id}`;
            method.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            document.getElementById('nameInput').value = plan.name;
            document.getElementById('typeInput').value = plan.type;
            document.getElementById('priceInput').value = plan.price;
            document.getElementById('currencyInput').value = plan.currency || '₹';
            document.getElementById('isFreeInput').checked = plan.is_free == 1;
            document.getElementById('statusInput').value = plan.status;
            document.getElementById('descInput').value = plan.description || '';
        } else {
            title.innerText = 'Create New Plan';
            form.action = "{{ route('admin.plans.store') }}";
            method.innerHTML = '';
            form.reset();
            document.getElementById('currencyInput').value = '₹';
        }
        
        document.getElementById('planModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('planModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('planModal');
        if (event.target == modal) closeModal();
    }
</script>
@endsection
