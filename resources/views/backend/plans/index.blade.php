@extends('backend.layouts.app')

@section('styles')
<style>
    .plan-admin select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-color: rgba(15, 23, 42, 0.95);
        color: #ffffff;
        border: 1px solid rgba(148, 163, 184, 0.2);
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='white' stroke-width='2'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1.2em 1.2em;
        padding-right: 2.5rem;
    }

    .plan-admin select option {
        background-color: #ffffff;
        color: #111827;
    }

    .plan-admin select:disabled,
    .plan-admin input:disabled,
    .plan-admin textarea:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .plan-admin select::-ms-expand {
        display: none;
    }
</style>
@endsection

@section('content')
<div class="container plan-admin">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 1.5rem; flex-wrap: wrap;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Subscription Plans</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Create plans, price them, and assign quotas in one place.</p>
        </div>
        <button onclick="openPlanModal()" class="gradient-btn" style="padding: 1rem 2rem; border-radius: 0.8rem; display: flex; align-items: center; gap: 0.8rem;">
            <i data-lucide="plus-circle" style="width: 20px;"></i>
            Create Plan
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="background: rgba(248, 113, 113, 0.15); color: #fca5a5; border: 1px solid rgba(248, 113, 113, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem;">
            <strong>Please check the plan details.</strong>
            <ul style="margin: 0.8rem 0 0 1.2rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 2rem;">
        @forelse($plans as $plan)
            <div class="premium-glass" style="padding: 2rem; border-radius: 1rem; position: relative; {{ $plan->status == 0 ? 'opacity: 0.65; filter: grayscale(0.4);' : '' }}">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;">
                    <span class="badge" style="background: rgba(255, 255, 255, 0.06); color: var(--text-secondary); padding: 0.35rem 0.8rem; border-radius: 0.5rem; font-size: 0.75rem; text-transform: uppercase; font-weight: 700;">
                        {{ $plan->type }}
                    </span>
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; justify-content: flex-end;">
                        @if($plan->is_free)
                            <span class="badge" style="background: rgba(34, 197, 94, 0.1); color: #4ade80; padding: 0.35rem 0.8rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700;">FREE</span>
                        @endif
                        <span class="badge" style="background: {{ $plan->status ? 'rgba(34, 197, 94, 0.1)' : 'rgba(248, 113, 113, 0.1)' }}; color: {{ $plan->status ? '#4ade80' : '#f87171' }}; padding: 0.35rem 0.8rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700;">
                            {{ $plan->status ? 'ACTIVE' : 'INACTIVE' }}
                        </span>
                    </div>
                </div>

                <h2 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $plan->name }}</h2>
                <p style="color: var(--text-secondary); min-height: 2.8rem; margin-bottom: 1.5rem;">{{ $plan->description ?: 'No description added yet.' }}</p>

                <div style="margin-bottom: 2rem;">
                    <span style="font-size: 2.2rem; font-weight: 800;" class="gradient-text">{{ $plan->currency ?: 'INR' }} {{ number_format($plan->price, 2) }}</span>
                    <span style="color: var(--text-secondary);">/ {{ $plan->type }}</span>
                </div>

                <div style="margin-bottom: 2rem;">
                    <h4 style="font-size: 0.8rem; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 1rem; letter-spacing: 0.05em;">Quotas</h4>
                    <ul style="list-style: none; padding: 0; margin: 0; display: grid; gap: 0.7rem;">
                        @forelse($plan->features as $pf)
                            <li style="display: flex; justify-content: space-between; gap: 1rem; color: var(--text-primary); background: rgba(255,255,255,0.04); border-radius: 0.5rem; padding: 0.7rem;">
                                <span>{{ $pf->feature->feature ?? 'Feature removed' }}</span>
                                <strong style="color: var(--accent-primary);">{{ $pf->value == -1 ? 'Unlimited' : $pf->value }} {{ $pf->unit }}</strong>
                            </li>
                        @empty
                            <li style="color: var(--text-secondary); font-size: 0.9rem; font-style: italic;">No quotas assigned yet.</li>
                        @endforelse
                    </ul>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <a href="{{ route('admin.plans.show', $plan->id) }}" class="nav-link" style="text-align: center; background: rgba(255,255,255,0.05); padding: 0.8rem; border-radius: 0.5rem; color: var(--text-primary); text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i data-lucide="sliders-horizontal" style="width: 16px;"></i>
                        Quotas
                    </a>
                    <button onclick='openPlanModal(@json($plan))' class="nav-link" style="background: rgba(99, 102, 241, 0.1); border: none; padding: 0.8rem; border-radius: 0.5rem; color: var(--accent-primary); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i data-lucide="edit-3" style="width: 16px;"></i>
                        Edit
                    </button>
                </div>
            </div>
        @empty
            <div class="premium-glass" style="grid-column: 1 / -1; padding: 3rem; text-align: center; border-radius: 1rem; color: var(--text-secondary);">
                No plans yet. Create your first subscription plan to get started.
            </div>
        @endforelse
    </div>
