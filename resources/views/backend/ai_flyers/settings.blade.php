@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">AI Flyer Studio Settings</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Configure OpenAI image models, generation quotas and retention limits</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('admin.ai-flyers.occasions') }}" class="btn" style="background: rgba(255,255,255,0.06); color: var(--text-primary); padding: 0.8rem 1.5rem; border-radius: 0.8rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <i data-lucide="calendar" style="width: 18px;"></i>
                Occasions Calendar
            </a>
            <a href="{{ route('admin.ai-flyers.monitoring') }}" class="btn" style="background: rgba(255,255,255,0.06); color: var(--text-primary); padding: 0.8rem 1.5rem; border-radius: 0.8rem; display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <i data-lucide="activity" style="width: 18px;"></i>
                Usage Monitoring
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div class="premium-glass" style="padding: 2.5rem; border-radius: 1.5rem;">
            <form method="POST" action="{{ route('admin.ai-flyers.settings') }}">
                @csrf

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.8rem; font-weight: 600;">AI Flyer Generator Status</label>
                    <select name="ai_flyer_enabled" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.9rem 1.2rem; border-radius: 0.8rem; color: #fff;">
                        <option value="1" {{ $settings['ai_flyer_enabled'] == '1' ? 'selected' : '' }}>Enabled (Active for all users)</option>
                        <option value="0" {{ $settings['ai_flyer_enabled'] == '0' ? 'selected' : '' }}>Disabled (Temporarily offline)</option>
                    </select>
                    <small style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.4rem; display: block;">Master toggle to turn AI generation on or off without code deployment.</small>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.8rem; font-weight: 600;">OpenAI API Secret Key (Optional Override)</label>
                    <input type="password" name="openai_api_key" placeholder="{{ !empty($settings['openai_api_key']) ? '••••••••••••••••••••••••' : 'sk-proj-...' }}" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.9rem 1.2rem; border-radius: 0.8rem; color: #fff;">
                    <small style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.4rem; display: block;">If left empty, system uses OPENAI_API_KEY from server .env file.</small>
                </div>

                <div style="margin-bottom: 2rem;">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.8rem; font-weight: 600;">OpenAI Image Generation Model</label>
                    <select name="openai_model" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.9rem 1.2rem; border-radius: 0.8rem; color: #fff;">
                        <option value="gpt-image-2.6-flare" {{ $settings['openai_model'] == 'gpt-image-2.6-flare' ? 'selected' : '' }}>gpt-image-2.6-flare (Recommended Default)</option>
                        <option value="dall-e-3" {{ $settings['openai_model'] == 'dall-e-3' ? 'selected' : '' }}>dall-e-3 (Standard Production)</option>
                    </select>
                    <small style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.4rem; display: block;">The system automatically falls back to DALL-E-3 if the selected model is not whitelisted on your OpenAI account.</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <label style="display: block; color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.8rem; font-weight: 600;">Default Image Quality</label>
                        <select name="image_quality" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.9rem 1.2rem; border-radius: 0.8rem; color: #fff;">
                            <option value="standard" {{ $settings['image_quality'] == 'standard' ? 'selected' : '' }}>Standard (Faster, lower cost)</option>
                            <option value="hd" {{ $settings['image_quality'] == 'hd' ? 'selected' : '' }}>HD (High Definition detail)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.8rem; font-weight: 600;">Default Aspect Ratio</label>
                        <select name="default_aspect_ratio" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.9rem 1.2rem; border-radius: 0.8rem; color: #fff;">
                            <option value="1:1" {{ $settings['default_aspect_ratio'] == '1:1' ? 'selected' : '' }}>Square (1:1)</option>
                            <option value="4:5" {{ $settings['default_aspect_ratio'] == '4:5' ? 'selected' : '' }}>Portrait (4:5)</option>
                            <option value="9:16" {{ $settings['default_aspect_ratio'] == '9:16' ? 'selected' : '' }}>Story (9:16)</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <label style="display: block; color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.8rem; font-weight: 600;">Daily Limit Per User</label>
                        <input type="number" name="daily_generation_limit_per_user" value="{{ $settings['daily_generation_limit_per_user'] }}" min="1" max="100" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.9rem 1.2rem; border-radius: 0.8rem; color: #fff;" required>
                        <small style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.4rem; display: block;">Maximum flyers a single user can generate per day.</small>
                    </div>

                    <div>
                        <label style="display: block; color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.8rem; font-weight: 600;">Max Saved Flyers Per User (Retention)</label>
                        <input type="number" name="max_saved_flyers_per_user" value="{{ $settings['max_saved_flyers_per_user'] }}" min="1" max="100" class="form-control" style="width: 100%; background: rgba(255,255,255,0.05); border: 1px solid var(--glass-border); padding: 0.9rem 1.2rem; border-radius: 0.8rem; color: #fff;" required>
                        <small style="color: var(--text-secondary); font-size: 0.8rem; margin-top: 0.4rem; display: block;">Oldest flyers beyond this count are automatically purged.</small>
                    </div>
                </div>

                <button type="submit" class="gradient-btn" style="padding: 1rem 2.5rem; border-radius: 1rem; border: none; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 0.8rem;">
                    <i data-lucide="save" style="width: 20px;"></i>
                    Save Configuration
                </button>
            </form>
        </div>

        <div>
            <div class="premium-glass" style="padding: 2rem; border-radius: 1.5rem; margin-bottom: 1.5rem;">
                <h3 style="color: var(--text-primary); font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.6rem;">
                    <i data-lucide="shield-check" style="color: #4ade80;"></i>
                    Server Security
                </h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.2rem;">
                    OpenAI API secret keys are stored securely on the Laravel host and never returned to client devices.
                </p>
                <div style="background: rgba(255,255,255,0.04); padding: 0.8rem 1rem; border-radius: 0.8rem; border: 1px solid var(--glass-border);">
                    <span style="color: var(--text-secondary); font-size: 0.8rem; display: block;">API Secret Status:</span>
                    <span style="color: #4ade80; font-weight: 700; font-size: 0.95rem;">
                        {{ config('services.openai.key') || env('OPENAI_API_SECRET_KEY') || env('OPENAI_API_KEY') ? 'Configured & Active' : 'Missing API Key in .env' }}
                    </span>
                </div>
            </div>

            <div class="premium-glass" style="padding: 2rem; border-radius: 1.5rem;">
                <h3 style="color: var(--text-primary); font-size: 1.2rem; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.6rem;">
                    <i data-lucide="trash-2" style="color: #f87171;"></i>
                    Storage Retention
                </h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6; margin-bottom: 1.5rem;">
                    Run manual storage cleanup to purge obsolete flyer files exceeding the maximum retention limit.
                </p>
                <form method="POST" action="{{ route('admin.ai-flyers.cleanup') }}">
                    @csrf
                    <button type="submit" class="btn" style="width: 100%; background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); padding: 0.8rem 1.2rem; border-radius: 0.8rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i data-lucide="refresh-cw" style="width: 16px;"></i>
                        Run Retention Cleanup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

