@extends('backend.layouts.app')

@section('content')
<style>
    /* Toggle Switch CSS */
    .switch {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 26px;
    }
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255,255,255,0.1);
        transition: .4s;
        border: 1px solid var(--glass-border);
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
    }
    input:checked + .slider {
        background-color: #4ade80;
    }
    input:focus + .slider {
        box-shadow: 0 0 1px #4ade80;
    }
    input:checked + .slider:before {
        transform: translateX(24px);
    }
    .slider.round {
        border-radius: 34px;
    }
    .slider.round:before {
        border-radius: 50%;
    }
</style>
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <a href="{{ route('admin.subscriptions.index') }}" style="color: var(--text-secondary); text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="arrow-left" style="width: 18px;"></i>
                    Back to Subscriptions
                </a>
            </div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Usage Stats: {{ $subscription->user->user_name ?? 'User' }}</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Monitoring feature consumption for <strong>{{ $subscription->plan->name ?? 'Plan' }}</strong></p>
        </div>
        <div class="premium-glass" style="padding: 1rem 1.5rem; border-radius: 1rem; text-align: right;">
            <div style="color: var(--text-secondary); font-size: 0.8rem; text-transform: uppercase; margin-bottom: 0.3rem;">Subscription Status</div>
            @if($subscription->isActive())
                <strong style="color: #4ade80;">ACTIVE</strong>
            @else
                <strong style="color: #f87171;">EXPIRED</strong>
            @endif
        </div>
    </div>
    
    <div class="header-area" style="margin-bottom: 2rem;">
        <div class="premium-glass" style="padding: 2rem; border-radius: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1.3rem; font-weight: 700;">Managed Badges</h3>
                <span style="color: var(--text-secondary); font-size: 0.8rem; text-transform: uppercase;">Public Profile Badges</span>
            </div>
            
            <form action="{{ route('admin.subscriptions.updateBadges', $subscription->id) }}" method="POST" style="display: flex; gap: 2rem; align-items: center;">
                @csrf
                <div style="display: flex; align-items: center; gap: 0.8rem;">
                    <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 26px;">
                        <input type="checkbox" name="jci_member" value="1" {{ ($subscription->user->masterUser->jci_member ?? 0) ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="slider round"></span>
                    </label>
                    <span style="font-weight: 600;">JCI Badge</span>
                </div>

                <div style="display: flex; align-items: center; gap: 0.8rem;">
                    <label class="switch" style="position: relative; display: inline-block; width: 50px; height: 26px;">
                        <input type="checkbox" name="bni_member" value="1" {{ ($subscription->user->masterUser->bni_member ?? 0) ? 'checked' : '' }} onchange="this.form.submit()">
                        <span class="slider round"></span>
                    </label>
                    <span style="font-weight: 600;">BNI Badge</span>
                </div>
                
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0; margin-left: auto;">
                    <i data-lucide="info" style="width: 14px; display: inline-block; vertical-align: middle;"></i>
                    Toggling will automatically save and update the user's public profile.
                </p>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr)); gap: 2rem;">
        @foreach($subscription->features as $usf)
            @php
                $limitObj = $planFeatures->get($usf->feature_id);
                $limit = $limitObj ? $limitObj->value : 0;
                $isUnlimited = ($limit == -1);
                $percentage = $isUnlimited ? 0 : min(100, ($usf->used_value / max(1, $limit)) * 100);
                $progressColor = $percentage > 90 ? '#f87171' : ($percentage > 70 ? '#fbbf24' : '#4ade80');
            @endphp
            
            <div class="premium-glass" style="padding: 2.5rem; border-radius: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3 style="font-size: 1.2rem; font-weight: 700;">{{ $usf->feature->feature }}</h3>
                    <button onclick="openUsageModal({{ $usf->feature_id }}, '{{ $usf->feature->feature }}', {{ $usf->used_value }})" style="background: rgba(255,255,255,0.05); border: none; color: var(--text-secondary); cursor: pointer; padding: 0.5rem; border-radius: 0.5rem;">
                        <i data-lucide="edit-2" style="width: 16px;"></i>
                    </button>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 0.8rem; font-size: 0.9rem;">
                    <span style="color: var(--text-secondary);">Current Consumption</span>
                    <span style="color: var(--text-primary); font-weight: 700;">
                        @if($isUnlimited)
                            {{ $usf->used_value }} / <i data-lucide="infinity" style="width: 14px; display: inline-block;"></i>
                        @else
                            {{ $usf->used_value }} / {{ $limit }} {{ $usf->unit ?? '' }}
                        @endif
                    </span>
                </div>

                <div style="width: 100%; height: 8px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: hidden; margin-bottom: 0.5rem;">
                    <div style="width: {{ $isUnlimited ? '0' : $percentage }}%; height: 100%; background: {{ $progressColor }}; box-shadow: 0 0 10px {{ $progressColor }}44; border-radius: 10px; transition: 0.6s cubic-bezier(0.4, 0, 0.2, 1);"></div>
                </div>
                
                @if(!$isUnlimited)
                    <div style="text-align: right; font-size: 0.75rem; color: var(--text-secondary);">
                        {{ number_format($percentage, 0) }}% Used
                    </div>
                @endif
            </div>
        @endforeach
    </div>
</div>

<!-- Usage Override Modal -->
<div id="usageModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); backdrop-filter: blur(8px); z-index: 9999; align-items: center; justify-content: center; padding: 2rem;">
    <div class="premium-glass" style="max-width: 450px; width: 100%; padding: 2.5rem; border-radius: 2rem; position: relative;">
        <button onclick="closeModal()" style="position: absolute; top: 1.5rem; right: 1.5rem; background: none; border: none; color: var(--text-secondary); cursor: pointer;">
            <i data-lucide="x" style="width: 24px;"></i>
        </button>
        
        <h2 class="gradient-text" style="font-size: 1.8rem; margin-bottom: 2rem;">Override Usage</h2>
        <p id="featureName" style="color: var(--text-secondary); margin-bottom: 1.5rem;"></p>
        
        <form action="{{ route('admin.subscriptions.updateUsage', $subscription->id) }}" method="POST">
            @csrf
            <input type="hidden" name="feature_id" id="modalFeatureId">
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 1rem; color: var(--text-secondary);">Adjust Current Value</label>
                <input type="number" name="used_value" id="modalUsedValue" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 1rem; border-radius: 0.8rem; color: white;" required min="0">
            </div>
            
            <button type="submit" class="gradient-btn" style="width: 100%; padding: 1.2rem; border-radius: 1rem; font-size: 1.1rem;">
                Apply Changes
            </button>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function openUsageModal(featureId, name, current) {
        document.getElementById('modalFeatureId').value = featureId;
        document.getElementById('featureName').innerText = "Feature: " + name;
        document.getElementById('modalUsedValue').value = current;
        document.getElementById('usageModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('usageModal').style.display = 'none';
    }

    window.onclick = function(event) {
        const modal = document.getElementById('usageModal');
        if (event.target == modal) closeModal();
    }
</script>
@endsection
