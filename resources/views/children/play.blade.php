<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $child->name }} Play Mode</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
        <link rel="stylesheet" href="{{ asset('css/pages.css') }}">
    </head>
    <body class="play-page">
        <main class="play-shell">
            <header class="play-header">
                <div>
                    <h1 class="play-title">Hi, {{ $child->name }}!</h1>
                    <p class="play-subtitle">Tap a big button to play.</p>
                </div>
                <a class="back-link" href="{{ route('dashboard') }}"><i class="fas fa-lock mr-1"></i> Parent</a>
            </header>

            @if ($worksheetAssignments->isNotEmpty())
                <section class="worksheet-strip" aria-label="Assigned worksheets">
                    @foreach ($worksheetAssignments as $assignment)
                        <a class="worksheet-card" href="{{ route('worksheets.download', $assignment->worksheet) }}">
                            <i class="fas fa-file-lines worksheet-icon"></i>
                            {{ $assignment->worksheet->title }}
                            <div class="worksheet-status">
                                {{ str_replace('_', ' ', ucfirst($assignment->status)) }}
                            </div>
                        </a>
                    @endforeach
                </section>
            @endif

            <section class="activity-grid">
                @forelse ($activities as $activity)
                    <form method="POST" action="{{ route('children.activities.complete', [$child, $activity]) }}" class="play-form">
                        @csrf
                        <button
                            class="activity-button"
                            type="submit"
                            data-activity-color="{{ $activity->button_color }}"
                            data-say="{{ $activity->prompt }}"
                        >
                            <span class="activity-icon">
                                @switch($activity->domain)
                                    @case('literacy') <i class="fas fa-font"></i> @break
                                    @case('numeracy') <i class="fas fa-hashtag"></i> @break
                                    @case('motor') <i class="fas fa-hand-pointer"></i> @break
                                    @default <i class="fas fa-heart"></i>
                                @endswitch
                            </span>
                            <span>{{ $activity->title }}</span>
                            @if (in_array($activity->id, $completedIds, true))
                                <span class="completed"><i class="fas fa-star"></i> Done</span>
                            @endif
                        </button>
                    </form>
                @empty
                    <p class="empty-play-message">Ask your grown-up to add an activity.</p>
                @endforelse
            </section>
        </main>

        <script src="{{ asset('js/pages.js') }}"></script>
        <script>
            document.querySelectorAll('.play-form').forEach((form) => {
                form.addEventListener('submit', (event) => {
                    const button = form.querySelector('[data-say]');

                    if (!button || !('speechSynthesis' in window)) {
                        return;
                    }

                    event.preventDefault();

                    let submitted = false;
                    const submitAfterAudio = () => {
                        if (submitted) {
                            return;
                        }

                        submitted = true;
                        form.submit();
                    };

                    const utterance = new SpeechSynthesisUtterance(button.dataset.say);
                    utterance.rate = 0.82;
                    utterance.onend = submitAfterAudio;
                    window.speechSynthesis.cancel();
                    window.speechSynthesis.speak(utterance);

                    setTimeout(submitAfterAudio, 2500);
                }, { once: true });
            });
        </script>
    </body>
</html>
