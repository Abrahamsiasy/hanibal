@php
    /** @var \App\Models\City|null $city */
    $city = $city ?? null;
@endphp

<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" name="name" id="name" value="{{ old('name', $city?->name) }}" class="form-control @error('name') is-invalid @enderror" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="slug" class="form-label">Slug</label>
    <input type="text" name="slug" id="slug" value="{{ old('slug', $city?->slug) }}" class="form-control @error('slug') is-invalid @enderror" required>
    @error('slug')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="hero_title" class="form-label">Hero Title</label>
    <input type="text" name="hero_title" id="hero_title" value="{{ old('hero_title', $city?->hero_title) }}" class="form-control @error('hero_title') is-invalid @enderror">
    @error('hero_title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="hero_subtitle" class="form-label">Hero Subtitle</label>
    <input type="text" name="hero_subtitle" id="hero_subtitle" value="{{ old('hero_subtitle', $city?->hero_subtitle) }}" class="form-control @error('hero_subtitle') is-invalid @enderror">
    @error('hero_subtitle')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="hero_image" class="form-label">Hero Image</label>
    @if ($city?->hero_image)
        <div class="mb-2">
            <img id="hero_image_preview"
                 src="{{ asset('storage/'.$city->hero_image) }}"
                 alt="Current hero image"
                 style="max-height:180px;max-width:100%;object-fit:cover;border-radius:6px;border:1px solid #dee2e6">
        </div>
    @else
        <div class="mb-2">
            <img id="hero_image_preview" src="" alt="" style="display:none;max-height:180px;max-width:100%;object-fit:cover;border-radius:6px;border:1px solid #dee2e6">
        </div>
    @endif
    <input type="file" name="hero_image" id="hero_image"
           class="form-control @error('hero_image') is-invalid @enderror"
           accept="image/*"
           onchange="previewImage(this, 'hero_image_preview')">
    @error('hero_image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($city?->hero_image)
        <div class="form-text text-muted">Upload a new file to replace the current image.</div>
    @endif
</div>

<div class="mb-3">
    <label for="venue" class="form-label">Venue</label>
    <input type="text" name="venue" id="venue" value="{{ old('venue', $city?->venue) }}" class="form-control @error('venue') is-invalid @enderror" placeholder="e.g. Millennium Hall">
    @error('venue')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="active" id="active" value="1" class="form-check-input" @checked(old('active', $city?->active ?? true))>
    <label for="active" class="form-check-label">Active</label>
</div>
