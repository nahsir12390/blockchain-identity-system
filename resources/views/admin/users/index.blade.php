<x-layouts::app :title="__('Access Control')">
    <div class="space-y-6">
        <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">Access Control</p>
                    <h1 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">User Security Registry</h1>
                    <p class="mt-2 max-w-3xl text-sm leading-6 text-zinc-600 dark:text-zinc-400">
                        Manage administrator privileges and monitor user readiness across identity verification, email trust, and 2FA.
                    </p>
                </div>
                <div class="rounded-lg border border-zinc-200 bg-zinc-50 px-4 py-3 dark:border-zinc-700 dark:bg-zinc-950">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Users</p>
                    <p class="mt-1 text-xl font-semibold text-zinc-950 dark:text-white">{{ $users->total() }}</p>
                </div>
            </div>
        </section>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <section class="space-y-3">
            @foreach ($users as $user)
                <article class="grid gap-4 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 lg:grid-cols-[1fr_auto] lg:items-center">
                    <div class="min-w-0">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                            <p class="font-semibold text-zinc-950 dark:text-white">{{ $user->name }}</p>
                            <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $user->is_admin ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200' }}">
                                {{ $user->is_admin ? 'Administrator' : 'Standard user' }}
                            </span>
                        </div>
                        <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $user->email }}</p>
                        <div class="mt-4 grid gap-3 md:grid-cols-3">
                            <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Email trust</p>
                                <p class="mt-1 text-sm font-semibold {{ $user->email_verified_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }}">{{ $user->email_verified_at ? 'Verified' : 'Unverified' }}</p>
                            </div>
                            <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">2FA</p>
                                <p class="mt-1 text-sm font-semibold {{ $user->two_factor_confirmed_at ? 'text-emerald-700 dark:text-emerald-300' : 'text-zinc-700 dark:text-zinc-200' }}">{{ $user->two_factor_confirmed_at ? 'Enabled' : 'Disabled' }}</p>
                            </div>
                            <div class="rounded-lg bg-zinc-50 p-3 dark:bg-zinc-950">
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">DID status</p>
                                <p class="mt-1 text-sm font-semibold text-zinc-950 dark:text-white">{{ $user->digitalIdentity ? ucfirst($user->digitalIdentity->status) : 'Not enrolled' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        @if ($user->is_admin)
                            @unless ($user->is(auth()->user()))
                                <form method="POST" action="{{ route('admin.users.demote', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:text-zinc-200 dark:hover:bg-zinc-800">Demote</button>
                                </form>
                            @else
                                <span class="rounded-lg bg-zinc-100 px-4 py-2 text-sm font-semibold text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400">Current admin</span>
                            @endunless
                        @else
                            <form method="POST" action="{{ route('admin.users.promote', $user) }}">
                                @csrf
                                @method('PATCH')
                                <button class="rounded-lg bg-emerald-500 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-emerald-400">Promote</button>
                            </form>
                        @endif
                    </div>
                </article>
            @endforeach
        </section>

        {{ $users->links() }}
    </div>
</x-layouts::app>
