<x-layouts::app :title="__('Ledger Integrity')">
    <div class="space-y-6">
        <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">Integrity Operations</p>
                    <h1 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">Proof Ledger Explorer</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                        Scan identity chains for broken links, altered canonical payloads, missing hashes, and mismatched transaction proofs.
                    </p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-950">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Network</p>
                    <p class="mt-1 font-mono text-xs font-semibold text-zinc-950 dark:text-white">{{ config('blockchain.network') }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Identities scanned</p>
                <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $identities->count() }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/40">
                <p class="text-sm text-emerald-800 dark:text-emerald-200">Valid ledgers</p>
                <p class="mt-3 text-3xl font-semibold text-emerald-700 dark:text-emerald-200">{{ $validCount }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-5 shadow-sm dark:border-rose-900 dark:bg-rose-950/40">
                <p class="text-sm text-rose-800 dark:text-rose-200">Issues found</p>
                <p class="mt-3 text-3xl font-semibold text-rose-700 dark:text-rose-200">{{ $invalidCount }}</p>
            </div>
        </section>

        <section class="space-y-4">
            @forelse ($identities as $row)
                @php
                    $identity = $row['identity'];
                    $inspection = $row['inspection'];
                @endphp

                <article class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="grid gap-0 lg:grid-cols-[1fr_auto]">
                        <div class="p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    <p class="font-semibold text-zinc-950 dark:text-white">{{ $identity->user->name }}</p>
                                    <p class="mt-1 break-all font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $identity->did }}</p>
                                </div>
                                <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $inspection['valid'] ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-200' }}">
                                    {{ $inspection['valid'] ? 'Valid chain' : 'Compromised' }}
                                </span>
                            </div>

                            <div class="mt-5 grid gap-3 md:grid-cols-4">
                                <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Status</p>
                                    <p class="mt-1 text-sm font-semibold text-zinc-950 dark:text-white">{{ ucfirst($identity->status) }}</p>
                                </div>
                                <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Blocks</p>
                                    <p class="mt-1 text-sm font-semibold text-zinc-950 dark:text-white">{{ $inspection['blocks'] }}</p>
                                </div>
                                <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950 md:col-span-2">
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Latest hash</p>
                                    <p class="mt-1 truncate font-mono text-xs text-zinc-950 dark:text-white">{{ $inspection['latest_hash'] ?? 'None' }}</p>
                                </div>
                            </div>

                            @if (! $inspection['valid'])
                                <div class="mt-4 rounded-lg border border-rose-200 bg-rose-50 p-4 dark:border-rose-900 dark:bg-rose-950/40">
                                    <p class="text-sm font-semibold text-rose-800 dark:text-rose-200">Detected issues</p>
                                    <ul class="mt-3 list-disc space-y-1 ps-4 text-xs text-rose-700 dark:text-rose-200">
                                        @foreach ($inspection['issues'] as $issue)
                                            <li>{{ $issue }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="border-t border-zinc-200 bg-zinc-950 p-5 text-white dark:border-zinc-700 lg:w-72 lg:border-s lg:border-t-0">
                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-300">Anchor Status</p>
                            <p class="mt-3 text-2xl font-semibold">{{ $identity->contract_address ? 'On-chain' : 'Local' }}</p>
                            <p class="mt-3 break-all font-mono text-xs leading-5 text-zinc-300">
                                {{ $identity->transaction_hash ?: 'No transaction hash has been recorded.' }}
                            </p>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-10 text-center text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                    No identity ledgers have been created yet.
                </div>
            @endforelse
        </section>
    </div>
</x-layouts::app>
