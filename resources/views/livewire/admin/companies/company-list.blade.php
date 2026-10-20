<div class="space-y-6">
    <!-- Header & Action Row -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-display font-bold uppercase text-2xl sm:text-3xl text-white">
                Partner Companies Management
            </h1>
            <p class="text-xs uppercase tracking-widest text-ink-400 mt-1">
                Manage commercial client logos, industries, websites, and supply profiles
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.bulk-import') }}" class="px-3.5 py-2 text-xs font-semibold uppercase tracking-wider text-gold-400 hover:text-white bg-ink-900 hover:bg-ink-800 border border-gold-500/30 hover:border-gold-500 rounded-sm transition-all flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                <span>Bulk Import</span>
            </a>
            <a href="{{ route('admin.companies.create') }}" class="btn-primary-kemin text-xs py-2 px-5">
                + Add Company
            </a>
        </div>
    </div>

    <!-- Filters Row -->
    <div class="bg-ink-900 border border-ink-800 rounded-sm p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
            <input
                type="text"
                wire:model.live.debounce.250ms="q"
                placeholder="Search companies, industries..."
                class="w-full bg-ink-800 border border-ink-700 rounded-sm px-4 py-2 pl-9 text-xs text-white placeholder-ink-500 focus:outline-none focus:border-gold-500"
            />
            <svg class="w-4 h-4 text-ink-500 absolute left-3 top-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <div class="text-xs text-ink-400">
            Total: <span class="text-white font-bold">{{ $companies->total() }}</span>
        </div>
    </div>

    <!-- Companies Table -->
    <div class="bg-ink-900 border border-ink-800 rounded-sm overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-ink-950 border-b border-ink-800 text-[11px] font-sans font-semibold uppercase tracking-wider text-ink-400">
                    <tr>
                        <th class="py-3.5 px-4 w-16">Logo</th>
                        <th class="py-3.5 px-4">Company &amp; Industry</th>
                        <th class="py-3.5 px-4">Country</th>
                        <th class="py-3.5 px-4 text-center">Products Supplied</th>
                        <th class="py-3.5 px-4 text-center">Featured</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink-800 text-ink-300">
                    @forelse($companies as $company)
                        <tr class="hover:bg-ink-800/40 transition-colors" wire:key="row-{{ $company->id }}">
                            <td class="py-3 px-4">
                                <div class="w-12 h-10 bg-white rounded-xs overflow-hidden flex items-center justify-center p-1 border border-neutral-300">
                                    <img src="{{ $company->getLogoUrl('thumb') }}" alt="" class="max-h-full max-w-full object-contain">
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.companies.edit', $company->id) }}" class="font-semibold text-white hover:text-gold-400 text-sm transition-colors block">
                                    {{ $company->name }}
                                </a>
                                @if($company->industry)
                                    <span class="text-[10px] text-gold-500 uppercase tracking-wider mt-0.5 block">
                                        {{ $company->industry }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-ink-400">
                                {{ $company->country ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-center font-mono">
                                <span class="px-2 py-0.5 rounded-full bg-ink-800 text-ink-300 border border-ink-700">
                                    {{ $company->products_count }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button
                                    type="button"
                                    wire:click="toggleFeatured({{ $company->id }})"
                                    class="p-1 rounded-sm cursor-pointer transition-colors {{ $company->is_featured ? 'text-gold-400 hover:text-gold-300' : 'text-ink-600 hover:text-ink-400' }}"
                                    title="Toggle Featured"
                                >
                                    <svg class="w-5 h-5" fill="{{ $company->is_featured ? 'currentColor' : 'none' }}" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                </button>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button
                                    type="button"
                                    wire:click="toggleActive({{ $company->id }})"
                                    class="px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider rounded-xs cursor-pointer border transition-colors {{ $company->is_active ? 'bg-success/15 border-success/40 text-success' : 'bg-ink-800 border-ink-700 text-ink-500' }}"
                                >
                                    {{ $company->is_active ? 'Active' : 'Draft' }}
                                </button>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <a
                                    href="{{ route('showcase.companies.show', $company->slug) }}"
                                    target="_blank"
                                    class="text-ink-400 hover:text-white p-1 inline-block"
                                    title="View in Showcase"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>

                                <a
                                    href="{{ route('admin.companies.edit', $company->id) }}"
                                    class="text-gold-400 hover:text-gold-300 p-1 inline-block"
                                    title="Edit Company"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                    </svg>
                                </a>

                                <button
                                    type="button"
                                    wire:click="confirmDelete({{ $company->id }})"
                                    class="text-danger hover:text-red-400 p-1 inline-block cursor-pointer"
                                    title="Delete Company"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-ink-500">
                                No partner companies found. Click "+ Add Company" to create one.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($companies->hasPages())
            <div class="p-4 border-t border-ink-800">
                {{ $companies->links() }}
            </div>
        @endif
    </div>

    <!-- Delete Confirmation Modal -->
    <x-ui.modal name="confirm-delete-company" maxWidth="sm">
        <div class="p-6 text-center space-y-4">
            <div class="w-12 h-12 rounded-full bg-danger/10 border border-danger/30 text-danger mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="font-display text-lg uppercase tracking-wider text-white">Delete Partner Company?</h3>
            <p class="text-xs text-ink-400">
                Are you sure you want to remove this company? Any product relationship links will be detached.
            </p>
            <div class="flex items-center justify-center gap-3 pt-2">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', 'confirm-delete-company')"
                    class="px-4 py-2 bg-ink-800 hover:bg-ink-700 text-white rounded-sm text-xs uppercase tracking-wider cursor-pointer"
                >
                    Cancel
                </button>
                <button
                    type="button"
                    wire:click="deleteCompany"
                    class="px-4 py-2 bg-danger hover:bg-red-700 text-white rounded-sm text-xs uppercase tracking-wider font-semibold cursor-pointer"
                >
                    Delete Company
                </button>
            </div>
        </div>
    </x-ui.modal>
</div>
