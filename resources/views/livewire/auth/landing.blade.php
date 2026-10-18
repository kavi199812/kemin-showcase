<div
    class="h-screen w-screen max-h-screen overflow-hidden relative flex flex-col justify-between bg-ink-950 text-white selection:bg-gold-500 selection:text-ink-950 p-2 sm:p-4 lg:p-6"
    x-data="{
        pinLength: @js($pinLength),
        digits: Array(@js($pinLength)).fill(''),
        showKeypad: false,
        shaking: false,
        submitting: false,
        init() {
            this.$nextTick(() => {
                this.focusFirst();
            });
        },
        focusFirst() {
            const first = document.getElementById('pin-digit-0');
            if (first) first.focus();
        },
        handleInput(index, event) {
            const val = event.target.value.replace(/[^0-9]/g, '');
            this.digits[index] = val ? val.slice(-1) : '';

            if (this.digits[index] && index < this.pinLength - 1) {
                const next = document.getElementById(`pin-digit-${index + 1}`);
                if (next) next.focus();
            }

            this.checkAndSubmit();
        },
        handleKeydown(index, event) {
            if (event.key === 'Backspace') {
                if (!this.digits[index] && index > 0) {
                    const prev = document.getElementById(`pin-digit-${index - 1}`);
                    if (prev) {
                        prev.focus();
                        this.digits[index - 1] = '';
                    }
                } else {
                    this.digits[index] = '';
                }
            } else if (event.key === 'ArrowLeft' && index > 0) {
                const prev = document.getElementById(`pin-digit-${index - 1}`);
                if (prev) prev.focus();
            } else if (event.key === 'ArrowRight' && index < this.pinLength - 1) {
                const next = document.getElementById(`pin-digit-${index + 1}`);
                if (next) next.focus();
            }
        },
        handlePaste(event) {
            event.preventDefault();
            const text = (event.clipboardData || window.clipboardData).getData('text').replace(/[^0-9]/g, '');
            if (!text) return;

            for (let i = 0; i < this.pinLength; i++) {
                this.digits[i] = text[i] || '';
            }

            const focusIdx = Math.min(text.length, this.pinLength - 1);
            const target = document.getElementById(`pin-digit-${focusIdx}`);
            if (target) target.focus();

            this.checkAndSubmit();
        },
        pressKey(digit) {
            for (let i = 0; i < this.pinLength; i++) {
                if (!this.digits[i]) {
                    this.digits[i] = digit.toString();
                    if (i < this.pinLength - 1) {
                        const next = document.getElementById(`pin-digit-${i + 1}`);
                        if (next) next.focus();
                    }
                    this.checkAndSubmit();
                    break;
                }
            }
        },
        backspaceKey() {
            for (let i = this.pinLength - 1; i >= 0; i--) {
                if (this.digits[i]) {
                    this.digits[i] = '';
                    const curr = document.getElementById(`pin-digit-${i}`);
                    if (curr) curr.focus();
                    break;
                }
            }
        },
        clearAll() {
            this.digits = Array(this.pinLength).fill('');
            this.focusFirst();
        },
        checkAndSubmit() {
            const entered = this.digits.join('');
            if (entered.length === this.pinLength && !this.submitting) {
                this.submitting = true;
                $wire.verifyPin(entered).then(() => {
                    this.submitting = false;
                }).catch(() => {
                    this.submitting = false;
                });
            }
        },
        triggerShake() {
            this.shaking = true;
            this.clearAll();
            setTimeout(() => {
                this.shaking = false;
            }, 500);
        }
    }"
    x-on:pin-error.window="triggerShake()"
