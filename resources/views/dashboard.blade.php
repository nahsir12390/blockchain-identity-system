<x-layouts::app :title="__('Dashboard')">
    @php
        $identity = auth()->user()->digitalIdentity?->load('ledgerEntries');
        $totalIdentities = \App\Models\DigitalIdentity::count();
        $verifiedIdentities = \App\Models\DigitalIdentity::where('status', 'verified')->count();
        $pendingIdentities = \App\Models\DigitalIdentity::where('status', 'pending')->count();
        $ledgerBlocks = \App\Models\LedgerEntry::count();
        $onchainAnchors = \App\Models\LedgerEntry::whereNotNull('blockchain_tx_hash')->count();
        $latestBlock = \App\Models\LedgerEntry::query()->latest('block_number')->first();
        $verifiedRate = $totalIdentities > 0 ? round(($verifiedIdentities / $totalIdentities) * 100) : 0;
    @endphp

    <div class="space-y-6">
        <section class="chain-grid overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-950 text-white shadow-sm dark:border-zinc-700">
            <div class="grid gap-0 lg:grid-cols-[1.3fr_.7fr]">
                <div class="p-6 sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">Decentralized Identity Network</p>
                    <div class="mt-5 max-w-3xl">
                        <h1 class="text-3xl font-semibold tracking-normal sm:text-4xl">Your identity command center</h1>
                        <p class="mt-3 text-sm leading-6 text-zinc-300">
                            Manage DIDs, anchor identity lifecycle events to the smart contract, and inspect the proof ledger for tamper evidence.
                        </p>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-xs text-zinc-400">Network</p>
                            <p class="mt-2 text-sm font-semibold">{{ config('blockchain.network') }}</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-xs text-zinc-400">Smart contract</p>
                            <p class="mt-2 truncate font-mono text-xs">{{ config('blockchain.contract_address') ?: 'Not deployed' }}</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="text-xs text-zinc-400">Anchoring</p>
                            <p class="mt-2 text-sm font-semibold {{ config('blockchain.enabled') ? 'text-emerald-300' : 'text-amber-300' }}">
                                {{ config('blockchain.enabled') ? 'Enabled' : 'Local fallback' }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/10 bg-zinc-900/80 p-6 lg:border-s lg:border-t-0">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-400">Latest recorded proof</p>
                    <div class="mt-5 rounded-xl border border-emerald-400/20 bg-emerald-400/10 p-5">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-emerald-200">Block height</span>
                            <span class="rounded-full bg-emerald-300 px-3 py-1 text-xs font-semibold text-zinc-950">{{ $latestBlock ? 'Recorded' : 'Awaiting proof' }}</span>
                        </div>
                        <p class="mt-4 text-4xl font-semibold">{{ $latestBlock?->block_number ?? 0 }}</p>
                        <p class="mt-4 break-all font-mono text-xs leading-5 text-emerald-100">
                            {{ $latestBlock?->entry_hash ?? 'No ledger block has been created yet.' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Total identities</p>
                <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $totalIdentities }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/40">
                <p class="text-sm text-emerald-800 dark:text-emerald-200">Verified rate</p>
                <p class="mt-3 text-3xl font-semibold text-emerald-700 dark:text-emerald-200">{{ $verifiedRate }}%</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm dark:border-amber-900 dark:bg-amber-950/40">
                <p class="text-sm text-amber-800 dark:text-amber-200">Pending review</p>
                <p class="mt-3 text-3xl font-semibold text-amber-700 dark:text-amber-200">{{ $pendingIdentities }}</p>
            </div>
            <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-5 shadow-sm dark:border-cyan-900 dark:bg-cyan-950/40">
                <p class="text-sm text-cyan-800 dark:text-cyan-200">On-chain anchors</p>
                <p class="mt-3 text-3xl font-semibold text-cyan-700 dark:text-cyan-200">{{ $onchainAnchors }}</p>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-[.9fr_1.1fr]">
            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Your DID wallet</p>
                        <h2 class="mt-2 text-xl font-semibold text-zinc-950 dark:text-white">{{ $identity ? ucfirst($identity->status) : 'Not enrolled' }}</h2>
                    </div>
                    <a href="{{ route('identity.index') }}" class="rounded-lg bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                        {{ $identity ? 'Open identity' : 'Create DID' }}
                    </a>
                </div>

                @if ($identity)
                    <div class="mt-6 space-y-4">
                        <div class="rounded-lg border border-zinc-200 bg-zinc-50 p-4 dark:border-zinc-700 dark:bg-zinc-950">
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Decentralized identifier</p>
                            <p class="mt-2 break-all font-mono text-sm text-zinc-950 dark:text-white">{{ $identity->did }}</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Ledger blocks</p>
                                <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $identity->ledgerEntries->count() }}</p>
                            </div>
                            <div class="rounded-lg border border-zinc-200 p-4 dark:border-zinc-700">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Transaction</p>
                                <p class="mt-2 truncate font-mono text-xs text-zinc-950 dark:text-white">{{ $identity->transaction_hash ?: 'Pending' }}</p>
                            </div>
                        </div>
                    </div>
                @else
                    <p class="mt-6 rounded-lg border border-dashed border-zinc-300 p-5 text-sm leading-6 text-zinc-600 dark:border-zinc-700 dark:text-zinc-400">
                        Create your decentralized identity to receive a DID, local proof ledger, and smart-contract anchor.
                    </p>
                @endif
            </div>

            <div class="rounded-xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Proof Ledger</p>
                        <h2 class="mt-2 text-xl font-semibold text-zinc-950 dark:text-white">{{ $ledgerBlocks }} recorded blocks</h2>
                    </div>
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('admin.ledger.index') }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-600 dark:text-emerald-300">Inspect</a>
                    @endif
                </div>

                <div class="mt-6 space-y-3">
                    @forelse (\App\Models\LedgerEntry::query()->with('digitalIdentity.user')->latest('recorded_at')->limit(4)->get() as $entry)
                        <div class="grid gap-3 rounded-lg border border-zinc-200 p-4 dark:border-zinc-700 sm:grid-cols-[auto_1fr_auto] sm:items-center">
                            <div class="flex size-10 items-center justify-center rounded-lg bg-zinc-950 font-mono text-xs font-semibold text-white dark:bg-white dark:text-zinc-950">
                                #{{ $entry->block_number }}
                            </div>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-medium text-zinc-950 dark:text-white">{{ $entry->action }}</p>
                                <p class="mt-1 truncate font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $entry->entry_hash }}</p>
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $entry->blockchain_tx_hash ? 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-200' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200' }}">
                                {{ $entry->blockchain_tx_hash ? 'On-chain' : 'Local' }}
                            </span>
                        </div>
                    @empty
                        <p class="rounded-lg border border-dashed border-zinc-300 p-5 text-sm text-zinc-500 dark:border-zinc-700 dark:text-zinc-400">
                            No proof blocks yet.
                        </p>
                    @endforelse
                </div>
            </div>
        </section>
    </div>
</x-layouts::app>