</div>

<div id="planModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 2rem;">
    <div class="premium-glass" style="max-width: 920px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 2rem; border-radius: 1rem; position: relative;">
        <button onclick="closeModal()" style="position: absolute; top: 1.2rem; right: 1.2rem; background: none; border: none; color: var(--text-secondary); cursor: pointer;">
            <i data-lucide="x" style="width: 24px;"></i>
        </button>

        <h2 id="modalTitle" class="gradient-text" style="font-size: 1.8rem; margin-bottom: 0.4rem;">Create Plan</h2>
        <p style="color: var(--text-secondary); margin-bottom: 2rem;">Add plan details and assign included quotas before saving.</p>

        <form id="planForm" action="{{ route('admin.plans.store') }}" method="POST">
            @csrf
            <div id="methodField"></div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem;">
                    <div class="form-group" style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.6rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">Plan Name</label>
                        <input type="text" name="name" id="nameInput" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.5rem; color: white; font-size: 1rem;" placeholder="Pro" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.2rem;">
                        <label style="display: block; margin-bottom: 0.6rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">Billing Period</label>
                        <select name="type" id="typeInput" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.5rem; color: white; font-size: 1rem; cursor: pointer;" required>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem;">
                <div class="form-group" style="margin-bottom: 1.2rem;">
                    <label style="display: block; margin-bottom: 0.6rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">Price</label>
                    <input type="number" name="price" id="priceInput" min="0" step="0.01" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.5rem; color: white; font-size: 1rem;" placeholder="500" required>
                </div>

                <div class="form-group" style="margin-bottom: 1.2rem;">
                    <label style="display: block; margin-bottom: 0.6rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">Currency</label>
                    <input type="text" name="currency" id="currencyInput" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.5rem; color: white; font-size: 1rem;" value="INR" required>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.2rem; margin-bottom: 1.2rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.6rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">Free Plan</label>
                    <label style="display: flex; align-items: center; gap: 0.7rem; background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.5rem; cursor: pointer;">
                        <input type="hidden" name="is_free" value="0">
                        <input type="checkbox" name="is_free" id="isFreeInput" value="1">
                        <span>Mark this plan as free</span>
                    </label>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.6rem; color: var(--text-secondary); font-size: 0.9rem; font-weight: 500;">Status</label>
                    <select name="status" id="statusInput" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.5rem; color: white; font-size: 1rem; cursor: pointer;">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.6rem; color: var(--text-secondary);">Description</label>
                <textarea name="description" id="descInput" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.5rem; color: white;" rows="3" placeholder="What does this plan unlock?"></textarea>
            </div>

            <div style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <h3 style="font-size: 1.1rem; font-weight: 600;">Feature Quotas</h3>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin-top: 0.3rem;">Tick features included in this plan, then choose a fixed limit or unlimited.</p>
                    </div>
                    <button type="button" onclick="enableAllQuotas()" style="background: rgba(255,255,255,0.08); color: var(--text-primary); border: 1px solid var(--glass-border); padding: 0.65rem 0.9rem; border-radius: 0.5rem; cursor: pointer;">Enable all</button>
                </div>

                <div style="display: grid; gap: 0.8rem;">
                    @forelse($allFeatures as $feature)
                        <div class="quota-row" data-feature-id="{{ $feature->id }}" style="display: grid; grid-template-columns: minmax(200px, 1.2fr) 0.75fr 0.85fr 0.85fr; gap: 0.8rem; align-items: center; background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); border-radius: 0.8rem; padding: 0.9rem;">
                            <label style="display: flex; align-items: center; gap: 0.7rem; color: var(--text-primary); font-weight: 600; cursor: pointer;">
                                <input type="hidden" name="features[{{ $feature->id }}][enabled]" value="0">
                                <input type="checkbox" class="quota-enabled" name="features[{{ $feature->id }}][enabled]" value="1" onchange="toggleQuotaRow(this)">
                                {{ $feature->feature }}
                            </label>
                            <select class="quota-mode" onchange="toggleQuotaMode(this)" disabled style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.75rem; border-radius: 0.5rem; color: white; font-size: 0.95rem; cursor: pointer;">
                                <option value="limited">Limited</option>
                                <option value="unlimited">Unlimited</option>
                            </select>
                            <input type="text" class="quota-value" name="features[{{ $feature->id }}][value]" placeholder="10" disabled style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.75rem; border-radius: 0.5rem; color: white; font-size: 0.95rem;">
                            <input type="text" class="quota-unit" name="features[{{ $feature->id }}][unit]" placeholder="unit" disabled style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.75rem; border-radius: 0.5rem; color: white; font-size: 0.95rem;">
                        </div>
                    @empty
                        <div style="color: var(--text-secondary); background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); border-radius: 0.8rem; padding: 1rem;">
                            Add master features first, then return here to assign quotas.
                        </div>
                    @endforelse
                </div>
            </div>

            <button type="submit" class="gradient-btn" style="width: 100%; padding: 1.1rem; border-radius: 0.8rem; font-size: 1.05rem;">
                Save Plan and Quotas
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

        form.reset();
        resetQuotaRows();
        document.getElementById('currencyInput').value = 'INR';
        document.getElementById('statusInput').value = '1';
        document.getElementById('typeInput').value = 'monthly';

        if (plan) {
            title.innerText = 'Edit Plan';
            form.action = `/plans/${plan.id}`;
            method.innerHTML = '<input type="hidden" name="_method" value="PUT">';

            document.getElementById('nameInput').value = plan.name || '';
            document.getElementById('typeInput').value = plan.type || 'monthly';
            document.getElementById('priceInput').value = plan.price || 0;
            document.getElementById('currencyInput').value = plan.currency || 'INR';
            document.getElementById('isFreeInput').checked = plan.is_free == 1;
            document.getElementById('statusInput').value = plan.status ?? 1;
            document.getElementById('descInput').value = plan.description || '';

            if (Array.isArray(plan.features)) {
                plan.features.forEach((quota) => {
                    const row = document.querySelector(`.quota-row[data-feature-id="${quota.feature_id}"]`);
                    if (!row) return;

                    const enabled = row.querySelector('.quota-enabled');
                    const mode = row.querySelector('.quota-mode');
                    const value = row.querySelector('.quota-value');
                    const unit = row.querySelector('.quota-unit');

                    enabled.checked = true;
                    mode.disabled = false;
                    value.disabled = false;
                    unit.disabled = false;
                    value.value = quota.value || '';
                    unit.value = quota.unit || '';
                    mode.value = quota.value == -1 ? 'unlimited' : 'limited';
                    toggleQuotaMode(mode);
                });
            }
        } else {
            title.innerText = 'Create Plan';
            form.action = "{{ route('admin.plans.store') }}";
            method.innerHTML = '';
        }

        document.getElementById('planModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('planModal').style.display = 'none';
    }

    function resetQuotaRows() {
        document.querySelectorAll('.quota-row').forEach((row) => {
            const enabled = row.querySelector('.quota-enabled');
            const mode = row.querySelector('.quota-mode');
            const value = row.querySelector('.quota-value');
            const unit = row.querySelector('.quota-unit');

            enabled.checked = false;
            mode.value = 'limited';
            mode.disabled = true;
            value.value = '';
            unit.value = '';
            value.readOnly = false;
            value.disabled = true;
            unit.disabled = true;
        });
    }

    function toggleQuotaRow(checkbox) {
        const row = checkbox.closest('.quota-row');
        const mode = row.querySelector('.quota-mode');
        const value = row.querySelector('.quota-value');
        const unit = row.querySelector('.quota-unit');

        mode.disabled = !checkbox.checked;
        value.disabled = !checkbox.checked;
        unit.disabled = !checkbox.checked;

        if (checkbox.checked && !value.value) {
            value.value = '10';
        }

        if (checkbox.checked) {
            toggleQuotaMode(mode);
        }
    }

    function toggleQuotaMode(select) {
        const row = select.closest('.quota-row');
        const value = row.querySelector('.quota-value');

        if (select.value === 'unlimited') {
            value.value = '-1';
            value.disabled = false;
            value.readOnly = true;
            return;
        }

        value.readOnly = false;
        value.disabled = !row.querySelector('.quota-enabled').checked;
        if (value.value === '-1') {
            value.value = '';
        }
    }

    function enableAllQuotas() {
        document.querySelectorAll('.quota-enabled').forEach((checkbox) => {
            checkbox.checked = true;
            toggleQuotaRow(checkbox);
        });
    }

    window.onclick = function(event) {
        const modal = document.getElementById('planModal');
        if (event.target === modal) closeModal();
    }
</script>
@endsection
