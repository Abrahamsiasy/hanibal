@php /** @var \App\Models\CityBanner|null $banner */ @endphp

<div class="mb-3">
    <label class="form-label">Title</label>
    <input type="text" name="title" value="{{ old('title', $banner?->title) }}" class="form-control @error('title') is-invalid @enderror" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Subtitle</label>
    <input type="text" name="subtitle" value="{{ old('subtitle', $banner?->subtitle) }}" class="form-control @error('subtitle') is-invalid @enderror">
    @error('subtitle')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Banner Image</label>
    @if ($banner?->image)
        <div class="mb-2">
            <img id="banner_image_preview"
                 src="{{ asset('storage/'.$banner->image) }}"
                 alt="Current banner image"
                 style="max-height:160px;max-width:100%;object-fit:cover;border-radius:6px;border:1px solid #dee2e6">
        </div>
    @else
        <div class="mb-2">
            <img id="banner_image_preview" src="" alt=""
                 style="display:none;max-height:160px;max-width:100%;object-fit:cover;border-radius:6px;border:1px solid #dee2e6">
        </div>
    @endif
    <input type="file" name="image"
           class="form-control @error('image') is-invalid @enderror"
           accept="image/*"
           onchange="previewImage(this, 'banner_image_preview')">
    @if ($banner?->image)
        <div class="form-text text-muted">Upload a new file to replace the current image.</div>
    @endif
    @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Link URL (optional)</label>
    <input type="url" name="link" value="{{ old('link', $banner?->link) }}" class="form-control @error('link') is-invalid @enderror">
    @error('link')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="mb-3">
    <label class="form-label">Position</label>
    <input type="number" name="position" min="0" value="{{ old('position', $banner?->position ?? 0) }}" class="form-control" style="width:100px">
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="active" value="1" id="active" class="form-check-input" @checked(old('active', $banner?->active ?? true))>
    <label for="active" class="form-check-label">Active</label>
</div>
