@extends('layouts.app')

@section('title', 'New Notification')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">New Notification</h1>
                    <p class="text-muted mb-0">Create a notification for your subscribers</p>
                </div>
                <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                    ← Back to Notifications
                </a>
            </div>

            <div class="card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('notifications.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">
                                Title <span class="text-danger">*</span>
                                <small class="text-muted">(max 50 characters)</small>
                            </label>
                            <input type="text" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   id="title" 
                                   name="title" 
                                   value="{{ old('title') }}" 
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
                                      required>{{ old('body') }}</textarea>
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
                            @if($organisation->hasFacebookPage() || $organisation->hasXAccount())
                                <button type="button" 
                                        class="btn btn-sm btn-outline-secondary" 
                                        id="toggle-social"
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#social-field">
                                    📣 Share to Social
                                </button>
                            @endif
                        </div>

                        {{-- Link Field (Collapsible) --}}
                        <div class="collapse {{ old('link') ? 'show' : '' }}" id="link-field">
                            <div class="mb-3">
                                <label for="link" class="form-label">Link</label>
                                <input type="url" 
                                       class="form-control @error('link') is-invalid @enderror" 
                                       id="link" 
                                       name="link" 
                                       value="{{ old('link') }}">
                                @error('link')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Schedule Field (Collapsible) --}}
                        <div class="collapse {{ old('scheduled_for') ? 'show' : '' }}" id="schedule-field">
                            <div class="mb-3">
                                <label for="scheduled_for" class="form-label">Schedule For</label>
                                <input type="text" 
                                       class="form-control @error('scheduled_for') is-invalid @enderror" 
                                       id="scheduled_for" 
                                       name="scheduled_for" 
                                       value="{{ old('scheduled_for') }}"
                                       data-datetime-input>
                                <div class="form-text">Select a date and time in the future</div>
                                @error('scheduled_for')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Social Share Field (Collapsible) --}}
                        @if($organisation->hasFacebookPage() || $organisation->hasXAccount())
                            <div class="collapse {{ old('post_to_facebook') || old('post_to_x') ? 'show' : '' }}" id="social-field">
                                <div class="mb-3">
                                    <label class="form-label">Share to Social Media</label>
                                    <div class="d-flex gap-4">
                                        @if($organisation->hasFacebookPage())
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="post_to_facebook" 
                                                       name="post_to_facebook" 
                                                       value="1"
                                                       {{ old('post_to_facebook') ? 'checked' : '' }}>
                                                <label class="form-check-label d-inline-flex align-items-center gap-2" for="post_to_facebook">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#1877F2" viewBox="0 0 24 24">
                                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                                    </svg>
                                                    {{ $organisation->facebook_page_name }}
                                                </label>
                                            </div>
                                        @endif
                                        @if($organisation->hasXAccount())
                                            <div class="form-check">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="post_to_x" 
                                                       name="post_to_x" 
                                                       value="1"
                                                       {{ old('post_to_x') ? 'checked' : '' }}>
                                                <label class="form-check-label d-inline-flex align-items-center gap-2" for="post_to_x">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                                    </svg>
                                                    @{{ $organisation->x_username }}
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="form-text">When you send this notification, it will also be posted to the selected platforms.</div>
                                </div>
                            </div>
                        @endif

                        <hr>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-secondary">
                                Save as Draft
                            </button>
                            <button type="submit" name="send_now" value="1" class="btn btn-primary">
                                Send Now
                            </button>
                        </div>
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

            // Social field toggle (only if elements exist)
            const socialField = document.getElementById('social-field');
            const toggleSocial = document.getElementById('toggle-social');

            if (socialField && toggleSocial) {
                socialField.addEventListener('shown.bs.collapse', () => {
                    toggleSocial.classList.remove('btn-outline-secondary');
                    toggleSocial.classList.add('btn-secondary');
                });
                socialField.addEventListener('hidden.bs.collapse', () => {
                    toggleSocial.classList.remove('btn-secondary');
                    toggleSocial.classList.add('btn-outline-secondary');
                    const fbCheckbox = document.getElementById('post_to_facebook');
                    const xCheckbox = document.getElementById('post_to_x');
                    if (fbCheckbox) fbCheckbox.checked = false;
                    if (xCheckbox) xCheckbox.checked = false;
                });
            }

            // Set initial button states if fields have values
            if (linkField.classList.contains('show')) {
                toggleLink.classList.remove('btn-outline-secondary');
                toggleLink.classList.add('btn-secondary');
            }
            if (scheduleField.classList.contains('show')) {
                toggleSchedule.classList.remove('btn-outline-secondary');
                toggleSchedule.classList.add('btn-secondary');
            }
            if (socialField && socialField.classList.contains('show')) {
                toggleSocial.classList.remove('btn-outline-secondary');
                toggleSocial.classList.add('btn-secondary');
            }
        });
    </script>
    @endpush
@endsection
