<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $child->name }} Play Mode</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css">
        <style>
            body {
                background: #fef3c7;
                color: #1f2937;
                font-family: Figtree, ui-sans-serif, system-ui, sans-serif;
                margin: 0;
            }

            .play-shell {
                min-height: 100vh;
                padding: 24px;
            }

            .play-header {
                align-items: center;
                display: flex;
                gap: 16px;
                justify-content: space-between;
                margin: 0 auto 24px;
                max-width: 1100px;
            }

            .back-link {
                background: #111827;
                border-radius: 999px;
                color: #fff;
                font-weight: 800;
                padding: 14px 20px;
                text-decoration: none;
            }

            .activity-grid {
                display: grid;
                gap: 18px;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                margin: 0 auto;
                max-width: 1100px;
            }

            .worksheet-strip {
                display: grid;
                gap: 14px;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                margin: 0 auto 28px;
                max-width: 1100px;
            }

            .worksheet-card {
                background: #fff;
                border: 4px solid #111827;
                border-radius: 24px;
                color: #111827;
                display: block;
                font-size: 1.2rem;
                font-weight: 900;
                padding: 18px;
                text-decoration: none;
            }

            .activity-button {
                border: 0;
                border-radius: 28px;
                box-shadow: 0 12px 0 rgba(0, 0, 0, .14);
                color: #fff;
                cursor: pointer;
                display: flex;
                flex-direction: column;
                font-size: 1.45rem;
                font-weight: 900;
                gap: 16px;
                min-height: 210px;
                padding: 24px;
                text-align: left;
                transform: translateY(0);
                transition: transform .15s ease, box-shadow .15s ease;
                width: 100%;
            }

            .activity-button:hover,
            .activity-button:focus {
                box-shadow: 0 6px 0 rgba(0, 0, 0, .18);
                outline: 4px solid rgba(255, 255, 255, .75);
                transform: translateY(6px);
            }

            .activity-icon {
                font-size: 3.25rem;
            }

            .completed {
                background: rgba(255, 255, 255, .24);
                border-radius: 999px;
                font-size: 1rem;
                padding: 8px 12px;
                width: fit-content;
            }
        </style>
    </head>
    <body>
        <main class="play-shell">
            <header class="play-header">
                <div>
                    <h1 style="font-size: clamp(2.25rem, 6vw, 4.5rem); margin: 0;">Hi, {{ $child->name }}!</h1>
                    <p style="font-size: 1.4rem; font-weight: 800; margin: 6px 0 0;">Tap a big button to play.</p>
                </div>
                <a class="back-link" href="{{ route('dashboard') }}"><i class="fas fa-lock mr-1"></i> Parent</a>
            </header>

            @if ($worksheetAssignments->isNotEmpty())
                <section class="worksheet-strip" aria-label="Assigned worksheets">
                    @foreach ($worksheetAssignments as $assignment)
                        <a class="worksheet-card" href="{{ route('worksheets.download', $assignment->worksheet) }}">
                            <i class="fas fa-file-lines" style="color: #2563eb;"></i>
                            {{ $assignment->worksheet->title }}
                            <div style="font-size: .95rem; margin-top: 8px;">
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
                            style="background: {{ $activity->button_color }}"
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
                    <p style="font-size: 1.6rem; font-weight: 800;">Ask your grown-up to add an activity.</p>
                @endforelse
            </section>
        </main>

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
