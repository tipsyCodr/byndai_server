@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                <a href="{{ route('admin.plans.index') }}" style="color: var(--text-secondary); text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                    <i data-lucide="arrow-left" style="width: 18px;"></i>
                    Back to Plans
                </a>
            </div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Configure: {{ $plan->name }}</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Assign feature quotas and limits for this specific plan</p>
        </div>
        <div class="premium-glass" style="padding: 1rem 1.5rem; border-radius: 1rem; display: flex; align-items: center; gap: 1rem;">
            <span style="color: var(--text-secondary);">Plan Type:</span>
            <strong style="color: var(--accent-primary); text-transform: uppercase;">{{ $plan->type }}</strong>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 350px; gap: 2rem;">
        <!-- Current Features Table -->
        <div class="premium-glass" style="padding: 0; overflow: hidden; border-radius: 1.5rem;">
            <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.2rem; font-weight: 600;">Active Quotas</h3>
                <span style="color: var(--text-secondary); font-size: 0.9rem;">{{ $plan->features->count() }} features active</span>
            </div>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: rgba(255, 255, 255, 0.05);">
                        <th style="padding: 1.2rem 2rem; color: var(--text-secondary); font-size: 0.8rem; text-transform: uppercase;">Feature</th>
                        <th style="padding: 1.2rem 2rem; color: var(--text-secondary); font-size: 0.8rem; text-transform: uppercase;">Allowed Value</th>
                        <th style="padding: 1.2rem 2rem; color: var(--text-secondary); font-size: 0.8rem; text-transform: uppercase; text-align: right;">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plan->features as $pf)
                    <tr style="border-bottom: 1px solid var(--glass-border);">
                        <td style="padding: 1.2rem 2rem;">
                            <span style="font-weight: 600;">{{ $pf->feature->feature }}</span>
                        </td>
                        <td style="padding: 1.2rem 2rem;">
                            <span style="color: var(--accent-primary); font-weight: 700;">{{ $pf->value }}</span>
                            <span style="color: var(--text-secondary); font-size: 0.9rem;">{{ $pf->unit }}</span>
                        </td>
                        <td style="padding: 1.2rem 2rem; text-align: right;">
                            <form action="{{ route('admin.plans.removeFeature', [$plan->id, $pf->feature_id]) }}" method="POST" onsubmit="return confirm('Remove this feature quota?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: none; border: none; color: #f87171; cursor: pointer;">
                                    <i data-lucide="x" style="width: 18px;"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" style="padding: 4rem; text-align: center; color: var(--text-secondary);">
                            No features assigned to this plan yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Add Feature Form -->
        <div class="premium-glass" style="padding: 2rem; border-radius: 1.5rem; align-self: start;">
            <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 1.5rem;">Add/Update quota</h3>
            <form action="{{ route('admin.plans.addFeature', $plan->id) }}" method="POST">
                @csrf
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Select Feature</label>
                    <select name="feature_id" class="form-control" style="width: 100%; background: rgba(255,255,255,0.1); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;" required>
                        @foreach($allFeatures as $feature)
                            <option value="{{ $feature->id }}">{{ $feature->feature }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Value</label>
                    <input type="text" name="value" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;" placeholder="e.g. 10 or Unlimited" required>
                    <small style="color: var(--text-secondary); margin-top: 0.5rem; display: block;">Use <strong>-1</strong> for Unlimited</small>
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label style="display: block; margin-bottom: 0.8rem; color: var(--text-secondary);">Unit (Optional)</label>
                    <input type="text" name="unit" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;" placeholder="e.g. per month, total">
                </div>

                <button type="submit" class="gradient-btn" style="width: 100%; padding: 1rem; border-radius: 0.8rem; font-weight: 600;">
                    Update Quota
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
