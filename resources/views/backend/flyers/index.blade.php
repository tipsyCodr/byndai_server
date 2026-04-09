@extends('backend.layouts.app')

@section('content')
<div class="container">
    <div class="header-area" style="margin-bottom: 3rem; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                <a href="{{ route('admin.flyer-categories.index') }}" style="color: var(--text-secondary); text-decoration: none;">Categories</a>
                <i data-lucide="chevron-right" style="width: 14px; color: var(--text-secondary);"></i>
                <span style="color: var(--text-secondary);">{{ $category->name }}</span>
            </div>
            <h1 class="gradient-text" style="font-size: 2.5rem; font-weight: 700; margin-bottom: 0.5rem;">Manage Flyers</h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem;">Upload and manage flyer images for <strong>{{ $category->name }}</strong></p>
        </div>
        <button onclick="document.getElementById('imageUpload').click()" class="gradient-btn" style="padding: 1rem 2rem; border-radius: 1rem; display: flex; align-items: center; gap: 0.8rem;">
            <i data-lucide="upload-cloud" style="width: 20px;"></i>
            Upload Flyers
        </button>
        <form id="uploadForm" action="{{ route('admin.flyers.store') }}" method="POST" enctype="multipart/form-data" style="display: none;">
            @csrf
            <input type="hidden" name="category_id" value="{{ $category->id }}">
            <input type="file" name="images[]" id="imageUpload" multiple onchange="document.getElementById('uploadForm').submit()">
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: rgba(34, 197, 94, 0.15); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.3); padding: 1rem; border-radius: 0.8rem; margin-bottom: 2rem; display: flex; align-items: center; gap: 1rem;">
            <i data-lucide="check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="flyers-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">
        @forelse($flyers as $flyer)
            <div class="premium-glass" style="padding: 0.8rem; border-radius: 1.2rem; position: relative; transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='none'">
                <div style="aspect-ratio: 1/1; border-radius: 0.8rem; overflow: hidden; background: #000; margin-bottom: 1rem;">
                    <img src="{{ asset($flyer->image_path) }}" alt="Flyer" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.75rem; color: var(--text-secondary);">Uploaded {{ $flyer->created_at->diffForHumans() }}</span>
                    <div style="display: flex; gap: 0.5rem;">
                         <form action="{{ route('admin.flyers.destroy', $flyer->id) }}" method="POST" onsubmit="return confirm('Delete this flyer?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: rgba(239, 68, 68, 0.1); border: none; padding: 0.5rem; border-radius: 0.5rem; color: #f87171; cursor: pointer;">
                                <i data-lucide="trash-2" style="width: 16px;"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @if(!$flyer->is_active)
                    <div style="position: absolute; top: 0.8rem; right: 0.8rem; background: rgba(239, 68, 68, 0.8); color: white; padding: 0.2rem 0.6rem; border-radius: 0.5rem; font-size: 0.7rem;">Inactive</div>
                @endif
            </div>
        @empty
            <div class="premium-glass" style="grid-column: 1 / -1; padding: 5rem; text-align: center; color: var(--text-secondary);">
                <i data-lucide="image" style="width: 64px; height: 64px; margin-bottom: 1.5rem; opacity: 0.2;"></i>
                <p style="font-size: 1.2rem;">No flyers found in this category.</p>
                <button onclick="document.getElementById('imageUpload').click()" style="margin-top: 1.5rem; background: none; border: 1px solid var(--accent-primary); color: var(--accent-primary); padding: 0.8rem 1.5rem; border-radius: 0.8rem; cursor: pointer; font-weight: 600;">
                    Click here to upload
                </button>
            </div>
        @endforelse
    </div>
</div>
@endsection
