<x-layouts::app :title="__('My Decentralized Identity')">
    <div class="space-y-6">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">DID Wallet</p>
                <h1 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">My Decentralized Identity</h1>
                <p class="mt-1 text-sm text-zinc-600 dark:text-zinc-400">Manage your DID, smart-contract anchor, verification status, and proof trail.</p>
            </div>

            @unless ($identity)
                <a href="{{ route('identity.create') }}" class="inline-flex items-center justify-center rounded-lg bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                    Create identity
                </a>
            @endunless
        </div>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @unless ($identity)
            <section class="overflow-hidden rounded-2xl border border-dashed border-zinc-300 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="grid gap-0 lg:grid-cols-[1fr_.9fr]">
                    <div class="p-8">
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">No identity record yet</p>
                        <h2 class="mt-3 text-2xl font-semibold text-zinc-950 dark:text-white">Create a tamper-evident identity profile</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                            Your sensitive identity data is converted to keyed hashes. The system records every lifecycle action as a local block and anchors proofs to the smart contract when the blockchain node is online.
                        </p>
                        <a href="{{ route('identity.create') }}" class="mt-6 inline-flex rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                            Start DID enrollment
                        </a>
                    </div>
                    <div class="border-t border-zinc-200 bg-zinc-950 p-8 text-white dark:border-zinc-700 lg:border-s lg:border-t-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">Identity lifecycle</p>
                        <div class="mt-5 space-y-4">
                            @foreach (['Submit hashed proof', 'Admin verification', 'Smart-contract anchor', 'Public DID verification'] as $step)
                                <div class="flex gap-3">
                                    <div class="mt-1 size-3 rounded-full bg-emerald-300"></div>
                                    <p class="text-sm text-zinc-200">{{ $step }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @else
            @php
                $statusClass = match ($identity->status) {
                    'verified' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200',
                    'rejected', 'revoked' => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-200',
                    default => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200',
                };
            @endphp

            <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="grid gap-0 lg:grid-cols-[1.2fr_.8fr]">
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Decentralized identifier</p>
                                <p class="mt-3 break-all font-mono text-base font-semibold text-zinc-950 dark:text-white">{{ $identity->did }}</p>
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ ucfirst($identity->status) }}
                            </span>
                        </div>

                        <dl class="mt-8 grid gap-4 sm:grid-cols-2">
                            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                <dt class="text-xs text-zinc-500 dark:text-zinc-400">Legal name</dt>
                                <dd class="mt-2 text-sm font-semibold text-zinc-950 dark:text-white">{{ $identity->legal_name }}</dd>
                            </div>
                            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                <dt class="text-xs text-zinc-500 dark:text-zinc-400">Identity type</dt>
                                <dd class="mt-2 text-sm font-semibold text-zinc-950 dark:text-white">{{ $identity->identity_type }}</dd>
                            </div>
                            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                <dt class="text-xs text-zinc-500 dark:text-zinc-400">Wallet address</dt>
                                <dd class="mt-2 break-all font-mono text-xs text-zinc-950 dark:text-white">{{ $identity->wallet_address ?: 'Not linked' }}</dd>
                            </div>
                            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                <dt class="text-xs text-zinc-500 dark:text-zinc-400">Network</dt>
                                <dd class="mt-2 text-sm font-semibold text-zinc-950 dark:text-white">{{ $identity->blockchain_network }}</dd>
                            </div>
                            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 sm:col-span-2">
                                <dt class="text-xs text-zinc-500 dark:text-zinc-400">Document proof</dt>
                                <dd class="mt-2">
                                    @if ($identity->document_path)
                                        <a href="{{ route('identity.document', $identity) }}" class="inline-flex rounded-lg bg-zinc-950 px-3 py-2 text-xs font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                                            Download private document
                                        </a>
                                        <p class="mt-2 break-all font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $identity->document_hash }}</p>
                                    @elseif ($identity->document_hash)
                                        <p class="break-all font-mono text-xs text-zinc-950 dark:text-white">{{ $identity->document_hash }}</p>
                                    @else
                                        <p class="text-sm text-zinc-500 dark:text-zinc-400">No document proof attached</p>
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="border-t border-zinc-200 bg-zinc-950 p-6 text-white dark:border-zinc-700 lg:border-s lg:border-t-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">Proof Passport</p>
                        <div class="mt-5 space-y-4 text-sm">
                            <div>
                                <p class="text-xs text-zinc-400">Transaction hash</p>
                                <p class="mt-1 break-all font-mono text-xs text-zinc-100">{{ $identity->transaction_hash ?: 'Pending' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-400">Contract address</p>
                                <p class="mt-1 break-all font-mono text-xs text-zinc-100">{{ $identity->contract_address ?: 'Local ledger only' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-zinc-400">Block hash</p>
                                <p class="mt-1 break-all font-mono text-xs text-zinc-100">{{ $identity->block_hash ?: 'Pending' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @if ($identity->status === 'rejected')
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-900 dark:border-rose-900 dark:bg-rose-950 dark:text-rose-200">
                    <p class="font-semibold">Verification rejected</p>
                    <p class="mt-1">{{ $identity->rejection_reason }}</p>
                    <form method="POST" action="{{ route('identity.resubmit', $identity) }}" class="mt-4">
                        @csrf
                        @method('PATCH')
                        <button class="rounded-lg bg-rose-700 px-4 py-2 text-sm font-medium text-white hover:bg-rose-800">Resubmit for review</button>
                    </form>
                </div>
            @endif

            @if ($identity->status === 'verified')
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 dark:border-emerald-900 dark:bg-emerald-950">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="font-semibold text-emerald-950 dark:text-emerald-100">Verified identity certificate</h2>
                            <p class="mt-1 text-sm text-emerald-800 dark:text-emerald-200">Generate a printable certificate backed by DID, transaction hash, and block hash.</p>
                        </div>
                        <a href="{{ route('identity.certificate', $identity) }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-700 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-800" target="_blank">
                            View certificate
                        </a>
                    </div>
                </div>
            @endif

            <section class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Block Explorer</p>
                    <h2 class="mt-2 font-semibold text-zinc-950 dark:text-white">Proof ledger timeline</h2>
                </div>
                <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @foreach ($identity->ledgerEntries as $entry)
                        <div class="grid gap-4 p-5 lg:grid-cols-[auto_1fr_auto] lg:items-start">
                            <div class="flex size-12 items-center justify-center rounded-xl bg-zinc-950 font-mono text-sm font-semibold text-white dark:bg-white dark:text-zinc-950">
                                #{{ $entry->block_number }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="font-semibold text-zinc-950 dark:text-white">{{ $entry->action }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $entry->recorded_at->format('M d, Y H:i') }}</p>
                                </div>
                                <dl class="mt-4 grid gap-3 text-xs sm:grid-cols-2">
                                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                        <dt class="text-zinc-500 dark:text-zinc-400">Entry hash</dt>
                                        <dd class="mt-1 break-all font-mono text-zinc-800 dark:text-zinc-200">{{ $entry->entry_hash }}</dd>
                                    </div>
                                    <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                        <dt class="text-zinc-500 dark:text-zinc-400">Previous hash</dt>
                                        <dd class="mt-1 break-all font-mono text-zinc-800 dark:text-zinc-200">{{ $entry->previous_hash ?: 'GENESIS' }}</dd>
                                    </div>
                                </dl>
                                @if ($entry->blockchain_tx_hash)
                                    <div class="mt-3 rounded-lg border border-cyan-200 bg-cyan-50 p-3 text-xs dark:border-cyan-900 dark:bg-cyan-950/40">
                                        <p class="font-semibold text-cyan-800 dark:text-cyan-200">On-chain proof confirmed</p>
                                        <p class="mt-1 break-all font-mono text-cyan-700 dark:text-cyan-300">{{ $entry->blockchain_tx_hash }}</p>
                                    </div>
                                @endif
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $entry->blockchain_tx_hash ? 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-200' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200' }}">
                                {{ $entry->blockchain_tx_hash ? 'Anchored' : 'Local' }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </section>
        @endunless
    </div>
</x-layouts::app>
