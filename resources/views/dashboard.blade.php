<x-layouts::app :title="__('Dashboard')">
    @if (auth()->user()->is_admin)
        @php
            $totalIdentities = \App\Models\DigitalIdentity::count();
            $pendingIdentities = \App\Models\DigitalIdentity::where('status', 'pending')->count();
            $verifiedIdentities = \App\Models\DigitalIdentity::where('status', 'verified')->count();
            $rejectedIdentities = \App\Models\DigitalIdentity::where('status', 'rejected')->count();
            $pendingRequests = \App\Models\DigitalIdentity::query()->with('user')->where('status', 'pending')->latest('submitted_at')->limit(5)->get();
        @endphp

        <div class="mx-auto max-w-6xl space-y-6">
            <section class="rounded-2xl bg-zinc-950 p-6 text-white sm:p-9">
                <p class="text-sm font-semibold text-emerald-300">DID ChainVault · Administration</p>
                <h1 class="mt-3 text-3xl font-semibold">Welcome, {{ auth()->user()->name }}!</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-300">Manage identity submissions and review verification requests.</p>
                <a href="{{ route('admin.identities.index') }}" class="mt-6 inline-flex min-h-11 items-center rounded-lg bg-emerald-500 px-5 py-3 text-sm font-semibold text-zinc-950 hover:bg-emerald-400">Review Identities</a>
            </section>

            <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="Identity statistics">
                @foreach ([
                    ['Total Identities', $totalIdentities],
                    ['Pending Verification', $pendingIdentities],
                    ['Verified Identities', $verifiedIdentities],
                    ['Rejected Identities', $rejectedIdentities],
                ] as [$label, $count])
                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $label }}</p>
                        <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $count }}</p>
                    </div>
                @endforeach
            </section>

            <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h2 class="text-xl font-semibold text-zinc-950 dark:text-white">Pending Verification Requests</h2>
                        <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">The most recent submissions waiting for an administrator.</p>
                    </div>
                    <a href="{{ route('admin.identities.index') }}" class="text-sm font-semibold text-emerald-700 underline dark:text-emerald-300">View all submissions</a>
                </div>
                <div class="mt-6 divide-y divide-zinc-200 dark:divide-zinc-800">
                    @forelse ($pendingRequests as $request)
                        <div class="flex flex-col justify-between gap-3 py-4 sm:flex-row sm:items-center">
                            <div class="min-w-0">
                                <p class="font-semibold text-zinc-950 dark:text-white">{{ $request->legal_name }}</p>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $request->identity_type }} · Submitted {{ $request->submitted_at?->format('M j, Y') ?? 'recently' }}</p>
                            </div>
                            <a href="{{ route('admin.identities.show', $request) }}" class="inline-flex min-h-10 items-center justify-center rounded-lg border border-zinc-300 px-4 py-2 text-sm font-semibold text-zinc-900 hover:bg-zinc-50 dark:border-zinc-700 dark:text-white dark:hover:bg-zinc-800">Review submission</a>
                        </div>
                    @empty
                        <p class="py-6 text-sm text-zinc-500 dark:text-zinc-400">No pending verification requests right now.</p>
                    @endforelse
                </div>
            </section>
        </div>
    @else
        @php
            $identity = auth()->user()->digitalIdentity;
        $statusText = match ($identity?->status) {
            'pending' => 'Pending verification',
            'verified' => 'Verified',
            'rejected' => 'Action needed',
            'revoked' => 'Verification revoked',
            default => 'Not created',
        };
        $statusMessage = match ($identity?->status) {
            'pending' => 'Your information has been submitted. An administrator will review it.',
            'verified' => 'Your identity has been approved. You can share your DID for a status check.',
            'rejected' => 'Your submission was not approved. Read the reason and resubmit it for review.',
            'revoked' => 'Your verification has been revoked. Contact the administrator if you need help.',
            default => 'You have created an account. Next, submit your identity details for review.',
        };
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        <section class="rounded-2xl bg-zinc-950 p-6 text-white sm:p-9">
            <p class="text-sm font-semibold text-emerald-300">DID ChainVault</p>
            <h1 class="mt-3 text-3xl font-semibold">Welcome, {{ auth()->user()->name }}!</h1>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-300">Create a digital identity, follow its verification progress, and share your verification status.</p>
        </section>

        @if (session('status'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-900 dark:border-emerald-900 dark:bg-emerald-950 dark:text-emerald-200">{{ session('status') }}</div>
        @endif

        <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">My identity status</p>
            <h2 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $statusText }}</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $statusMessage }}</p>
            @if ($identity)
                <div class="mt-6 rounded-xl bg-zinc-50 p-4 dark:bg-zinc-950">
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Your Digital Identity ID (DID)</p>
                    <p class="mt-2 break-all font-mono text-sm text-zinc-950 dark:text-white">{{ $identity->did }}</p>
                </div>
            @endif
            <a href="{{ $identity ? route('identity.index') : route('identity.create') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">{{ $identity ? 'View My Identity' : 'Create Digital Identity' }}</a>
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
            <h2 class="text-xl font-semibold text-zinc-950 dark:text-white">How it works</h2>
            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-300">Just four steps to get started.</p>
            <ol class="mt-6 grid gap-4 sm:grid-cols-2">
                @foreach ([
                    ['1', 'Create your account', 'You have already signed in.'],
                    ['2', 'Submit your identity', 'Enter your details and attach a supporting document if available.'],
                    ['3', 'Wait for review', 'An administrator reviews the information you submitted.'],
                    ['4', 'Share your DID', 'After approval, others can check its verification status.'],
                ] as [$number, $heading, $description])
                    <li class="flex gap-3 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full bg-emerald-100 font-semibold text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200">{{ $number }}</span>
                        <div><h3 class="font-semibold text-zinc-950 dark:text-white">{{ $heading }}</h3><p class="mt-1 text-sm leading-6 text-zinc-600 dark:text-zinc-400">{{ $description }}</p></div>
                    </li>
                @endforeach
            </ol>
            <a href="{{ route('verify.index') }}" class="mt-6 inline-block text-sm font-semibold text-emerald-700 underline dark:text-emerald-300">Check an identity using a DID</a>
            <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">A verification status reflects an administrator's decision in this system; it does not independently prove that a document is genuine.</p>
        </section>
    </div>
    @endif
</x-layouts::app>