>
    <!-- Full-Screen Video Background with Refined Luxury Overlay -->
    <div class="fixed inset-0 pointer-events-none overflow-hidden z-0">
        <video
            autoplay
            loop
            muted
            playsinline
            class="w-full h-full object-cover opacity-100"
        >
            <source src="{{ asset('videos/hero-background.mp4') }}" type="video/mp4">
        </video>
        <!-- Very light contrast overlay so video is vivid, bright and clearly visible -->
        <div class="absolute inset-0 bg-ink-950/10"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-ink-950/40 via-transparent to-ink-950/25"></div>

        <!-- Ambient Luxury Glow Orbs -->
        <div class="absolute -top-32 -left-32 w-80 h-80 bg-gold-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-32 -right-32 w-80 h-80 bg-copper-700/15 rounded-full blur-3xl"></div>
    </div>

    <!-- Top Navigation Strip -->
    <header class="relative z-10 max-w-7xl mx-auto w-full flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/kemin-logo.webp') }}" alt="Kemin Exports" class="h-12 sm:h-16 md:h-20 max-h-[10vh] w-auto object-contain drop-shadow-[0_4px_16px_rgba(0,0,0,0.85)]" />
        </div>
        <a
            href="{{ route('admin.login') }}"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-sm border border-ink-700 hover:border-gold-500/50 bg-ink-900/60 backdrop-blur-sm text-[10px] sm:text-[11px] font-sans uppercase tracking-widest text-ink-300 hover:text-white transition-colors"
        >
            <svg class="w-3.5 h-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Admin</span>
        </a>
    </header>

    <!-- Center PIN Section (Fitted in viewport with zero scroll) -->
    <main class="relative z-10 max-w-sm sm:max-w-md mx-auto w-full my-auto text-center shrink-0 py-0.5">
        <!-- Lockout Notice or PIN Input Container -->
        @if($isLockedOut)
            <div class="bg-ink-900/95 backdrop-blur-md border border-danger/40 rounded-sm p-4 shadow-2xl relative">
                <div class="w-9 h-9 rounded-full bg-danger/10 border border-danger/40 mx-auto flex items-center justify-center text-danger mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="font-display text-base uppercase tracking-wider text-white">Access Temporarily Suspended</h3>
                <p class="text-xs text-ink-300 mt-1 leading-relaxed">
                    {{ $lockoutMessage }}
                </p>
            </div>
        @else
            <div
                class="bg-ink-900/85 backdrop-blur-md border border-ink-800 rounded-sm p-3 sm:p-4 shadow-2xl relative transition-transform duration-300"
                :class="{ 'animate-shake border-danger/80': shaking }"
            >
                <div class="absolute -top-px left-8 right-8 h-px bg-gradient-to-r from-transparent via-gold-500/50 to-transparent"></div>

                <div class="mb-2">
                    <span class="text-[10px] sm:text-[11px] uppercase tracking-widest font-sans font-semibold text-ink-300">
                        Enter Presenter Security PIN
                    </span>
                </div>

                @if($errorMessage)
                    <div class="mb-2 p-1.5 rounded-xs bg-danger/10 border border-danger/40 text-danger text-xs font-sans tracking-wide">
                        {{ $errorMessage }}
                    </div>
                @endif

                <!-- 4-Digit Segmented PIN Input Box -->
                <div class="flex items-center justify-center gap-2 sm:gap-3 mb-2.5" @paste="handlePaste($event)">
                    <template x-for="(digit, idx) in digits" :key="idx">
                        <input
                            :id="`pin-digit-${idx}`"
                            type="password"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="1"
                            :value="digit"
                            @input="handleInput(idx, $event)"
                            @keydown="handleKeydown(idx, $event)"
                            class="w-10 h-11 sm:w-12 sm:h-13 text-center font-display font-bold text-xl sm:text-2xl text-gold-400 bg-ink-800/95 border rounded-sm transition-all duration-200 focus:outline-none focus:scale-105"
                            :class="{
                                'border-gold-500 ring-2 ring-gold-500/30 bg-ink-800': digits[idx] !== '',
                                'border-ink-700 hover:border-ink-600': digits[idx] === '',
                                'border-danger': shaking
                            }"
                            autocomplete="off"
                        />
                    </template>
                </div>

                <!-- Toggle On-Screen Keypad Button -->
                <div class="mt-2 text-center">
                    <button
                        type="button"
                        @click="showKeypad = !showKeypad"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-sm text-[10px] font-sans uppercase tracking-wider text-ink-400 hover:text-gold-400 hover:bg-ink-800/80 border border-ink-800 hover:border-ink-700/80 transition-all cursor-pointer focus:outline-none"
                    >
                        <svg class="w-3.5 h-3.5 text-gold-500/80" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                        </svg>
                        <span x-text="showKeypad ? 'Hide Keypad' : 'Show Keypad'"></span>
                    </button>
                </div>

                <!-- On-Screen Virtual Keypad (Collapsible) -->
                <div
                    x-show="showKeypad"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="max-w-[220px] sm:max-w-[240px] mx-auto grid grid-cols-3 gap-1 pt-2"
                    style="display: none;"
                >
                    <template x-for="n in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="n">
                        <button
                            type="button"
                            @click="pressKey(n)"
                            class="h-8 sm:h-9 rounded-sm bg-ink-800/80 hover:bg-ink-700/80 border border-ink-700/80 text-white font-display font-medium text-sm sm:text-base hover:border-gold-500/40 active:scale-95 transition-all cursor-pointer"
                            x-text="n"
                        ></button>
                    </template>
                    <button
                        type="button"
                        @click="clearAll()"
                        class="h-8 sm:h-9 rounded-sm bg-ink-800/80 hover:bg-ink-700/80 border border-ink-700/80 text-ink-400 hover:text-white text-[10px] font-sans uppercase tracking-wider active:scale-95 transition-all cursor-pointer"
                    >
                        Clear
                    </button>
                    <button
                        type="button"
                        @click="pressKey(0)"
                        class="h-8 sm:h-9 rounded-sm bg-ink-800/80 hover:bg-ink-700/80 border border-ink-700/80 text-white font-display font-medium text-sm sm:text-base hover:border-gold-500/40 active:scale-95 transition-all cursor-pointer"
                    >
                        0
                    </button>
                    <button
                        type="button"
                        @click="backspaceKey()"
                        class="h-8 sm:h-9 rounded-sm bg-ink-800/80 hover:bg-ink-700/80 border border-ink-700/80 text-ink-400 hover:text-white flex items-center justify-center active:scale-95 transition-all cursor-pointer"
                        aria-label="Backspace"
                    >
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </main>

    <!-- Bottom-Left Brand Lockup (Print. Craft. Deliver. / KEMIN EXPORTS) -->
    <div class="absolute bottom-3 left-4 sm:bottom-6 sm:left-8 md:bottom-8 md:left-10 z-10 pointer-events-none select-none text-left">
        <p class="font-sans font-light text-white text-xs sm:text-base md:text-xl lg:text-2xl tracking-wide drop-shadow-[0_2px_8px_rgba(0,0,0,0.95)]">
            Print. Craft. Deliver.
        </p>
        <h2 class="font-display font-bold uppercase tracking-tight text-3xl sm:text-5xl md:text-6xl lg:text-7xl leading-none mt-0.5 drop-shadow-[0_4px_16px_rgba(0,0,0,0.95)]">
            <span class="text-white">KEMIN</span> <span class="text-kraft">EXPORTS</span>
        </h2>
    </div>

    <!-- Bottom Footer Strip -->
    <footer class="relative z-10 w-full flex items-center justify-end shrink-0 pb-1 pr-2 sm:pr-6">
        <p class="text-[9px] sm:text-[10px] uppercase tracking-widest text-white/90 drop-shadow-[0_1px_4px_rgba(0,0,0,0.9)] font-sans">
            &copy; {{ date('Y') }} Kemin Exports &middot; Internal Sales Showcase &middot; Confidential
        </p>
    </footer>
</div>
