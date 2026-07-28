@extends('backend.layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700;">Referrer Details: {{ $referrer->full_name }}</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Manage status, coupon code settings, commission rates, and review document</p>
    </div>
    <a href="{{ route('admin.referrers.index') }}" class="gradient-btn" style="text-decoration: none;">&larr; Back to Referrers</a>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; padding: 0.75rem 1rem; border-radius: 0.8rem; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Profile & Status Card -->
    <div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">Registration Info & Review</h3>
        
        <div style="margin-bottom: 0.8rem;"><strong>Mobile:</strong> {{ $referrer->user?->user_mobile }}</div>
        <div style="margin-bottom: 0.8rem;"><strong>Email:</strong> {{ $referrer->user?->email }}</div>
        <div style="margin-bottom: 0.8rem;"><strong>Address:</strong> {{ $referrer->address }}</div>
        <div style="margin-bottom: 1rem;"><strong>Current Status:</strong> <span style="text-transform: uppercase; font-weight: 700; color: #a855f7;">{{ $referrer->status }}</span></div>

        @if($referrer->aadhaar_photo)
            <div style="margin-bottom: 1.5rem;">
                <strong style="display: block; margin-bottom: 0.5rem;">Aadhaar Photo Document:</strong>
                <a href="{{ asset('storage/' . $referrer->aadhaar_photo) }}" target="_blank">
                    <img src="{{ asset('storage/' . $referrer->aadhaar_photo) }}" style="max-width: 100%; height: 180px; object-fit: cover; border-radius: 0.8rem; border: 1px solid var(--glass-border);" alt="Aadhaar Photo">
                </a>
            </div>
        @endif

        <form action="{{ route('admin.referrers.update-status', $referrer->id) }}" method="POST" style="margin-top: 1rem; border-top: 1px solid var(--glass-border); padding-top: 1rem;">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Update Status</label>
                <select name="status" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                    <option value="pending" {{ $referrer->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ $referrer->status === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ $referrer->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>

            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Admin Remarks (Optional)</label>
                <textarea name="admin_remarks" rows="2" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">{{ $referrer->admin_remarks }}</textarea>
            </div>

            <button type="submit" class="gradient-btn" style="width: 100%;">Save Status Decision</button>
        </form>
    </div>

    <!-- Coupon Code & Commission Settings Card -->
    <div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem;">
        <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">Coupon & Commission Configuration</h3>

        <form action="{{ route('admin.referrers.update-coupon', $referrer->id) }}" method="POST">
            @csrf
            <div style="margin-bottom: 1rem;">
                <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Coupon Code (Unique)</label>
                <input type="text" name="code" value="{{ old('code', $referrer->coupon?->code) }}" required style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white; font-family: monospace; font-weight: 700;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Discount Type</label>
                    <select name="discount_type" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                        <option value="percentage" {{ ($referrer->coupon?->discount_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ ($referrer->coupon?->discount_type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount (INR)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Discount Value</label>
                    <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $referrer->coupon?->discount_value ?? 10.00) }}" required style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Commission Type</label>
                    <select name="commission_type" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                        <option value="percentage" {{ ($referrer->coupon?->commission_type ?? 'percentage') === 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="fixed" {{ ($referrer->coupon?->commission_type ?? '') === 'fixed' ? 'selected' : '' }}>Fixed Amount (INR)</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Commission Value</label>
                    <input type="number" step="0.01" name="commission_value" value="{{ old('commission_value', $referrer->coupon?->commission_value ?? 10.00) }}" required style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                </div>
            </div>

            <button type="submit" class="gradient-btn" style="width: 100%; margin-bottom: 1rem;">Update Coupon Rules</button>
        </form>

        <form action="{{ route('admin.referrers.regenerate-code', $referrer->id) }}" method="POST" style="margin-bottom: 1.5rem;">
            @csrf
            <button type="submit" style="width: 100%; background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 0.6rem; padding: 0.6rem; cursor: pointer; font-weight: 600;">
                Regenerate Random Code
            </button>
        </form>

        <!-- Admin Reset Password Section -->
        <div style="border-top: 1px solid var(--glass-border); padding-top: 1rem;">
            <h4 style="margin-bottom: 0.8rem; font-size: 1rem; color: #a855f7;">Reset User Password</h4>
            <form action="{{ route('admin.referrers.reset-password', $referrer->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 0.8rem;">
                    <label style="display: block; margin-bottom: 0.3rem; font-size: 0.8rem;">New Password</label>
                    <input type="password" name="password" required minlength="6" placeholder="Enter new password" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.5rem; color: white;">
                </div>
                <div style="margin-bottom: 0.8rem;">
                    <label style="display: block; margin-bottom: 0.3rem; font-size: 0.8rem;">Confirm New Password</label>
                    <input type="password" name="password_confirmation" required minlength="6" placeholder="Confirm new password" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.5rem; color: white;">
                </div>
                <button type="submit" class="gradient-btn" style="width: 100%; padding: 0.5rem; font-size: 0.85rem;">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Sales History for this Referrer -->
<div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem;">
    <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">Referral Sales History</h3>
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <th style="padding: 0.8rem;">Date</th>
                <th style="padding: 0.8rem;">Customer</th>
                <th style="padding: 0.8rem;">Plan</th>
                <th style="padding: 0.8rem;">Original</th>
                <th style="padding: 0.8rem;">Discount</th>
                <th style="padding: 0.8rem;">Final Amount</th>
                <th style="padding: 0.8rem;">Commission</th>
            </tr>
        </thead>
        <tbody>
            @forelse($referrer->sales as $sale)
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 0.8rem;">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                    <td style="padding: 0.8rem;">{{ $sale->user?->user_name }} ({{ $sale->user?->email }})</td>
                    <td style="padding: 0.8rem;">{{ $sale->plan?->name }}</td>
                    <td style="padding: 0.8rem;">₹{{ number_format($sale->original_price, 2) }}</td>
                    <td style="padding: 0.8rem;">₹{{ number_format($sale->discount_amount, 2) }}</td>
                    <td style="padding: 0.8rem;">₹{{ number_format($sale->final_amount, 2) }}</td>
                    <td style="padding: 0.8rem; color: #a855f7; font-weight: 700;">₹{{ number_format($sale->commission_amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 1.5rem; text-align: center; color: var(--text-secondary);">No sales recorded yet for this referrer.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
