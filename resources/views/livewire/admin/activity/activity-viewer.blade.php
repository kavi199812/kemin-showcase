<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-display font-bold uppercase text-2xl sm:text-3xl text-white">
                Audit Activity Log
            </h1>
            <p class="text-xs uppercase tracking-widest text-ink-400 mt-1">
                Spatie activity logging &middot; Tracking internal changes to products, companies, and categories
            </p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-ink-900 border border-ink-800 rounded-sm p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <input
                type="text"
                wire:model.live.debounce.250ms="q"
                placeholder="Search event or model..."
                class="w-full sm:w-72 bg-ink-800 border border-ink-700 rounded-sm px-4 py-2 text-xs text-white placeholder-ink-500 focus:outline-none focus:border-gold-500"
            />

            <select
                wire:model.live="event"
                class="bg-ink-800 border border-ink-700 rounded-sm px-3 py-2 text-xs text-ink-300 focus:outline-none focus:border-gold-500"
            >
                <option value="">All Events</option>
                <option value="created">Created</option>
                <option value="updated">Updated</option>
                <option value="deleted">Deleted</option>
            </select>
        </div>

        <div class="text-xs text-ink-400">
            Total Logged Events: <span class="text-white font-bold">{{ $activities->total() }}</span>
        </div>
    </div>

    <!-- Activity Log Table -->
    <div class="bg-ink-900 border border-ink-800 rounded-sm overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-ink-950 border-b border-ink-800 text-[11px] font-sans font-semibold uppercase tracking-wider text-ink-400">
                    <tr>
                        <th class="py-3.5 px-4 w-28">Event</th>
                        <th class="py-3.5 px-4">Description</th>
                        <th class="py-3.5 px-4">Subject</th>
                        <th class="py-3.5 px-4">Causer</th>
                        <th class="py-3.5 px-4">Changes (Properties)</th>
                        <th class="py-3.5 px-4 text-right">Timestamp</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-800 text-ink-300">
                    @forelse($activities as $act)
                        <tr class="hover:bg-ink-800/40 transition-colors" wire:key="act-{{ $act->id }}">
                            <td class="py-3 px-4">
                                @php
                                $badgeColor = match($act->event) {
                                    'created' => 'bg-success/15 border-success/40 text-success',
                                    'updated' => 'bg-gold-500/15 border-gold-500/40 text-gold-400',
                                    'deleted' => 'bg-danger/15 border-danger/40 text-danger',
                                    default => 'bg-ink-800 border-ink-700 text-ink-300',
                                };
                                @endphp
                                <span class="px-2 py-0.5 rounded-xs text-[10px] uppercase font-semibold border {{ $badgeColor }}">
                                    {{ $act->event ?? 'logged' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-semibold text-white">
                                {{ $act->description }}
                            </td>
                            <td class="py-3 px-4 font-mono text-ink-400 text-[11px]">
                                {{ class_basename($act->subject_type ?? '') }} #{{ $act->subject_id ?? '—' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-white">{{ $act->causer?->name ?? 'System' }}</span>
                                @if($act->causer?->email)
                                    <span class="text-[10px] text-ink-500 block">{{ $act->causer->email }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 max-w-xs truncate font-mono text-[10px] text-ink-400">
                                @if(!empty($act->properties['attributes']))
                                    {{ json_encode($act->properties['attributes']) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-ink-400 text-[11px]">
                                {{ $act->created_at->format('M d, Y H:i:s') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-ink-500">
                                No activity records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="p-4 border-t border-ink-800">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
