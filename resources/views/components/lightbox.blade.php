<div
    x-data="{
        isOpen: false,
        activeIndex: 0,
        images: [],
        open(images, index = 0) {
            this.images = images;
            this.activeIndex = index;
            this.isOpen = true;
            document.body.classList.add('overflow-y-hidden');
        },
        close() {
            this.isOpen = false;
            document.body.classList.remove('overflow-y-hidden');
        },
        next() {
            if (this.images.length > 0) {
                this.activeIndex = (this.activeIndex + 1) % this.images.length;
            }
        },
        prev() {
            if (this.images.length > 0) {
                this.activeIndex = (this.activeIndex - 1 + this.images.length) % this.images.length;
            }
        }
    }"
    x-on:open-lightbox.window="open($event.detail.images, $event.detail.index || 0)"
    x-on:keydown.escape.window="if (isOpen) close()"
    x-on:keydown.arrow-right.window="if (isOpen) next()"
    x-on:keydown.arrow-left.window="if (isOpen) prev()"
    x-show="isOpen"
    style="display: none;"
    class="fixed inset-0 z-50 flex items-center justify-center select-none"
    role="dialog"
    aria-modal="true"
    aria-label="Photo Lightbox"
>
    <!-- Dark Backdrop -->
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="close()"
        class="absolute inset-0 bg-ink-950/95 backdrop-blur-md"
    ></div>

    <!-- Lightbox Controls Header -->
    <div class="absolute top-0 inset-x-0 p-6 flex items-center justify-between z-10">
        <div class="text-xs uppercase tracking-widest text-gold-400 font-sans font-semibold">
            <span x-text="activeIndex + 1"></span> / <span x-text="images.length"></span>
        </div>
        <button
            @click="close()"
            class="p-2 text-ink-300 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-gold-500 rounded-sm"
            aria-label="Close Lightbox"
        >
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Active Image Container -->
    <div class="relative z-10 max-w-5xl max-h-[82vh] px-4 flex items-center justify-center">
        <template x-if="images.length > 0">
            <img
                :src="images[activeIndex]"
                alt="Product preview"
                class="max-w-full max-h-[80vh] object-contain rounded-xs shadow-2xl border border-ink-800"
            />
        </template>
    </div>

    <!-- Navigation Buttons -->
    <template x-if="images.length > 1">
        <div>
            <button
                @click="prev()"
                class="absolute left-6 top-1/2 -translate-y-1/2 z-20 p-3 bg-ink-900/80 hover:bg-ink-800 border border-ink-700 hover:border-gold-500/50 text-white rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-gold-500"
                aria-label="Previous Image"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <button
                @click="next()"
                class="absolute right-6 top-1/2 -translate-y-1/2 z-20 p-3 bg-ink-900/80 hover:bg-ink-800 border border-ink-700 hover:border-gold-500/50 text-white rounded-full transition-all focus:outline-none focus:ring-2 focus:ring-gold-500"
                aria-label="Next Image"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </template>
</div>
