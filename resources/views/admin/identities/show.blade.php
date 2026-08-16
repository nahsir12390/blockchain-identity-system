<x-layouts::app :title="__('Review Identity')">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-zinc-950 dark:text-white">Review Identity</h1>
                <p class="mt-1 break-all font-mono text-sm text-zinc-600 dark:text-zinc-400">{{ $identity->did }}</p>
            </div>
            <a href="{{ route('admin.identities.index') }}" class="text-sm font-medium text-zinc-700 underline dark:text-zinc-200">Back to queue</a>
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700 lg:col-span-2">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Applicant details</h2>
                <dl class="mt-5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs text-zinc-500 dark:text-zinc-400">User</dt>
                        <dd class="mt-1 text-sm font-medium text-zinc-950 dark:text-white">{{ $identity->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Email</dt>
                        <dd class="mt-1 text-sm font-medium text-zinc-950 dark:text-white">{{ $identity->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Identity type</dt>
                        <dd class="mt-1 text-sm font-medium text-zinc-950 dark:text-white">{{ $identity->identity_type }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Status</dt>
                        <dd class="mt-1 text-sm font-medium text-zinc-950 dark:text-white">{{ ucfirst($identity->status) }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Identity number hash</dt>
                        <dd class="mt-1 break-all font-mono text-xs text-zinc-950 dark:text-white">{{ $identity->identity_number_hash }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Document proof</dt>
                        <dd class="mt-2">
                            @if ($identity->document_path)
                                <a href="{{ route('identity.document', $identity) }}" class="inline-flex rounded-lg bg-zinc-950 px-3 py-2 text-xs font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                                    Download submitted document
                                </a>
                                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $identity->document_original_name }} · {{ number_format(($identity->document_size ?? 0) / 1024, 1) }} KB</p>
                                <p class="mt-2 break-all font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $identity->document_hash }}</p>
                            @elseif ($identity->document_hash)
                                <p class="break-all font-mono text-xs text-zinc-950 dark:text-white">{{ $identity->document_hash }}</p>
                            @else
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">No document proof attached.</p>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Blockchain transaction</dt>
                        <dd class="mt-1 break-all font-mono text-xs text-zinc-950 dark:text-white">{{ $identity->transaction_hash ?: 'Not anchored yet' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-zinc-500 dark:text-zinc-400">Contract address</dt>
                        <dd class="mt-1 break-all font-mono text-xs text-zinc-950 dark:text-white">{{ $identity->contract_address ?: 'Local ledger only' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Decision</h2>
                <div class="mt-4 space-y-3">
                    @if ($identity->status === 'pending')
                        <form method="POST" action="{{ route('admin.identities.verify', $identity) }}">
                            @csrf
                            @method('PATCH')
                            <button class="w-full rounded-lg bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800">Verify identity</button>
                        </form>
                    @endif

                    @if ($identity->status === 'verified')
                        <form method="POST" action="{{ route('admin.identities.revoke', $identity) }}">
                            @csrf
                            @method('PATCH')
                            <button class="w-full rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Revoke identity</button>
                        </form>
                    @endif
                </div>

                @if ($identity->status === 'pending')
                    <form method="POST" action="{{ route('admin.identities.reject', $identity) }}" class="mt-5 space-y-3">
                        @csrf
                        @method('PATCH')
                        <label for="rejection_reason" class="text-sm font-medium text-zinc-950 dark:text-white">Reject with reason</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4" class="w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900 dark:text-white"></textarea>
                        @error('rejection_reason') <p class="text-sm text-rose-600">{{ $message }}</p> @enderror
                        <button class="w-full rounded-lg bg-rose-700 px-4 py-2 text-sm font-medium text-white hover:bg-rose-800">Reject identity</button>
                    </form>
                @endif

                @unless (in_array($identity->status, ['pending', 'verified'], true))
                    <p class="mt-4 rounded-lg bg-zinc-50 p-3 text-sm text-zinc-600 dark:bg-zinc-900 dark:text-zinc-400">
                        No admin decision is available for the current {{ $identity->status }} status.
                    </p>
                @endunless
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Ledger trail</h2>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ($identity->ledgerEntries as $entry)
                    <div class="p-5">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="font-medium text-zinc-950 dark:text-white">Block #{{ $entry->block_number }}: {{ $entry->action }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $entry->actor?->name ?? 'System' }}</p>
                        </div>
                        <p class="mt-2 break-all font-mono text-xs text-zinc-600 dark:text-zinc-400">{{ $entry->entry_hash }}</p>
                        @if ($entry->blockchain_tx_hash)
                            <dl class="mt-3 grid gap-3 rounded-lg bg-emerald-50 p-3 text-xs dark:bg-emerald-950/40 sm:grid-cols-2">
                                <div>
                                    <dt class="font-semibold text-emerald-800 dark:text-emerald-200">On-chain tx</dt>
                                    <dd class="mt-1 break-all font-mono text-emerald-700 dark:text-emerald-300">{{ $entry->blockchain_tx_hash }}</dd>
                                </div>
                                <div>
                                    <dt class="font-semibold text-emerald-800 dark:text-emerald-200">Contract</dt>
                                    <dd class="mt-1 break-all font-mono text-emerald-700 dark:text-emerald-300">{{ $entry->contract_address }}</dd>
                                </div>
                            </dl>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts::app>
