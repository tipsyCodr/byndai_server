@extends('backend.layouts.app')

@section('styles')
<style>
    .notification-card {
        max-width: 800px;
        margin: 0 auto;
        padding: 2.5rem;
        border-radius: 1.5rem;
    }

    .form-group {
        margin-bottom: 2rem;
    }

    label {
        display: block;
        margin-bottom: 0.8rem;
        color: var(--text-secondary);
        font-weight: 500;
        font-size: 0.95rem;
    }

    .form-control {
        width: 100%;
        padding: 0.9rem 1.2rem;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--glass-border);
        border-radius: 0.8rem;
        color: var(--text-primary);
        font-family: inherit;
        transition: all 0.3s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent-primary);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 15px rgba(99, 102, 241, 0.2);
    }

    .radio-group {
        display: flex;
        gap: 2rem;
        margin-bottom: 1rem;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        cursor: pointer;
        padding: 0.5rem 1rem;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 0.6rem;
        transition: background 0.3s;
    }

    .radio-item:hover {
        background: rgba(255, 255, 255, 0.06);
    }

    .preview-box {
        margin-top: 3rem;
        padding: 2rem;
        border: 1px dashed var(--glass-border);
        border-radius: 1.2rem;
        background: rgba(0, 0, 0, 0.3);
    }

    .preview-notification {
        background: white;
        color: #1a1a1a;
        padding: 1.2rem;
        border-radius: 1rem;
        display: flex;
        gap: 1.2rem;
        align-items: flex-start;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        max-width: 420px;
    }

    .app-icon-small {
        width: 44px;
        height: 44px;
        background: var(--primary-gradient);
        border-radius: 0.7rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .alert {
        padding: 1.2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.15);
        color: #4ade80;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .section-divider {
        height: 1px;
        background: var(--glass-border);
        margin: 2.5rem 0;
        position: relative;
    }

    .section-title {
        position: absolute;
        top: 50%;
        left: 2rem;
        transform: translateY(-50%);
        background: #1e1b4b; /* Matches layout bg */
        padding: 0 1rem;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--text-secondary);
    }

    .input-group-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    @media (max-width: 600px) {
        .input-group-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Custom Notifications</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Broadcast news or deep-link users to specific pages</p>
        </div>
        <div class="premium-glass" style="width: 60px; height: 60px; border-radius: 1.2rem; display: flex; align-items: center; justify-content: center;">
            <i data-lucide="send" class="gradient-text" style="width: 30px; height: 30px;"></i>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <i data-lucide="alert-circle"></i>
            <ul style="margin: 0; padding-left: 1rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="notification-card premium-glass">
        <form action="{{ route('admin.notifications.send') }}" method="POST" id="notificationForm" enctype="multipart/form-data">
            @csrf

            <!-- Target Selection -->
            <div class="form-group">
                <label>Recipients</label>
                <div class="radio-group">
                    <label class="radio-item">
                        <input type="radio" name="target_type" value="all" checked onchange="toggleUserSelect()">
                        <span>Broadcast to All</span>
                    </label>
                    <label class="radio-item">
                        <input type="radio" name="target_type" value="single" onchange="toggleUserSelect()">
                        <span>Select Specific User</span>
                    </label>
                </div>
            </div>

            <div class="form-group" id="userSelectGroup" style="display: none;">
                <label for="user_id">User Account</label>
                <select name="user_id" id="user_id" class="form-control">
                    <option value="">-- Search User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->user_name }} (ID: {{ $user->id }})</option>
                    @endforeach
                </select>
            </div>

            <div class="section-divider">
                <span class="section-title">Content Details</span>
            </div>

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="What's happening?" required oninput="updatePreview()">
            </div>

            <div class="form-group">
                <label for="body">Message</label>
                <textarea name="body" id="body" class="form-control" rows="3" placeholder="Enter your notification message here..." required oninput="updatePreview()"></textarea>
            </div>

            <div class="input-group-row">
                <div class="form-group">
                    <label for="image_file">Upload Banner Image</label>
                    <input type="file" name="image_file" id="image_file" class="form-control" accept="image/*" onchange="handleFileUpload(this)">
                    <small style="color: var(--text-secondary); margin-top: 0.5rem; display: block;">Supports JPG, PNG (Max 2MB)</small>
                </div>
                <div class="form-group">
                    <label for="image_url">Or Image URL</label>
                    <input type="url" name="image_url" id="image_url" class="form-control" placeholder="https://..." oninput="updatePreview()">
                </div>
            </div>

            <div class="section-divider">
                <span class="section-title">Action & Navigation</span>
            </div>

            <div class="input-group-row">
                <div class="form-group">
                    <label for="screen">Open Page</label>
                    <select name="screen" id="screen" class="form-control" onchange="updatePreview()">
                        <option value="home">Home Feed</option>
                        <option value="events">All Events</option>
                        <option value="event_details">Specific Event Details</option>
                        <option value="profile">User Profile</option>
                        <option value="subscription">Subscription Page</option>
                        <option value="custom">Custom Screen Name</option>
                    </select>
                </div>
                <div class="form-group" id="itemIdGroup">
                    <label for="item_id" id="itemIdLabel">Item ID / Screen Name</label>
                    <input type="text" name="item_id" id="item_id" class="form-control" placeholder="ID or Screen Path" oninput="updatePreview()">
                </div>
            </div>

            <div style="margin-top: 3rem;">
                <button type="submit" class="gradient-btn" style="width: 100%; padding: 1.2rem; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; gap: 1rem; border-radius: 1rem;">
                    <i data-lucide="zap" style="width: 24px; height: 24px;"></i>
                    Send Push Notification
                </button>
            </div>
        </form>

        <!-- Live Preview -->
        <div class="preview-box">
            <h4 style="margin-bottom: 1.5rem; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--text-secondary); display: flex; align-items: center; gap: 0.5rem;">
                <i data-lucide="smartphone" style="width: 16px;"></i> Mobile Preview
            </h4>
            <div class="preview-notification">
                <div class="app-icon-small">
                    <img src="{{ asset('assets/images/logo-white.png') }}" style="width: 24px;" alt="Logo">
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.3rem;">
                        <strong id="previewTitle" style="font-size: 0.95rem; font-weight: 600;">Flash Sale! ⚡</strong>
                        <span style="font-size: 0.7rem; color: #999;">now</span>
                    </div>
                    <p id="previewBody" style="font-size: 0.85rem; line-height: 1.5; color: #444;">Your custom message will appear exactly like this on user devices.</p>
                    <div id="previewImageContainer" style="margin-top: 0.8rem; display: none;">
                        <img id="previewImage" src="" style="width: 100%; border-radius: 0.8rem; object-fit: cover; max-height: 180px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    </div>
                    <div style="margin-top: 0.8rem; padding-top: 0.8rem; border-top: 1px solid #eee; display: flex; align-items: center; gap: 0.5rem; color: var(--accent-primary); font-size: 0.75rem; font-weight: 600;">
                        <i data-lucide="external-link" style="width: 12px;"></i>
                        Action: <span id="previewAction">Open Home</span>
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
    }

    function handleFileUpload(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('previewImageContainer').style.display = 'block';
                document.getElementById('image_url').value = ''; // Clear URL if file is chosen
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updatePreview() {
        const title = document.getElementById('title').value || 'Notification Title';
        const body = document.getElementById('body').value || 'Your message will appear here...';
        const urlImage = document.getElementById('image_url').value;
        const fileImage = document.getElementById('image_file').files[0];
        
        const screen = document.getElementById('screen');
        const screenText = screen.options[screen.selectedIndex].text;
        const itemId = document.getElementById('item_id').value;

        document.getElementById('previewTitle').innerText = title;
        document.getElementById('previewBody').innerText = body;
        document.getElementById('previewAction').innerText = itemId ? `${screenText} (ID: ${itemId})` : screenText;

        if (!fileImage) {
            const imgContainer = document.getElementById('previewImageContainer');
            const img = document.getElementById('previewImage');

            if (urlImage && (urlImage.startsWith('http'))) {
                img.src = urlImage;
                imgContainer.style.display = 'block';
            } else {
                imgContainer.style.display = 'none';
            }
        }

        // Update Label context
        const itemIdLabel = document.getElementById('itemIdLabel');
        const screenVal = screen.value;
        if (screenVal === 'event_details') itemIdLabel.innerText = 'Event ID';
        else if (screenVal === 'profile') itemIdLabel.innerText = 'User ID';
        else if (screenVal === 'custom') itemIdLabel.innerText = 'Custom Screen Name';
        else itemIdLabel.innerText = 'Optional Parameter';
    }

    // Initialize
    updatePreview();
</script>
@endsection
