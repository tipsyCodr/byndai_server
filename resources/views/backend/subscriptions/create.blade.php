@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
            <a href="{{ route('admin.subscriptions.index') }}" style="color: var(--text-secondary); text-decoration: none; display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="arrow-left" style="width: 18px;"></i>
                Back to Subscriptions
            </a>
        </div>
        <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Create New Subscription</h1>
        <p style="color: var(--text-secondary); font-size: 1.1rem;">Add a new subscription for testing payment functionality</p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem;">
            <ul style="margin: 0; padding-left: 1.5rem;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="premium-glass" style="padding: 2.5rem; border-radius: 1.5rem; max-width: 700px;">
        <form action="{{ route('admin.subscriptions.store') }}" method="POST">
            @csrf

            <!-- User Selection -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-primary); font-weight: 600;">User <span style="color: #f87171;">*</span></label>
                <select name="user_id" class="form-control" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;">
                    <option value="" style="background-color: #1a1a1a; color: #888;">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }} style="background-color: #1a1a1a; color: white;">
                            {{ $user->user_name }} (ID: {{ $user->id }})
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <span style="color: #f87171; font-size: 0.85rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Plan Selection -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-primary); font-weight: 600;">Subscription Plan <span style="color: #f87171;">*</span></label>
                <select name="plan_id" class="form-control" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;">
                    <option value="" style="background-color: #1a1a1a; color: #888;">Select Plan</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }} style="background-color: #1a1a1a; color: white;">
                            {{ $plan->name }} ({{ ucfirst($plan->type) }})
                        </option>
                    @endforeach
                </select>
                @error('plan_id')
                    <span style="color: #f87171; font-size: 0.85rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Start Date -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-primary); font-weight: 600;">Start Date <span style="color: #f87171;">*</span></label>
                <input type="date" name="start_date" value="{{ old('start_date', now()->format('Y-m-d')) }}" class="form-control" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;">
                @error('start_date')
                    <span style="color: #f87171; font-size: 0.85rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- End Date -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-primary); font-weight: 600;">End Date <span style="color: #f87171;">*</span></label>
                <input type="date" name="end_date" value="{{ old('end_date', now()->addMonth()->format('Y-m-d')) }}" class="form-control" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;">
                <p style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.5rem;">
                    <i data-lucide="info" style="width: 14px; display: inline-block; vertical-align: middle;"></i>
                    Must be after the start date
                </p>
                @error('end_date')
                    <span style="color: #f87171; font-size: 0.85rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Status -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-primary); font-weight: 600;">Payment Status <span style="color: #f87171;">*</span></label>
                <select name="payment_status" class="form-control" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;">
                    <option value="" style="background-color: #1a1a1a; color: #888;">Select Status</option>
                    <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }} style="background-color: #1a1a1a; color: white;">Pending</option>
                    <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }} style="background-color: #1a1a1a; color: white;">Paid</option>
                    <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }} style="background-color: #1a1a1a; color: white;">Failed</option>
                </select>
                @error('payment_status')
                    <span style="color: #f87171; font-size: 0.85rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment Method -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-primary); font-weight: 600;">Payment Method <span style="color: #f87171;">*</span></label>
                <select name="payment_method" class="form-control" required style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;">
                    <option value="" style="background-color: #1a1a1a; color: #888;">Select Method</option>
                    <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }} style="background-color: #1a1a1a; color: white;">Online</option>
                    <option value="offline" {{ old('payment_method') == 'offline' ? 'selected' : '' }} style="background-color: #1a1a1a; color: white;">Offline</option>
                </select>
                @error('payment_method')
                    <span style="color: #f87171; font-size: 0.85rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Payment ID (Optional) -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; margin-bottom: 0.8rem; color: var(--text-primary); font-weight: 600;">Payment ID (Optional)</label>
                <input type="text" name="payment_id" value="{{ old('payment_id') }}" class="form-control" placeholder="e.g., pay_xxx..." style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.8rem; border-radius: 0.8rem; color: white;">
                @error('payment_id')
                    <span style="color: #f87171; font-size: 0.85rem; margin-top: 0.3rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 1rem; margin-top: 2.5rem;">
                <a href="{{ route('admin.subscriptions.index') }}" style="flex: 1; padding: 1rem; border-radius: 0.8rem; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); color: var(--text-primary); cursor: pointer; font-weight: 600; text-decoration: none; text-align: center;">
                    Cancel
                </a>
                <button type="submit" class="gradient-btn" style="flex: 1; padding: 1rem; border-radius: 0.8rem; font-weight: 600;">
                    Create Subscription
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    select option {
        background-color: #1a1a1a;
        color: white;
    }
</style>
@endsection
