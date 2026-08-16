<x-layouts::app :title="__('Identity Review')">
    <div class="space-y-6">
        <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">Verifier Operations</p>
                    <h1 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">Identity Review Queue</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                        Review submitted decentralized identities, inspect document proofs, and commit decisions to the proof ledger.
                    </p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-950">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Submissions</p>
                    <p class="mt-1 text-xl font-semibold text-zinc-950 dark:text-white">{{ $identities->total() }}</p>
                </div>
            </div>
        </section>

        <section class="space-y-3">
            @forelse ($identities as $identity)
                @php
                    $statusClass = match ($identity->status) {
                        'verified' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200',
                        'rejected', 'revoked' => 'bg-rose-100 text-rose-800 dark:bg-rose-950 dark:text-rose-200',
                        default => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200',
                    };
                @endphp

                <article class="grid gap-4 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div class="min-w-0">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <p class="font-semibold text-zinc-950 dark:text-white">{{ $identity->user->name }}</p>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">{{ ucfirst($identity->status) }}</span>
                            @if ($identity->document_path)
                                <span class="w-fit rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-800 dark:bg-cyan-950 dark:text-cyan-200">Document attached</span>
                            @endif
                        </div>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $identity->user->email }}</p>
                        <div class="mt-4 grid gap-3 md:grid-cols-3">
                            <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">DID</p>
                                <p class="mt-1 truncate font-mono text-xs text-zinc-950 dark:text-white">{{ $identity->did }}</p>
                            </div>
                            <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Identity type</p>
                                <p class="mt-1 text-sm font-semibold text-zinc-950 dark:text-white">{{ $identity->identity_type }}</p>
                            </div>
                            <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Submitted</p>
                                <p class="mt-1 text-sm font-semibold text-zinc-950 dark:text-white">{{ $identity->submitted_at?->format('M d, Y') ?? 'Pending' }}</p>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('admin.identities.show', $identity) }}" class="inline-flex justify-center rounded-lg bg-zinc-950 px-4 py-2 text-sm font-semibold text-white hover:bg-zinc-800 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200">
                        Open review
                    </a>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-zinc-300 bg-white p-10 text-center text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-400">
                    No identity submissions yet.
                </div>
            @endforelse
        </section>

        {{ $identities->links() }}
    </div>
</x-layouts::app>
