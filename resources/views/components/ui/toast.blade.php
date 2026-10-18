<div
    x-data="{
        messages: [],
        add(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.messages.push({ id, message, type });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.messages = this.messages.filter(m => m.id !== id);
        }
    }"
    x-on:notify.window="add($event.detail.message || $event.detail, $event.detail.type || 'success')"
    @if(session('success')) x-init="add(@js(session('success')), 'success')" @endif
    @if(session('error')) x-init="add(@js(session('error')), 'danger')" @endif
    @if(session('status')) x-init="add(@js(session('status')), 'info')" @endif
    class="fixed bottom-5 right-5 z-50 flex flex-col gap-2 max-w-sm w-full pointer-events-none"
>
    <template x-for="item in messages" :key="item.id">
        <div
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="pointer-events-auto flex items-center justify-between p-4 rounded-sm border shadow-xl text-sm"
            :class="{
                'bg-ink-900 border-gold-500/60 text-white': item.type === 'success',
                'bg-ink-900 border-danger text-white': item.type === 'danger',
                'bg-ink-900 border-ink-700 text-ink-300': item.type === 'info'
            }"
        >
            <div class="flex items-center gap-3">
                <template x-if="item.type === 'success'">
                    <span class="w-2 h-2 rounded-full bg-gold-500"></span>
                </template>
                <template x-if="item.type === 'danger'">
                    <span class="w-2 h-2 rounded-full bg-danger"></span>
                </template>
                <p x-text="item.message" class="font-sans font-medium text-xs tracking-wide"></p>
            </div>
            <button @click="remove(item.id)" class="text-ink-500 hover:text-white transition-colors ml-3">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </template>
</div>
