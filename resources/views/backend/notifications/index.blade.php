@extends('backend.layouts.app')

@section('styles')
<style>
    .notification-card {
        max-width: 800px;
        margin: 0 auto;
        padding: 2rem;
        border-radius: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    label {
        display: block;
        margin-bottom: 0.5rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.8rem 1rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 0.8rem;
        color: var(--text-primary);
        font-family: inherit;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent-primary);
    }

    .radio-group {
        display: flex;
        gap: 2rem;
        margin-bottom: 1rem;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
    }

    .radio-item input {
        accent-color: var(--accent-primary);
    }

    .preview-box {
        margin-top: 2rem;
        padding: 1.5rem;
        border: 1px dashed var(--glass-border);
        border-radius: 1rem;
        background: rgba(0, 0, 0, 0.2);
    }

    .preview-notification {
        background: white;
        color: #333;
        padding: 1rem;
        border-radius: 0.8rem;
        display: flex;
        gap: 1rem;
        align-items: flex-start;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        max-width: 400px;
    }

    .app-icon {
        width: 40px;
        height: 40px;
        background: var(--primary-gradient);
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.8rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.1);
        color: #22c55e;
        border: 1px solid rgba(34, 197, 94, 0.2);
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1 class="gradient-text" style="font-size: 2rem; margin-bottom: 0.5rem;">Firebase Notifications</h1>
            <p style="color: var(--text-secondary);">Send custom push notifications to your users</p>
        </div>
        <div class="app-icon" style="width: 50px; height: 50px;">
            <i data-lucide="bell" style="color: white; width: 24px;"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="notification-card premium-glass">
        <form action="{{ route('admin.notifications.send') }}" method="POST" id="notificationForm">
            @csrf

            <!-- Target Selection -->
            <div class="form-group">
                <label>Notification Target</label>
                <div class="radio-group">
                    <label class="radio-item">
                        <input type="radio" name="target_type" value="all" checked onchange="toggleUserSelect()">
                        <span>Broadcast to All Users</span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="target_type" value="single" onchange="toggleUserSelect()">
                        <span>Specific User</span>
                    </label>
                </div>
            </div>

            <!-- Specific User Dropdown (Hidden initially) -->
            <div class="form-group" id="userSelectGroup" style="display: none;">
                <label for="user_id">Select User</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">-- Choose User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->user_name }} (ID: {{ $user->id }})</option>
                    @endforeach
                </select>
                @error('user_id') <small style="color: #ef4444;">{{ $message }}</small> @enderror
            </div>

            <!-- Title -->
            <div class="form-group">
                <label for="title">Notification Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Flash Sale! ⚡" required oninput="updatePreview()">
                @error('title') <small style="color: #ef4444;">{{ $message }}</small> @enderror
            </div>

            <!-- Body -->
            <div class="form-group">
                <label for="body">Message Content</label>
                <textarea name="body" id="body" class="form-control" rows="3" placeholder="Get 50% off on all Pro plans only for today..." required oninput="updatePreview()"></textarea>
                @error('body') <small style="color: #ef4444;">{{ $message }}</small> @enderror
            </div>

            <!-- Image URL (Optional) -->
            <div class="form-group">
                <label for="image_url">Image URL (Optional)</label>
                <input type="url" name="image_url" id="image_url" class="form-control" placeholder="https://example.com/banner.jpg" oninput="updatePreview()">
                <small style="color: var(--text-secondary); font-size: 0.8rem;">Displays as a large image on Android/iOS.</small>
            </div>

            <div style="margin-top: 2rem;">
                <button type="submit" class="gradient-btn" style="width: 100%; padding: 1rem; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                    <i data-lucide="send" style="width: 20px;"></i>
                    Send Notification
                </button>
            </div>
        </form>

        <!-- Live Preview -->
        <div class="preview-box">
            <h4 style="margin-bottom: 1rem; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-secondary);">Device Preview</h4>
            <div class="preview-notification">
                <div class="app-icon" style="width: 32px; height: 32px; border-radius: 0.4rem;">
                    <img src="{{ asset('assets/images/logo-white.png') }}" style="width: 20px;" alt="Logo">
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.2rem;">
                        <strong id="previewTitle" style="font-size: 0.9rem;">Notification Title</strong>
                        <span style="font-size: 0.7rem; color: #888;">now</span>
                    </div>
                    <p id="previewBody" style="font-size: 0.8rem; line-height: 1.4; color: #555;">Message content goes here...</p>
                    <div id="previewImageContainer" style="margin-top: 0.5rem; display: none;">
                        <img id="previewImage" src="" style="width: 100%; border-radius: 0.5rem; object-fit: cover; max-height: 150px;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleUserSelect() {
        const targetType = document.querySelector('input[name="target_type"]:checked').value;
        const userGroup = document.getElementById('userSelectGroup');
        userGroup.style.display = (targetType === 'single') ? 'block' : 'none';
        
        if (targetType === 'all') {
            document.getElementById('user_id').value = '';
        }
    }

    function updatePreview() {
        const title = document.getElementById('title').value || 'Notification Title';
        const body = document.getElementById('body').value || 'Message content goes here...';
        const image = document.getElementById('image_url').value;

        document.getElementById('previewTitle').innerText = title;
        document.getElementById('previewBody').innerText = body;

        const imgContainer = document.getElementById('previewImageContainer');
        const img = document.getElementById('previewImage');

        if (image && (image.startsWith('http') || image.startsWith('https'))) {
            img.src = image;
            imgContainer.style.display = 'block';
        } else {
            imgContainer.style.display = 'none';
        }
    }

    // Initialize preview
    updatePreview();
</script>
@endsection
