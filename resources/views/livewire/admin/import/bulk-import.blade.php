<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-ink-800">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold uppercase tracking-wider bg-gold-500/10 text-gold-400 border border-gold-500/30">
                    Bulk Data Manager
                </span>
            </div>
            <h1 class="font-display font-bold uppercase text-2xl sm:text-3xl text-white mt-1">
                CSV Bulk Import &amp; Media Attach
            </h1>
            <p class="text-xs uppercase tracking-widest text-ink-400 mt-1">
                Upload hundreds of Products or Partner Companies instantly with automated photo matching
            </p>
        </div>

        <!-- Quick navigation links -->
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.products.index') }}" class="px-3 py-2 text-xs font-sans uppercase tracking-wider text-ink-300 hover:text-white bg-ink-900 hover:bg-ink-800 border border-ink-800 rounded-sm transition-colors">
                &larr; Products
            </a>
            <a href="{{ route('admin.companies.index') }}" class="px-3 py-2 text-xs font-sans uppercase tracking-wider text-ink-300 hover:text-white bg-ink-900 hover:bg-ink-800 border border-ink-800 rounded-sm transition-colors">
                &larr; Companies
            </a>
        </div>
    </div>

    <!-- Mode Tabs -->
    <div class="flex items-center space-x-2 border-b border-ink-800">
        <button
            type="button"
            wire:click="switchTab('products')"
            class="px-5 py-3 text-xs font-display font-semibold uppercase tracking-wider transition-all border-b-2 flex items-center gap-2 cursor-pointer {{ $activeTab === 'products' ? 'text-gold-400 border-gold-500 bg-ink-900/60' : 'text-ink-400 border-transparent hover:text-ink-200 hover:border-ink-700' }}"
        >
            <svg class="w-4 h-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <span>Products Bulk Import</span>
        </button>

        <button
            type="button"
            wire:click="switchTab('companies')"
            class="px-5 py-3 text-xs font-display font-semibold uppercase tracking-wider transition-all border-b-2 flex items-center gap-2 cursor-pointer {{ $activeTab === 'companies' ? 'text-gold-400 border-gold-500 bg-ink-900/60' : 'text-ink-400 border-transparent hover:text-ink-200 hover:border-ink-700' }}"
        >
            <svg class="w-4 h-4 text-gold-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Companies Bulk Import</span>
        </button>
    </div>

    <!-- Results Panel (Visible after import) -->
    @if($resultSummary)
        <div class="bg-ink-900 border border-gold-500/30 rounded-sm p-6 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-48 h-48 bg-gold-500/5 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex items-center justify-between pb-4 border-b border-ink-800 mb-6">
                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">
                        &check;
                    </span>
                    <div>
                        <h3 class="font-display font-bold uppercase text-lg text-white">
                            Import Execution Completed
                        </h3>
                        <p class="text-xs text-ink-400">
                            Processed {{ $resultSummary['total'] }} records from CSV file.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    wire:click="clearResults"
                    class="text-xs uppercase tracking-wider text-ink-400 hover:text-white px-3 py-1.5 bg-ink-800 hover:bg-ink-700 rounded-sm transition-colors cursor-pointer"
                >
                    Dismiss
                </button>
            </div>

            <!-- Metric summary cards -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                <div class="bg-ink-950/70 border border-ink-800 p-3 rounded-sm">
                    <div class="text-[10px] uppercase tracking-wider text-ink-400">Total Rows</div>
                    <div class="text-xl font-display font-bold text-white mt-1">{{ $resultSummary['total'] }}</div>
                </div>
                <div class="bg-ink-950/70 border border-ink-800 p-3 rounded-sm">
                    <div class="text-[10px] uppercase tracking-wider text-emerald-400">Created New</div>
                    <div class="text-xl font-display font-bold text-emerald-400 mt-1">+{{ $resultSummary['created'] }}</div>
                </div>
                <div class="bg-ink-950/70 border border-ink-800 p-3 rounded-sm">
                    <div class="text-[10px] uppercase tracking-wider text-sky-400">Updated Existing</div>
                    <div class="text-xl font-display font-bold text-sky-400 mt-1">{{ $resultSummary['updated'] }}</div>
                </div>
                <div class="bg-ink-950/70 border border-ink-800 p-3 rounded-sm">
                    <div class="text-[10px] uppercase tracking-wider text-gold-400">Media Attached</div>
                    <div class="text-xl font-display font-bold text-gold-400 mt-1">{{ $resultSummary['images_attached'] }}</div>
                </div>
                <div class="bg-ink-950/70 border border-ink-800 p-3 rounded-sm">
                    <div class="text-[10px] uppercase tracking-wider text-amber-400">Skipped</div>
                    <div class="text-xl font-display font-bold text-amber-400 mt-1">{{ $resultSummary['skipped'] }}</div>
                </div>
            </div>

            <!-- Error and Warning Logs if any -->
            @if(! empty($resultSummary['errors']) || ! empty($resultSummary['warnings']))
                <div class="mt-6 space-y-3">
                    @if(! empty($resultSummary['errors']))
                        <div class="bg-red-950/40 border border-red-800/60 p-4 rounded-sm">
                            <h4 class="text-xs font-semibold text-red-400 uppercase tracking-wider mb-2">Errors Encountered ({{ count($resultSummary['errors']) }}):</h4>
                            <ul class="list-disc list-inside space-y-1 text-xs text-red-300">
                                @foreach($resultSummary['errors'] as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(! empty($resultSummary['warnings']))
                        <div class="bg-amber-950/30 border border-amber-800/50 p-4 rounded-sm">
                            <h4 class="text-xs font-semibold text-amber-400 uppercase tracking-wider mb-2">Notices / Skipped Rows:</h4>
                            <ul class="list-disc list-inside space-y-1 text-xs text-amber-300">
                                @foreach($resultSummary['warnings'] as $warn)
                                    <li>{{ $warn }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form Column (2 spans) -->
        <div class="lg:col-span-2 space-y-6">
            <form wire:submit.prevent="import" class="space-y-6">
                <!-- Step 1: CSV Upload -->
                <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-ink-800">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-gold-500 text-ink-950 text-xs font-bold flex items-center justify-center">1</span>
                            <h2 class="font-display font-bold uppercase text-base text-white">
                                Select CSV File
                            </h2>
                        </div>
                        <span class="text-[10px] uppercase tracking-wider text-ink-400">Required</span>
                    </div>

                    <p class="text-xs text-ink-400">
                        Upload your prepared <code>.csv</code> spreadsheet containing {{ $activeTab === 'products' ? 'product names, categories, specifications, and job numbers' : 'company profiles, industries, and websites' }}.
                    </p>

                    <div class="relative border-2 border-dashed {{ $errors->has('csvFile') ? 'border-red-500' : 'border-ink-700 hover:border-gold-500/70' }} rounded-sm p-6 text-center transition-colors bg-ink-950/40">
                        <input
                            type="file"
                            wire:model="csvFile"
                            accept=".csv,text/csv,text/plain"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                        />
                        <div class="flex flex-col items-center justify-center pointer-events-none">
                            <svg class="w-10 h-10 text-gold-500/70 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            @if($csvFile)
                                <div class="text-xs text-emerald-400 font-semibold flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    <span>{{ $csvFile->getClientOriginalName() }}</span>
                                    <span class="text-ink-400">({{ round($csvFile->getSize() / 1024, 1) }} KB)</span>
                                </div>
                                <span class="text-[11px] text-ink-500 mt-1">Click or drop to replace file</span>
                            @else
                                <span class="text-xs text-white font-medium">Click to browse or drop .CSV file here</span>
                                <span class="text-[11px] text-ink-500 mt-1">UTF-8 encoded comma-separated file (up to 15MB)</span>
                            @endif
                        </div>
                    </div>

                    @error('csvFile')
                        <div class="text-xs text-red-400 mt-1 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    @if($csvFile)
                        <div class="flex justify-end">
                            <button
                                type="button"
                                wire:click="removeCsv"
                                class="text-[11px] uppercase tracking-wider text-red-400 hover:text-red-300 cursor-pointer"
                            >
                                Remove file
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Step 2: Images / Media (Multi-file or ZIP) -->
                <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-ink-800">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-gold-500 text-ink-950 text-xs font-bold flex items-center justify-center">2</span>
                            <h2 class="font-display font-bold uppercase text-base text-white">
                                Attach Images / Media
                            </h2>
                        </div>
                        <span class="text-[10px] uppercase tracking-wider text-gold-400 font-semibold bg-gold-500/10 px-2 py-0.5 rounded border border-gold-500/20">
                            Optional &bull; Auto-Matched
                        </span>
                    </div>

                    <p class="text-xs text-ink-400">
                        Images are automatically matched by <strong class="text-gold-400">CSV image filename</strong>, <strong class="text-gold-400">Job Number</strong> (e.g. <code>JB-1001.jpg</code>), or <strong class="text-gold-400">Slug</strong>. You can upload via either method below:
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <!-- Option A: Multiple Image Files -->
                        <div class="bg-ink-950/60 border border-ink-800 rounded-sm p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-white">Method A: Select Images</span>
                                <span class="text-[10px] text-ink-500">Multiple files</span>
                            </div>
                            <p class="text-[11px] text-ink-400 leading-relaxed">
                                Select or drop multiple images together (JPG, PNG, WebP, SVG).
                            </p>

                            <div class="relative border border-dashed border-ink-700 hover:border-gold-500/60 rounded-sm p-4 text-center bg-ink-900/60 transition-colors">
                                <input
                                    type="file"
                                    wire:model="imageFiles"
                                    multiple
                                    accept="image/jpeg,image/png,image/webp,image/svg+xml"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                />
                                <div class="pointer-events-none">
                                    <svg class="w-6 h-6 text-ink-400 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs text-ink-300 font-medium">Choose Photos</span>
                                    <div class="text-[10px] text-ink-500 mt-0.5">Hold Shift or Ctrl to select multiple</div>
                                </div>
                            </div>

                            @if(! empty($imageFiles))
                                <div class="text-xs text-gold-400 font-semibold flex items-center justify-between">
                                    <span>{{ count($imageFiles) }} photos selected</span>
                                    <button type="button" wire:click="$set('imageFiles', [])" class="text-[10px] text-red-400 hover:underline">Clear all</button>
                                </div>
                                <div class="max-h-24 overflow-y-auto space-y-1 pr-1 text-[11px] text-ink-400">
                                    @foreach(array_slice($imageFiles, 0, 8) as $img)
                                        <div class="truncate">&bull; {{ $img->getClientOriginalName() }}</div>
                                    @endforeach
                                    @if(count($imageFiles) > 8)
                                        <div class="text-ink-500 text-[10px] italic">...and {{ count($imageFiles) - 8 }} more</div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Option B: Single ZIP Archive -->
                        <div class="bg-ink-950/60 border border-ink-800 rounded-sm p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-white">Method B: Upload as .ZIP</span>
                                <span class="text-[10px] text-ink-500">Best for 50+ files</span>
                            </div>
                            <p class="text-[11px] text-ink-400 leading-relaxed">
                                Put all images in a folder, compress as a <code>.zip</code> file and upload.
                            </p>

                            <div class="relative border border-dashed border-ink-700 hover:border-gold-500/60 rounded-sm p-4 text-center bg-ink-900/60 transition-colors">
                                <input
                                    type="file"
                                    wire:model="zipFile"
                                    accept=".zip,application/zip"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                />
                                <div class="pointer-events-none">
                                    <svg class="w-6 h-6 text-gold-500/70 mx-auto mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                    @if($zipFile)
                                        <span class="text-xs text-emerald-400 font-semibold">{{ $zipFile->getClientOriginalName() }}</span>
                                        <div class="text-[10px] text-ink-400 mt-0.5">({{ round($zipFile->getSize() / (1024 * 1024), 1) }} MB)</div>
                                    @else
                                        <span class="text-xs text-ink-300 font-medium">Choose .ZIP Archive</span>
                                        <div class="text-[10px] text-ink-500 mt-0.5">Max archive size: 100MB</div>
                                    @endif
                                </div>
                            </div>

                            @if($zipFile)
                                <div class="flex justify-end">
                                    <button type="button" wire:click="removeZip" class="text-[10px] text-red-400 hover:underline">Remove ZIP</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Step 3: Import Preferences & Execution -->
                <div class="bg-ink-900 border border-ink-800 rounded-sm p-6 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-ink-800">
                        <span class="w-6 h-6 rounded-full bg-gold-500 text-ink-950 text-xs font-bold flex items-center justify-center">3</span>
                        <h2 class="font-display font-bold uppercase text-base text-white">
                            Import Options &amp; Confirm
                        </h2>
                    </div>

                    <div class="space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                wire:model="updateExisting"
                                class="mt-0.5 rounded bg-ink-800 border-ink-700 text-gold-500 focus:ring-gold-500 focus:ring-offset-ink-900"
                            />
                            <div>
                                <span class="text-xs font-semibold text-white">Update existing records</span>
                                <p class="text-[11px] text-ink-400">If a record with matching Job Number, Name, or Slug already exists, update its details and attach any new photos instead of skipping.</p>
                            </div>
                        </label>

                        @if($activeTab === 'products')
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input
                                    type="checkbox"
                                    wire:model="createCategories"
                                    class="mt-0.5 rounded bg-ink-800 border-ink-700 text-gold-500 focus:ring-gold-500 focus:ring-offset-ink-900"
                                />
                                <div>
                                    <span class="text-xs font-semibold text-white">Auto-create new categories</span>
                                    <p class="text-[11px] text-ink-400">If the category listed in CSV does not exist in the database, automatically create it.</p>
                                </div>
                            </label>
                        @endif
                    </div>

                    <div class="pt-4 border-t border-ink-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="text-xs text-ink-400 flex items-center gap-2">
                            <span class="inline-block w-2 h-2 rounded-full {{ $csvFile ? 'bg-emerald-400 animate-pulse' : 'bg-ink-600' }}"></span>
                            <span>{{ $csvFile ? 'Ready to process file' : 'Select a CSV file to proceed' }}</span>
                        </div>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            @disabled(! $csvFile || $isProcessing)
                            class="btn-primary-kemin py-2.5 px-8 text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                        >
                            <span wire:loading.remove wire:target="import">
                                Start {{ $activeTab === 'products' ? 'Products' : 'Companies' }} Import &rarr;
                            </span>
                            <span wire:loading wire:target="import" class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-ink-950" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Processing CSV &amp; Optimizing Images...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar / Guidance Column (1 span) -->
        <div class="space-y-6">
            <!-- Sample CSV Download Card -->
            <div class="bg-ink-900 border border-gold-500/30 rounded-sm p-5 space-y-4 shadow-lg">
                <div class="flex items-center gap-2 text-gold-400">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <h3 class="font-display font-bold uppercase text-sm">
                        Download Sample Template
                    </h3>
                </div>

                <p class="text-xs text-ink-300 leading-relaxed">
                    Download the pre-formatted Excel/CSV template with demo records, headers, and correct specification structures.
                </p>

                <div class="space-y-2">
                    <a
                        href="{{ route('admin.bulk-import.sample', ['type' => 'products']) }}"
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-ink-950 hover:bg-ink-800 border border-ink-800 hover:border-gold-500/50 rounded-sm text-xs font-semibold text-white transition-colors"
                    >
                        <span class="flex items-center gap-2">
                            <span class="text-gold-500">&darr;</span>
                            <span>Products CSV Sample</span>
                        </span>
                        <span class="text-[10px] text-ink-500 uppercase tracking-wider">.csv</span>
                    </a>

                    <a
                        href="{{ route('admin.bulk-import.sample', ['type' => 'companies']) }}"
                        class="w-full flex items-center justify-between px-4 py-2.5 bg-ink-950 hover:bg-ink-800 border border-ink-800 hover:border-gold-500/50 rounded-sm text-xs font-semibold text-white transition-colors"
                    >
                        <span class="flex items-center gap-2">
                            <span class="text-gold-500">&darr;</span>
                            <span>Companies CSV Sample</span>
                        </span>
                        <span class="text-[10px] text-ink-500 uppercase tracking-wider">.csv</span>
                    </a>
                </div>
            </div>

            <!-- CSV Structure Reference Card -->
            <div class="bg-ink-900 border border-ink-800 rounded-sm p-5 space-y-4">
                <h3 class="font-display font-bold uppercase text-sm text-white pb-2 border-b border-ink-800">
                    {{ $activeTab === 'products' ? 'Products Columns Guide' : 'Companies Columns Guide' }}
                </h3>

                @if($activeTab === 'products')
                    <div class="space-y-3 text-xs text-ink-300">
                        <div>
                            <span class="font-mono text-gold-400 font-bold">name</span>
                            <span class="text-[10px] text-red-400 uppercase ml-1">Required</span>
                            <p class="text-[11px] text-ink-400">e.g. <code>Luxury Rigid Ceylon Tea Box</code></p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">job_number</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Recommended</span>
                            <p class="text-[11px] text-ink-400">e.g. <code>JB-1001</code> (used for matching photos!)</p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">category</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Optional</span>
                            <p class="text-[11px] text-ink-400">e.g. <code>Rigid Boxes</code>, <code>Corrugated Cartons</code></p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">specs</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Optional</span>
                            <p class="text-[11px] text-ink-400">Format: <code>Material: Greyboard; Finishing: Matte; GSM: 350</code></p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">companies</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Optional</span>
                            <p class="text-[11px] text-ink-400">Comma-separated client names: <code>Dilmah Tea, Akbar Brothers</code></p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">images</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Optional</span>
                            <p class="text-[11px] text-ink-400">File names: <code>box1.jpg, box2.png</code> (or name photos <code>JB-1001.jpg</code>)</p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">is_featured / is_active</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Optional</span>
                            <p class="text-[11px] text-ink-400">Use <code>1</code> or <code>0</code> (defaults: featured=0, active=1)</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-3 text-xs text-ink-300">
                        <div>
                            <span class="font-mono text-gold-400 font-bold">name</span>
                            <span class="text-[10px] text-red-400 uppercase ml-1">Required</span>
                            <p class="text-[11px] text-ink-400">e.g. <code>Dilmah Tea</code></p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">industry</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Optional</span>
                            <p class="text-[11px] text-ink-400">e.g. <code>Food &amp; Beverage</code>, <code>Apparel</code></p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">country</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Optional</span>
                            <p class="text-[11px] text-ink-400">Defaults to <code>Sri Lanka</code></p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">website</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Optional</span>
                            <p class="text-[11px] text-ink-400">e.g. <code>https://www.dilmahtea.com</code></p>
                        </div>
                        <div>
                            <span class="font-mono text-gold-400 font-bold">logo</span>
                            <span class="text-[10px] text-ink-500 uppercase ml-1">Optional</span>
                            <p class="text-[11px] text-ink-400">e.g. <code>dilmah_logo.png</code> or <code>dilmah.svg</code></p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Pro Tips Card -->
            <div class="bg-ink-950/80 border border-ink-800 rounded-sm p-4 text-xs text-ink-400 space-y-2">
                <div class="flex items-center gap-1.5 text-gold-400 font-bold uppercase tracking-wider text-[11px]">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span>Pro-tip: Photo Matching</span>
                </div>
                <p class="leading-relaxed">
                    Name your product images with the Job Number (e.g. <code>JB-1001.jpg</code> or <code>JB-1001_front.png</code>). The importer will automatically detect and link them directly to the corresponding product without manual entry!
                </p>
            </div>
        </div>
    </div>
</div>
