@extends('backend.layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700;">Global Referral Settings</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Configure auto-approval toggle, default discount rates, and default commission rules for referrers.</p>
    </div>
    <a href="{{ route('admin.referrers.index') }}" class="gradient-btn" style="text-decoration: none;">&larr; Back to Referrers</a>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; padding: 0.75rem 1rem; border-radius: 0.8rem; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div class="premium-glass" style="max-width: 800px; border-radius: 1rem; padding: 2rem;">
    <form action="{{ route('admin.referrers.update-settings') }}" method="POST">
        @csrf

        <!-- Auto Approve Toggle -->
        <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1.5rem;">
            <label style="display: flex; align-items: center; gap: 0.8rem; font-weight: 600; cursor: pointer;">
                <input type="checkbox" name="auto_approve_referrers" value="1" {{ ($settings['auto_approve_referrers'] ?? '0') === '1' ? 'checked' : '' }} style="width: 1.2rem; height: 1.2rem; accent-color: #a855f7;">
                <div>
                    <span style="font-size: 1.05rem;">Auto-Approve Referrer Accounts</span>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; font-weight: normal; margin-top: 0.2rem;">If enabled, newly registered referrer applications will be automatically approved upon registration.</p>
                </div>
            </label>
        </div>

        <!-- Default Discount Configuration -->
        <div style="margin-bottom: 2rem; border-bottom: 1px solid var(--glass-border); padding-bottom: 1.5rem;">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem; color: #a855f7;">Default Customer Discount Settings</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Discount Type</label>
                    <select name="default_discount_type" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                        <option value="percentage" {{ ($settings['default_discount_type'] ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ ($settings['default_discount_type'] ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount (INR)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Default Discount Value</label>
                    <input type="number" step="0.01" name="default_discount_value" value="{{ old('default_discount_value', $settings['default_discount_value'] ?? '10.00') }}" required style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                </div>
            </div>
        </div>

        <!-- Default Commission Configuration -->
        <div style="margin-bottom: 2rem;">
            <h3 style="margin-bottom: 1rem; font-size: 1.1rem; color: #a855f7;">Default Referrer Commission Settings</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Commission Type</label>
                    <select name="default_commission_type" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                        <option value="percentage" {{ ($settings['default_commission_type'] ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ ($settings['default_commission_type'] ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount (INR)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Default Commission Value</label>
                    <input type="number" step="0.01" name="default_commission_value" value="{{ old('default_commission_value', $settings['default_commission_value'] ?? '10.00') }}" required style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                </div>
            </div>
        </div>

        <button type="submit" class="gradient-btn" style="width: 100%; padding: 0.8rem; font-size: 1rem;">Save Global Referral Settings</button>
    </form>
</div>
@endsection
