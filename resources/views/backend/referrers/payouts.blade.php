@extends('backend.layouts.app')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h1 style="font-size: 1.8rem; font-weight: 700;">Commission Payouts</h1>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Record and track payouts sent to referrers</p>
    </div>
    <a href="{{ route('admin.referrers.index') }}" class="gradient-btn" style="text-decoration: none;">&larr; Back to Referrers</a>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; padding: 0.75rem 1rem; border-radius: 0.8rem; margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #f87171; padding: 0.75rem 1rem; border-radius: 0.8rem; margin-bottom: 1.5rem;">
        {{ $errors->first() }}
    </div>
@endif

<!-- New Payout Form Card -->
<div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem; margin-bottom: 2rem;">
    <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">Record New Payout</h3>
    <form action="{{ route('admin.referrers.store-payout') }}" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
        @csrf
        <div>
            <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Referrer *</label>
            <select name="referrer_id" required style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                <option value="">Select Referrer</option>
                @foreach($referrers as $ref)
                    <option value="{{ $ref->id }}">{{ $ref->full_name }} (Pending: ₹{{ number_format($ref->commissions->where('status','pending')->sum('amount'), 2) }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Payout Amount (INR) *</label>
            <input type="number" step="0.01" name="amount" required placeholder="1000.00" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
        </div>

        <div>
            <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Payment Method *</label>
            <select name="payment_method" required style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
                <option value="bank_transfer">Bank Transfer (NEFT/IMPS)</option>
                <option value="upi">UPI / GPay / PhonePe</option>
                <option value="cash">Cash / Other</option>
            </select>
        </div>

        <div>
            <label style="display: block; margin-bottom: 0.4rem; font-size: 0.85rem;">Transaction Ref / UTR</label>
            <input type="text" name="transaction_reference" placeholder="UTR12345678" style="width: 100%; background: rgba(15, 23, 42, 0.6); border: 1px solid var(--glass-border); border-radius: 0.6rem; padding: 0.6rem; color: white;">
        </div>

        <div>
            <button type="submit" class="gradient-btn" style="width: 100%; padding: 0.65rem;">Process & Mark Paid</button>
        </div>
    </form>
</div>

<!-- Payout History Table -->
<div class="premium-glass" style="border-radius: 1rem; padding: 1.5rem; overflow-x: auto;">
    <h3 style="margin-bottom: 1rem; font-size: 1.2rem;">Payout History</h3>
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
        <thead>
            <tr style="border-bottom: 1px solid var(--glass-border);">
                <th style="padding: 1rem; color: var(--text-secondary);">ID</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Paid Date</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Referrer</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Amount Paid</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Method</th>
                <th style="padding: 1rem; color: var(--text-secondary);">Transaction Ref</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payouts as $payout)
                <tr style="border-bottom: 1px solid var(--glass-border);">
                    <td style="padding: 1rem;">#{{ $payout->id }}</td>
                    <td style="padding: 1rem;">{{ $payout->paid_at ? $payout->paid_at->format('Y-m-d H:i') : 'N/A' }}</td>
                    <td style="padding: 1rem; font-weight: 600;">{{ $payout->referrer?->full_name ?? 'N/A' }}</td>
                    <td style="padding: 1rem; color: #34d399; font-weight: 700;">₹{{ number_format($payout->amount, 2) }}</td>
                    <td style="padding: 1rem; text-transform: uppercase;">{{ str_replace('_', ' ', $payout->payment_method) }}</td>
                    <td style="padding: 1rem; font-family: monospace;">{{ $payout->transaction_reference ?? 'N/A' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="padding: 2rem; text-align: center; color: var(--text-secondary);">No payouts recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 1rem;">
        {{ $payouts->links() }}
    </div>
</div>
@endsection
