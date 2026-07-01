<x-app-layout>
    <x-slot name="header">
        <div class="row align-items-center">
            <div class="col-sm-8">
                <h1 class="m-0">Find and Search</h1>
                <p class="text-muted mb-0">Find the hidden letters in the room.</p>
            </div>
            <div class="col-sm-4 text-sm-right mt-3 mt-sm-0">
                <button class="btn btn-outline-primary" type="button" id="resetFindSearch">
                    <i class="fas fa-rotate-right mr-1"></i> New Round
                </button>
            </div>
        </div>
    </x-slot>

    <div class="find-search-game" data-find-search-game>
        <section class="find-search-panel">
            <div>
                <span class="find-search-label">Find</span>
                <div class="find-search-target" id="currentLetter">A</div>
            </div>
            <div>
                <span class="find-search-label">Score</span>
                <div class="find-search-score"><span id="foundCount">0</span>/<span id="totalCount">0</span></div>
            </div>
            <button class="btn btn-warning find-search-hint" type="button" id="hintButton">
                <i class="fas fa-lightbulb mr-1"></i> Hint
            </button>
            <button class="btn btn-outline-secondary find-search-sound" type="button" id="soundButton" aria-pressed="true">
                <i class="fas fa-volume-high mr-1"></i> Sound On
            </button>
            <div class="find-search-message" id="gameMessage" aria-live="polite">Tap the hidden letter.</div>
        </section>

        <section class="find-search-board" aria-label="Room hide and seek letter game">
            <img src="{{ asset('kidkinder/img/game1.jpg') }}" alt="Cartoon room with furniture hiding letters">

            <button class="hidden-letter" type="button" data-letter="A" style="--x: 34.4%; --y: 78.8%;" aria-label="Letter A hidden near the table">A</button>
            <button class="hidden-letter" type="button" data-letter="B" style="--x: 73.2%; --y: 23.2%;" aria-label="Letter B hidden on the bookshelf">B</button>
            <button class="hidden-letter" type="button" data-letter="C" style="--x: 5.7%; --y: 24.3%;" aria-label="Letter C hidden in the left curtain">C</button>
            <button class="hidden-letter" type="button" data-letter="D" style="--x: 47.2%; --y: 61.6%;" aria-label="Letter D hidden near the cabinet">D</button>
            <button class="hidden-letter" type="button" data-letter="E" style="--x: 67.6%; --y: 55.7%;" aria-label="Letter E hidden on the bed pillow">E</button>
            <button class="hidden-letter" type="button" data-letter="F" style="--x: 18.6%; --y: 35.3%;" aria-label="Letter F hidden in the window">F</button>
            <button class="hidden-letter" type="button" data-letter="G" style="--x: 29.9%; --y: 58.5%;" aria-label="Letter G hidden on the sofa">G</button>
            <button class="hidden-letter" type="button" data-letter="H" style="--x: 87.7%; --y: 29.3%;" aria-label="Letter H hidden near the wall picture">H</button>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const game = document.querySelector('[data-find-search-game]');

            if (!game) {
                return;
            }

            const letters = Array.from(game.querySelectorAll('.hidden-letter'));
            const currentLetter = document.getElementById('currentLetter');
            const foundCount = document.getElementById('foundCount');
            const totalCount = document.getElementById('totalCount');
            const message = document.getElementById('gameMessage');
            const hintButton = document.getElementById('hintButton');
            const soundButton = document.getElementById('soundButton');
            const resetButton = document.getElementById('resetFindSearch');
            let remaining = [];
            let hintTimer = null;
            let audioContext = null;
            let soundEnabled = true;

            const getAudioContext = () => {
                if (!soundEnabled) {
                    return null;
                }

                const AudioContext = window.AudioContext || window.webkitAudioContext;

                if (!AudioContext) {
                    return null;
                }

                if (!audioContext) {
                    audioContext = new AudioContext();
                }

                if (audioContext.state === 'suspended') {
                    audioContext.resume();
                }

                return audioContext;
            };

            const playTone = (frequency, startAt, duration, type = 'sine', volume = 0.16) => {
                const context = getAudioContext();

                if (!context) {
                    return;
                }

                const oscillator = context.createOscillator();
                const gain = context.createGain();
                const start = context.currentTime + startAt;

                oscillator.type = type;
                oscillator.frequency.setValueAtTime(frequency, start);
                gain.gain.setValueAtTime(0.001, start);
                gain.gain.exponentialRampToValueAtTime(volume, start + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.001, start + duration);

                oscillator.connect(gain);
                gain.connect(context.destination);
                oscillator.start(start);
                oscillator.stop(start + duration + 0.03);
            };

            const playSound = (name) => {
                const sounds = {
                    correct: [
                        [523.25, 0, .12],
                        [659.25, .09, .14],
                        [783.99, .2, .18],
                    ],
                    missed: [
                        [220, 0, .1, 'triangle', .1],
                        [164.81, .1, .16, 'triangle', .08],
                    ],
                    hint: [
                        [987.77, 0, .08, 'sine', .1],
                        [1318.51, .08, .1, 'sine', .09],
                        [1567.98, .17, .12, 'sine', .08],
                    ],
                    complete: [
                        [523.25, 0, .12],
                        [659.25, .1, .12],
                        [783.99, .2, .12],
                        [1046.5, .33, .28],
                    ],
                    reset: [
                        [392, 0, .1, 'sine', .09],
                        [523.25, .08, .12, 'sine', .09],
                    ],
                };

                (sounds[name] || []).forEach(([frequency, startAt, duration, type, volume]) => {
                    playTone(frequency, startAt, duration, type, volume);
                });
            };

            const shuffle = (items) => items
                .map((item) => ({ item, order: Math.random() }))
                .sort((left, right) => left.order - right.order)
                .map(({ item }) => item);

            const updateTarget = () => {
                foundCount.textContent = letters.length - remaining.length;
                totalCount.textContent = letters.length;

                if (remaining.length === 0) {
                    currentLetter.textContent = 'Done';
                    message.textContent = 'You found every letter. Great searching!';
                    hintButton.disabled = true;
                    playSound('complete');
                    return;
                }

                currentLetter.textContent = remaining[0].dataset.letter;
                hintButton.disabled = false;
            };

            const clearHints = () => {
                window.clearTimeout(hintTimer);
                letters.forEach((letter) => letter.classList.remove('is-hinting'));
            };

            const startRound = () => {
                clearHints();
                remaining = shuffle(letters);
                letters.forEach((letter) => {
                    letter.classList.remove('is-found', 'is-missed');
                    letter.disabled = false;
                });
                message.textContent = 'Tap the hidden letter.';
                updateTarget();
            };

            letters.forEach((letter) => {
                letter.addEventListener('click', () => {
                    if (letter.dataset.letter !== currentLetter.textContent) {
                        letter.classList.add('is-missed');
                        message.textContent = 'Good try. Look for ' + currentLetter.textContent + '.';
                        playSound('missed');
                        setTimeout(() => letter.classList.remove('is-missed'), 450);
                        return;
                    }

                    clearHints();
                    letter.classList.add('is-found');
                    letter.disabled = true;
                    remaining = remaining.filter((item) => item !== letter);
                    message.textContent = 'Yes! You found ' + letter.dataset.letter + '.';
                    playSound('correct');
                    updateTarget();
                });
            });

            hintButton.addEventListener('click', () => {
                clearHints();

                if (remaining.length === 0) {
                    return;
                }

                remaining[0].classList.add('is-hinting');
                message.textContent = 'The letter is shining for a moment.';
                playSound('hint');
                hintTimer = window.setTimeout(clearHints, 3000);
            });

            soundButton.addEventListener('click', () => {
                soundEnabled = !soundEnabled;
                soundButton.setAttribute('aria-pressed', soundEnabled ? 'true' : 'false');
                soundButton.innerHTML = soundEnabled
                    ? '<i class="fas fa-volume-high mr-1"></i> Sound On'
                    : '<i class="fas fa-volume-xmark mr-1"></i> Sound Off';

                if (soundEnabled) {
                    playSound('hint');
                }
            });

            resetButton.addEventListener('click', () => {
                startRound();
                playSound('reset');
            });
            startRound();
        });
    </script>
</x-app-layout>
