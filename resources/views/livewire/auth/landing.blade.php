<div
    class="min-h-screen relative flex flex-col justify-between overflow-hidden bg-ink-950 text-white selection:bg-gold-500 selection:text-ink-950 px-4 py-8 sm:px-6 lg:px-8"
    x-data="{
        pinLength: @js($pinLength),
        digits: Array(@js($pinLength)).fill(''),
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
            // Virtual keypad support for presentation tablets/mobile
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
    <!-- Background Video with Luxury Dark Overlay & Ambient Lighting -->
    <div class="absolute inset-0 pointer-events-none overflow-hidden z-0">
        <video
            autoplay
            loop
            muted
            playsinline
            class="absolute inset-0 w-full h-full object-cover opacity-35 scale-105"
        >
            <source src="{{ asset('videos/hero-background.mp4') }}" type="video/mp4">
        </video>
        <!-- Dark ink gradient overlays for contrast and readability -->
        <div class="absolute inset-0 bg-ink-950/70 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-ink-950 via-ink-950/75 to-ink-950/85"></div>
        <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_center,rgba(20,20,20,0.15)_0%,#0b0b0b_90%)]"></div>

        <!-- Ambient Glow Orbs -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-gold-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-copper-700/15 rounded-full blur-3xl"></div>
    </div>

    <!-- Top Navigation Strip -->
    <header class="relative z-10 max-w-7xl mx-auto w-full flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/kemin-logo.svg') }}" alt="Kemin Exports" class="h-10 w-auto" />
        </div>
        <a
            href="{{ route('admin.login') }}"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-sm border border-ink-800 hover:border-gold-500/50 text-[11px] font-sans uppercase tracking-widest text-ink-300 hover:text-white transition-colors"
        >
            <svg class="w-3.5 h-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Admin</span>
        </a>
    </header>

    <!-- Center Hero & PIN Card -->
    <main class="relative z-10 max-w-xl mx-auto w-full my-auto text-center py-6">
        <!-- Eyebrow & Brand Title -->
        <div class="mb-8">
            <x-ui.eyebrow text="CONFIDENTIAL CLIENT SHOWCASE" class="justify-center mb-3" />
            <h1 class="font-display font-bold uppercase tracking-tight text-white text-5xl sm:text-6xl md:text-7xl leading-none">
                {{ $heroLine1 }}
            </h1>
            <p class="font-display font-light text-ink-300 text-2xl sm:text-3xl mt-1 tracking-wide">
                {{ $heroLine2 }}
            </p>
            <p class="mt-3 text-xs uppercase tracking-[0.2em] text-ink-500">
                {{ $tagline }}
            </p>
        </div>

        <!-- Lockout Notice or PIN Input Container -->
        @if($isLockedOut)
            <div class="bg-ink-900 border border-danger/40 rounded-sm p-6 shadow-2xl relative">
                <div class="w-12 h-12 rounded-full bg-danger/10 border border-danger/40 mx-auto flex items-center justify-center text-danger mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <h3 class="font-display text-lg uppercase tracking-wider text-white">Access Temporarily Suspended</h3>
                <p class="text-xs text-ink-300 mt-2 leading-relaxed">
                    {{ $lockoutMessage }}
                </p>
            </div>
        @else
            <div
                class="bg-ink-900/90 backdrop-blur-md border border-ink-800 rounded-sm p-6 sm:p-8 shadow-2xl relative transition-transform duration-300"
                :class="{ 'animate-shake border-danger/80': shaking }"
            >
                <div class="absolute -top-px left-12 right-12 h-px bg-gradient-to-r from-transparent via-gold-500/50 to-transparent"></div>

                <div class="mb-6">
                    <span class="text-xs uppercase tracking-widest font-sans font-semibold text-ink-300">
                        Enter Presenter Security PIN
                    </span>
                    <p class="text-[11px] text-ink-500 mt-1">
                        Showcase will automatically unlock upon entering the last digit.
                    </p>
                </div>

                @if($errorMessage)
                    <div class="mb-5 p-3 rounded-xs bg-danger/10 border border-danger/40 text-danger text-xs font-sans tracking-wide">
                        {{ $errorMessage }}
                    </div>
                @endif

                <!-- Segmented PIN Digits Box -->
                <div class="flex items-center justify-center gap-2 sm:gap-3.5 mb-6" @paste="handlePaste($event)">
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
                            class="w-11 h-14 sm:w-14 sm:h-16 text-center font-display font-bold text-2xl text-gold-400 bg-ink-800 border rounded-sm transition-all duration-200 focus:outline-none focus:scale-105"
                            :class="{
                                'border-gold-500 ring-2 ring-gold-500/30 bg-ink-800/90': digits[idx] !== '',
                                'border-ink-700 hover:border-ink-600': digits[idx] === '',
                                'border-danger': shaking
                            }"
                            autocomplete="off"
                        />
                    </template>
                </div>

                <!-- On-Screen Keypad for Fast iPad / Tablet Presenter Input -->
                <div class="max-w-[280px] mx-auto grid grid-cols-3 gap-2 pt-2">
                    <template x-for="n in [1, 2, 3, 4, 5, 6, 7, 8, 9]" :key="n">
                        <button
                            type="button"
                            @click="pressKey(n)"
                            class="h-11 rounded-sm bg-ink-800/80 hover:bg-ink-700/80 border border-ink-700 text-white font-display font-medium text-lg hover:border-gold-500/40 active:scale-95 transition-all cursor-pointer"
                            x-text="n"
                        ></button>
                    </template>
                    <button
                        type="button"
                        @click="clearAll()"
                        class="h-11 rounded-sm bg-ink-800/80 hover:bg-ink-700/80 border border-ink-700 text-ink-400 hover:text-white text-[11px] font-sans uppercase tracking-wider active:scale-95 transition-all cursor-pointer"
                    >
                        Clear
                    </button>
                    <button
                        type="button"
                        @click="pressKey(0)"
                        class="h-11 rounded-sm bg-ink-800/80 hover:bg-ink-700/80 border border-ink-700 text-white font-display font-medium text-lg hover:border-gold-500/40 active:scale-95 transition-all cursor-pointer"
                    >
                        0
                    </button>
                    <button
                        type="button"
                        @click="backspaceKey()"
                        class="h-11 rounded-sm bg-ink-800/80 hover:bg-ink-700/80 border border-ink-700 text-ink-400 hover:text-white flex items-center justify-center active:scale-95 transition-all cursor-pointer"
                        aria-label="Backspace"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/>
                        </svg>
                    </button>
                </div>

                <div class="mt-6 flex items-center justify-center gap-2 text-[11px] text-ink-500">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold-500/70"></span>
                    <span>Constant-time Argon2id Encryption</span>
                </div>
            </div>
        @endif
    </main>

    <!-- Bottom Security Bar -->
    <footer class="relative z-10 max-w-7xl mx-auto w-full text-center">
        <p class="text-[10px] uppercase tracking-widest text-ink-500">
            &copy; {{ date('Y') }} Kemin Exports &middot; Internal Sales Presentation System &middot; Strict No-Index
        </p>
    </footer>
</div>
