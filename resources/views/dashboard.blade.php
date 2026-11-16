<x-layouts::app :title="__('Dashboard')">
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
</x-layouts::app>
