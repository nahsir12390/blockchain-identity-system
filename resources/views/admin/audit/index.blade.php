<x-layouts::app :title="__('Audit Log')">
    <div class="space-y-6">
        <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">Security Monitoring</p>
                    <h1 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">Cybersecurity Activity Log</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                        Monitor identity lifecycle events, verifier actions, proof hashes, and on-chain confirmations across the platform.
                    </p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-950">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Filter</p>
                    <p class="mt-1 text-sm font-semibold text-zinc-950 dark:text-white">{{ $selectedAction ?: 'All actions' }} / {{ $selectedStatus ?: 'All statuses' }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Total audit events</p>
                <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $totalEntries }}</p>
            </div>
            <div class="rounded-xl border border-cyan-200 bg-cyan-50 p-5 shadow-sm dark:border-cyan-900 dark:bg-cyan-950/40">
                <p class="text-sm text-cyan-800 dark:text-cyan-200">Authenticated actions</p>
                <p class="mt-3 text-3xl font-semibold text-cyan-700 dark:text-cyan-200">{{ $adminActions }}</p>
            </div>
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900 dark:bg-emerald-950/40">
                <p class="text-sm text-emerald-800 dark:text-emerald-200">On-chain events</p>
                <p class="mt-3 text-3xl font-semibold text-emerald-700 dark:text-emerald-200">{{ $entries->whereNotNull('blockchain_tx_hash')->count() }}</p>
            </div>
        </section>

        <form method="GET" action="{{ route('admin.audit.index') }}" class="grid gap-4 rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 md:grid-cols-[1fr_1fr_auto]">
            <div>
                <label for="action" class="text-sm font-medium text-zinc-950 dark:text-white">Action</label>
                <select id="action" name="action" class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    <option value="">All actions</option>
                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected($selectedAction === $action)>{{ $action }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="status" class="text-sm font-medium text-zinc-950 dark:text-white">Identity status</label>
                <select id="status" name="status" class="mt-2 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-950 dark:text-white">
                    <option value="">All statuses</option>
                    @foreach (['pending', 'verified', 'rejected', 'revoked'] as $status)
                        <option value="{{ $status }}" @selected($selectedStatus === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end gap-3">
                <button class="rounded-lg bg-zinc-950 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">Filter</button>
                <a href="{{ route('admin.audit.index') }}" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Reset</a>
            </div>
        </form>

        <section class="space-y-3">
            @forelse ($entries as $entry)
                <article class="grid gap-4 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900 lg:grid-cols-[auto_1fr_auto] lg:items-start">
                    <div class="flex size-12 items-center justify-center rounded-xl bg-zinc-950 font-mono text-sm font-semibold text-white dark:bg-white dark:text-zinc-950">
                        #{{ $entry->block_number }}
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-zinc-950 dark:text-white">{{ $entry->action }}</p>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $entry->recorded_at->format('M d, Y H:i') }} by {{ $entry->actor?->name ?? 'System' }}</p>
                            </div>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $entry->blockchain_tx_hash ? 'bg-cyan-100 text-cyan-800 dark:bg-cyan-950 dark:text-cyan-200' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200' }}">
                                {{ $entry->blockchain_tx_hash ? 'On-chain' : 'Local only' }}
                            </span>
                        </div>
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Identity</p>
                                <p class="mt-1 truncate text-sm font-medium text-zinc-950 dark:text-white">{{ $entry->digitalIdentity->user->name }}</p>
                                <p class="mt-1 truncate font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $entry->digitalIdentity->did }}</p>
                            </div>
                            <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Proof hash</p>
                                <p class="mt-1 truncate font-mono text-xs text-zinc-950 dark:text-white">{{ $entry->entry_hash }}</p>
                            </div>
                        </div>
                        @if ($entry->blockchain_tx_hash)
                            <p class="mt-3 break-all rounded-lg border border-cyan-200 bg-cyan-50 p-3 font-mono text-xs text-cyan-800 dark:border-cyan-900 dark:bg-cyan-950/40 dark:text-cyan-200">
                                {{ $entry->blockchain_tx_hash }}
                            </p>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($entry->digitalIdentity->status) }}</p>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-10 text-center text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                    No audit events found.
                </div>
            @endforelse
        </section>

        {{ $entries->links() }}
    </div>
</x-layouts::app>
