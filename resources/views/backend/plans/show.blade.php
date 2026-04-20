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
@php
    $quotaMap = $plan->features->keyBy('feature_id');
@endphp

<div class="container plan-admin">
    <div class="header-area" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: flex-end; gap: 1.5rem; flex-wrap: wrap;">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <a href="{{ route('admin.plans.index') }}" style="color: var(--text-secondary); text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="arrow-left" style="width: 18px;"></i>
                    Back to Plans
                </a>
            </div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $plan->name }} Quotas</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Tick included features, set their limits, and save all changes together.</p>
        </div>
        <div class="premium-glass" style="padding: 1rem 1.5rem; border-radius: 0.8rem; display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
            <span style="color: var(--text-secondary);">{{ $plan->currency ?: 'INR' }} {{ number_format($plan->price, 2) }}</span>
            <strong style="color: var(--accent-primary); text-transform: uppercase;">{{ $plan->type }}</strong>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger" style="background: rgba(248, 113, 113, 0.15); color: #fca5a5; border: 1px solid rgba(248, 113, 113, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem;">
            <strong>Please check the quota values.</strong>
            <ul style="margin: 0.8rem 0 0 1.2rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <input type="hidden" name="name" value="{{ $plan->name }}">
        <input type="hidden" name="type" value="{{ $plan->type }}">
        <input type="hidden" name="price" value="{{ $plan->price }}">
        <input type="hidden" name="currency" value="{{ $plan->currency ?: 'INR' }}">
        <input type="hidden" name="is_free" value="{{ $plan->is_free ? 1 : 0 }}">
        <input type="hidden" name="status" value="{{ $plan->status }}">
        <input type="hidden" name="description" value="{{ $plan->description }}">

        <div class="premium-glass" style="padding: 0; overflow: hidden; border-radius: 1rem;">
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 600;">Feature Quotas</h3>
                    <p style="color: var(--text-secondary); margin-top: 0.35rem;">Choose a fixed limit or unlimited. Unticked features are removed from this plan.</p>
                </div>
                <div style="display: flex; gap: 0.8rem; flex-wrap: wrap;">
                    <button type="button" onclick="enableAllQuotas()" style="background: rgba(255,255,255,0.08); color: var(--text-primary); border: 1px solid var(--glass-border); padding: 0.7rem 1rem; border-radius: 0.5rem; cursor: pointer;">Enable all</button>
                    <button type="button" onclick="disableAllQuotas()" style="background: rgba(248,113,113,0.08); color: #fca5a5; border: 1px solid rgba(248,113,113,0.25); padding: 0.7rem 1rem; border-radius: 0.5rem; cursor: pointer;">Clear all</button>
                </div>
            </div>

            <div style="padding: 1.5rem; display: grid; gap: 0.9rem;">
                @forelse($allFeatures as $feature)
                    @php
                        $quota = $quotaMap->get($feature->id);
                        $enabled = (bool) $quota;
                    @endphp
                    <div class="quota-row" style="display: grid; grid-template-columns: minmax(200px, 1.2fr) 0.75fr 0.85fr 0.85fr; gap: 1rem; align-items: center; background: rgba(255,255,255,0.04); border: 1px solid var(--glass-border); border-radius: 0.8rem; padding: 1rem;">
                        <label style="display: flex; align-items: center; gap: 0.8rem; color: var(--text-primary); font-weight: 600; cursor: pointer;">
                            <input type="hidden" name="features[{{ $feature->id }}][enabled]" value="0">
                            <input type="checkbox" class="quota-enabled" name="features[{{ $feature->id }}][enabled]" value="1" onchange="toggleQuotaRow(this)" {{ $enabled ? 'checked' : '' }}>
                            {{ $feature->feature }}
                        </label>
                        <div>
                            <label style="display: block; color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 0.4rem; font-weight: 500;">Mode</label>
                            <select class="quota-mode" onchange="toggleQuotaMode(this)" {{ $enabled ? '' : 'disabled' }} style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.75rem; border-radius: 0.5rem; color: white; font-size: 0.95rem; cursor: pointer;">
                                <option value="limited" {{ ($quota->value ?? '') == -1 ? '' : 'selected' }}>Limited</option>
                                <option value="unlimited" {{ ($quota->value ?? '') == -1 ? 'selected' : '' }}>Unlimited</option>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 0.4rem; font-weight: 500;">Limit</label>
                            <input type="text" class="quota-value" name="features[{{ $feature->id }}][value]" value="{{ $quota->value ?? '' }}" placeholder="10" {{ $enabled ? '' : 'disabled' }} {{ $enabled && ($quota->value ?? '') == -1 ? 'readonly' : '' }} style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.75rem; border-radius: 0.5rem; color: white; font-size: 0.95rem;">
                        </div>
                        <div>
                            <label style="display: block; color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 0.4rem; font-weight: 500;">Unit</label>
                            <input type="text" class="quota-unit" name="features[{{ $feature->id }}][unit]" value="{{ $quota->unit ?? '' }}" placeholder="unit" {{ $enabled ? '' : 'disabled' }} style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.75rem; border-radius: 0.5rem; color: white; font-size: 0.95rem;">
                        </div>
                    </div>\n                @empty\n                    <div style=\"padding: 3rem; text-align: center; color: var(--text-secondary);\">\n                        No master features exist yet. Create master features first, then assign them to plans.\n                    </div>\n                @endforelse\n            </div>"
        </div>

        <div style="display: flex; justify-content: flex-end; margin-top: 1.5rem;">
            <button type="submit" class="gradient-btn" style="padding: 1rem 1.6rem; border-radius: 0.8rem; display: flex; align-items: center; gap: 0.6rem;">
                <i data-lucide="save" style="width: 18px;"></i>
                Save Quotas
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
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

    function disableAllQuotas() {
        document.querySelectorAll('.quota-enabled').forEach((checkbox) => {
            checkbox.checked = false;
            toggleQuotaRow(checkbox);
        });
    }
</script>
@endsection
