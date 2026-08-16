<x-layouts::app :title="__('Create Identity')">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-950 text-white shadow-sm dark:border-zinc-800">
            <div class="grid gap-0 lg:grid-cols-[1.15fr_.85fr]">
                <div class="p-6 sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">DID Enrollment Terminal</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-normal">Create Decentralized Identity</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-300">
                        Submit your identity proof for verification. Sensitive values are converted into keyed hashes before storage, then lifecycle actions are recorded in the proof ledger.
                    </p>
                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-xs text-zinc-400">Network</p>
                            <p class="mt-2 truncate text-sm font-semibold">{{ config('blockchain.network') }}</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-xs text-zinc-400">Privacy</p>
                            <p class="mt-2 text-sm font-semibold text-emerald-300">HMAC protected</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-xs text-zinc-400">Proof mode</p>
                            <p class="mt-2 text-sm font-semibold {{ config('blockchain.enabled') ? 'text-cyan-300' : 'text-amber-300' }}">{{ config('blockchain.enabled') ? 'On-chain' : 'Local' }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/10 bg-zinc-900 p-6 lg:border-s lg:border-t-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-cyan-300">Enrollment Pipeline</p>
                    <div class="mt-5 space-y-4">
                        @foreach ([
                            ['01', 'Hash private identity data'],
                            ['02', 'Issue did:trustwall identifier'],
                            ['03', 'Queue admin verification'],
                            ['04', 'Anchor proof to ledger'],
                        ] as [$step, $label])
                            <div class="flex items-center gap-3">
                                <span class="flex size-9 items-center justify-center rounded-lg bg-white/10 font-mono text-xs font-semibold text-emerald-200">{{ $step }}</span>
                                <span class="text-sm text-zinc-200">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <form method="POST" action="{{ route('identity.store') }}" enctype="multipart/form-data" class="grid gap-6 lg:grid-cols-[1fr_.42fr]">
            @csrf

            <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 pb-5 dark:border-zinc-800">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Identity Payload</p>
                    <h2 class="mt-2 text-lg font-semibold text-zinc-950 dark:text-white">Applicant proof details</h2>
                </div>

                <div class="mt-6 space-y-5">
                    <div>
                        <label for="legal_name" class="text-sm font-medium text-zinc-950 dark:text-white">Legal name</label>
                        <input id="legal_name" name="legal_name" value="{{ old('legal_name', auth()->user()->name) }}" class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" required>
                        @error('legal_name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="identity_type" class="text-sm font-medium text-zinc-950 dark:text-white">Identity type</label>
                            <select id="identity_type" name="identity_type" class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" required>
                                @foreach (['National ID', 'Student ID', 'Passport', 'Driver License'] as $type)
                                    <option value="{{ $type }}" @selected(old('identity_type') === $type)>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="identity_number" class="text-sm font-medium text-zinc-950 dark:text-white">Identity number</label>
                            <input id="identity_number" name="identity_number" class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" required>
                            @error('identity_number') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="wallet_address" class="text-sm font-medium text-zinc-950 dark:text-white">Wallet address</label>
                        <input id="wallet_address" name="wallet_address" value="{{ old('wallet_address') }}" class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" placeholder="0x...">
                    </div>

                    <div>
                        <label for="public_key" class="text-sm font-medium text-zinc-950 dark:text-white">Public key</label>
                        <textarea id="public_key" name="public_key" rows="4" class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 font-mono text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" placeholder="Paste public key or wallet signature">{{ old('public_key') }}</textarea>
                    </div>

                    <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-4 dark:border-cyan-900 dark:bg-cyan-950/30">
                        <label for="document_file" class="text-sm font-semibold text-zinc-950 dark:text-white">Upload identity document</label>
                        <p class="mt-1 text-xs leading-5 text-zinc-600 dark:text-zinc-400">Accepted: PDF, JPG, PNG, WEBP. Maximum size: 4MB. Stored privately and represented by a SHA-256 file hash.</p>
                        <input id="document_file" name="document_file" type="file" accept=".pdf,.jpg,.jpeg,.png,.webp" class="mt-3 w-full rounded-lg border border-cyan-200 bg-white px-3 py-2 text-sm text-zinc-950 file:mr-3 file:rounded-md file:border-0 file:bg-zinc-950 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white dark:border-cyan-900 dark:bg-zinc-950 dark:text-white dark:file:bg-emerald-400 dark:file:text-zinc-950">
                        @error('document_file') <p class="mt-2 text-sm text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="document_reference" class="text-sm font-medium text-zinc-950 dark:text-white">Document reference</label>
                        <input id="document_reference" name="document_reference" value="{{ old('document_reference') }}" class="mt-2 min-h-11 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white" placeholder="Optional certificate number, checksum, or reference code">
                    </div>
                </div>
            </section>

            <aside class="space-y-4">
                <div class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Generated Proof</p>
                    <div class="mt-4 space-y-4 text-sm">
                        <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-950">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">DID namespace</p>
                            <p class="mt-1 font-mono text-xs text-zinc-950 dark:text-white">did:trustwall:ulid</p>
                        </div>
                        <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-950">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Stored data</p>
                            <p class="mt-1 text-sm font-semibold text-zinc-950 dark:text-white">Private file + hash</p>
                        </div>
                        <div class="rounded-lg bg-zinc-50 p-4 dark:bg-zinc-950">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Initial status</p>
                            <p class="mt-1 text-sm font-semibold text-amber-700 dark:text-amber-300">Pending review</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-900 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-100">
                    <p class="font-semibold">Zero-trust storage</p>
                    <p class="mt-2 leading-6">Raw identity numbers are not stored. Uploaded documents stay in private storage, while hashes, block hashes, and transaction references power verification.</p>
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('identity.index') }}" class="flex-1 rounded-lg border border-zinc-300 px-4 py-2 text-center text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Cancel</a>
                    <button class="flex-1 rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-emerald-400">Submit proof</button>
                </div>
            </aside>
        </form>
    </div>
</x-layouts::app>
