@extends('layouts.app')

@section('title', 'Edit Notification')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">Edit Notification</h1>
                    <p class="text-muted mb-0">Update your notification</p>
                </div>
                <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                    ← Back to Notifications
                </a>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('notifications.update', $notification) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">
                                Title <span class="text-danger">*</span>
                                <small class="text-muted">(max 50 characters)</small>
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title', $notification->title) }}" 
                                   maxlength="50"
                                   required
                                   autofocus>
                            <div class="form-text text-end">
                                <span id="title-count">0</span>/50
                            </div>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">
                                Message <span class="text-danger">*</span>
                                <small class="text-muted">(max 280 characters)</small>
                            </label>
                            <textarea class="form-control @error('body') is-invalid @enderror" 
                                      id="body" 
                                      name="body" 
                                      rows="4"
                                      maxlength="280"
                                      required>{{ old('body', $notification->body) }}</textarea>
                            <div class="form-text text-end">
                                <span id="body-count">0</span>/280
                            </div>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Optional Fields Toggle Buttons --}}
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary" 
                                    id="toggle-link"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#link-field">
                                🔗 Add Link
                            </button>
                            <button type="button" 
                                    class="btn btn-sm btn-outline-secondary" 
                                    id="toggle-schedule"
                                    data-bs-toggle="collapse" 
                                    data-bs-target="#schedule-field">
                                📅 Schedule
                            </button>
                        </div>

                        {{-- Link Field (Collapsible) --}}
                        <div class="collapse {{ old('link', $notification->link) ? 'show' : '' }}" id="link-field">
                            <div class="mb-3">
                                <label for="link" class="form-label">Link</label>
                                <input type="url" 
                                       class="form-control @error('link') is-invalid @enderror" 
                                       id="link" 
                                       name="link" 
                                       value="{{ old('link', $notification->link) }}">
                                @error('link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Schedule Field (Collapsible) --}}
                        <div class="collapse {{ old('scheduled_for', $notification->scheduled_for) ? 'show' : '' }}" id="schedule-field">
                            <div class="mb-3">
                                <label for="scheduled_for" class="form-label">Schedule For</label>
                                <input type="datetime-local" 
                                       class="form-control @error('scheduled_for') is-invalid @enderror" 
                                       id="scheduled_for" 
                                       name="scheduled_for" 
                                       value="{{ old('scheduled_for', $notification->scheduled_for?->format('Y-m-d\TH:i')) }}">
                                @error('scheduled_for')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-secondary">
                                Save Changes
                            </button>
                            @if(!$notification->isSent())
                                <button type="submit" name="send_now" value="1" class="btn btn-primary">
                                    Send Now
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-4 border-danger">
                <div class="card-header bg-danger text-white">
                    Danger Zone
                </div>
                <div class="card-body">
                    <p class="mb-3">Deleting a notification cannot be undone.</p>
                    <form action="{{ route('notifications.destroy', $notification) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this notification?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            Delete Notification
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Character counters
            const titleInput = document.getElementById('title');
            const titleCount = document.getElementById('title-count');
            const bodyInput = document.getElementById('body');
            const bodyCount = document.getElementById('body-count');

            function updateCount(input, counter) {
                counter.textContent = input.value.length;
            }

            titleInput.addEventListener('input', () => updateCount(titleInput, titleCount));
            bodyInput.addEventListener('input', () => updateCount(bodyInput, bodyCount));

            // Initial counts
            updateCount(titleInput, titleCount);
            updateCount(bodyInput, bodyCount);

            // Toggle button active states
            const linkField = document.getElementById('link-field');
            const scheduleField = document.getElementById('schedule-field');
            const toggleLink = document.getElementById('toggle-link');
            const toggleSchedule = document.getElementById('toggle-schedule');

            linkField.addEventListener('shown.bs.collapse', () => {
                toggleLink.classList.remove('btn-outline-secondary');
                toggleLink.classList.add('btn-secondary');
            });
            linkField.addEventListener('hidden.bs.collapse', () => {
                toggleLink.classList.remove('btn-secondary');
                toggleLink.classList.add('btn-outline-secondary');
                document.getElementById('link').value = '';
            });

            scheduleField.addEventListener('shown.bs.collapse', () => {
                toggleSchedule.classList.remove('btn-outline-secondary');
                toggleSchedule.classList.add('btn-secondary');
            });
            scheduleField.addEventListener('hidden.bs.collapse', () => {
                toggleSchedule.classList.remove('btn-secondary');
                toggleSchedule.classList.add('btn-outline-secondary');
                document.getElementById('scheduled_for').value = '';
            });

            // Set initial button states if fields have values
            if (linkField.classList.contains('show')) {
                toggleLink.classList.remove('btn-outline-secondary');
                toggleLink.classList.add('btn-secondary');
            }
            if (scheduleField.classList.contains('show')) {
                toggleSchedule.classList.remove('btn-outline-secondary');
                toggleSchedule.classList.add('btn-secondary');
            }
        });
    </script>
    @endpush
@endsection
