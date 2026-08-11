@php
    /** @var \App\Models\Event|null $event */
    $event = $event ?? null;
@endphp

<div class="mb-3">
    <label for="title" class="form-label">Title</label>
    <input type="text" name="title" id="title" value="{{ old('title', $event?->title) }}" class="form-control @error('title') is-invalid @enderror" required>
    @error('title')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $event?->description) }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="image" class="form-label">Event Image</label>
    @if ($event?->image)
        <div class="mb-2">
            <img id="event_image_preview"
                 src="{{ asset('storage/'.$event->image) }}"
                 alt="Current event image"
                 style="max-height:200px;max-width:100%;object-fit:cover;border-radius:6px;border:1px solid #dee2e6">
        </div>
    @else
        <div class="mb-2">
            <img id="event_image_preview" src="" alt="" style="display:none;max-height:200px;max-width:100%;object-fit:cover;border-radius:6px;border:1px solid #dee2e6">
        </div>
    @endif
    <input type="file" name="image" id="image"
           class="form-control @error('image') is-invalid @enderror"
           accept="image/*"
           onchange="previewImage(this, 'event_image_preview')">
    @error('image')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    @if ($event?->image)
        <div class="form-text text-muted">Upload a new file to replace the current image.</div>
    @endif
</div>

<div class="mb-3">
    <label for="starts_at" class="form-label">Starts At</label>
    <input type="datetime-local" name="starts_at" id="starts_at"
           value="{{ old('starts_at', $event?->starts_at?->format('Y-m-d\TH:i')) }}"
           class="form-control @error('starts_at') is-invalid @enderror" required>
    @error('starts_at')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="status" class="form-label">Status</label>
    <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
        @foreach ($statuses as $status)
            <option value="{{ $status->value }}" @selected(old('status', $event?->status?->value) === $status->value)>
                {{ $status->label() }}
            </option>
        @endforeach
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<hr>
<h6 class="mb-3">Participants (up to 4)</h6>
@php $existingParticipants = old('participant_names') ? null : ($event?->participants ?? []); @endphp
@for ($i = 0; $i < 4; $i++)
    <div class="row g-2 mb-3 align-items-start">
        <div class="col-md-5">
            <input type="text" name="participant_names[]"
                   value="{{ old("participant_names.{$i}", $existingParticipants[$i]['name'] ?? '') }}"
                   class="form-control @error("participant_names.{$i}") is-invalid @enderror"
                   placeholder="Participant {{ $i + 1 }} name">
        </div>
        <div class="col-md-5">
            @php $existingImg = $existingParticipants[$i]['image'] ?? null; @endphp
            @if ($existingImg)
                <div class="mb-1">
                    <img id="participant_preview_{{ $i }}"
                         src="{{ asset('storage/'.$existingImg) }}"
                         alt="Participant {{ $i + 1 }}"
                         style="width:64px;height:64px;object-fit:cover;border-radius:50%;border:2px solid #dee2e6">
                </div>
            @else
                <div class="mb-1">
                    <img id="participant_preview_{{ $i }}" src="" alt=""
                         style="display:none;width:64px;height:64px;object-fit:cover;border-radius:50%;border:2px solid #dee2e6">
                </div>
            @endif
            <input type="file" name="participant_images[]"
                   class="form-control @error("participant_images.{$i}") is-invalid @enderror"
                   accept="image/*"
                   onchange="previewImage(this, 'participant_preview_{{ $i }}')">
            @if ($existingImg)
                <div class="form-text text-muted">Upload to replace.</div>
            @endif
        </div>
    </div>
@endfor
