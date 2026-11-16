<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Digital Identity - {{ config('app.name') }}</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-950 text-white">
    <main class="mx-auto w-full max-w-4xl px-5 py-8 sm:px-8">
        <nav class="flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="font-semibold">DID ChainVault</a>
            <a href="{{ route('login') }}" class="rounded-lg border border-white/20 px-4 py-2 text-sm hover:bg-white/10">Log in</a>
        </nav>

        <header class="py-12">
            <p class="text-sm font-medium text-emerald-300">Public identity check</p>
            <h1 class="mt-3 text-3xl font-semibold sm:text-4xl">Check a Digital Identity</h1>
            <p class="mt-4 max-w-2xl text-sm leading-6 text-zinc-300">Enter a Digital Identity ID (DID) to see whether it has been approved by an administrator in DID ChainVault.</p>
        </header>

        <form action="{{ route('verify.index') }}" method="GET" class="rounded-2xl border border-white/10 bg-white/5 p-5 sm:p-7">
            <label for="q" class="text-sm font-semibold">Digital Identity ID (DID)</label>
            <p class="mt-1 text-xs text-zinc-400">You can copy this ID from the My Identity page.</p>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row">
                <input id="q" name="q" value="{{ $query }}" placeholder="did:trustwall:..." maxlength="255" class="min-h-12 min-w-0 flex-1 rounded-lg border border-white/20 bg-zinc-900 px-4 font-mono text-sm text-white focus:border-emerald-400">
                <button type="submit" class="min-h-12 rounded-lg bg-emerald-400 px-6 py-3 text-sm font-semibold text-zinc-950 hover:bg-emerald-300">Check Status</button>
            </div>
        </form>

        @if ($query !== '')
            <div class="mt-6" aria-live="polite">
                @unless ($identity)
                    <section class="rounded-2xl border border-amber-400/30 bg-amber-400/10 p-6">
                        <h2 class="text-xl font-semibold text-amber-200">No matching identity found</h2>
                        <p class="mt-2 text-sm text-zinc-200">Please check the DID and try again. This system cannot confirm a record for the value you entered.</p>
                    </section>
                @else
                    @php
                        $isVerified = $identity->status === 'verified' && $ledgerValid;
                        $resultTitle = ! $ledgerValid ? 'Proof record could not be validated' : match ($identity->status) {
                            'verified' => 'Identity approved',
                            'pending' => 'Awaiting administrator review',
                            'rejected' => 'Identity not approved',
                            'revoked' => 'Verification revoked',
                            default => 'Not verified',
                        };
                        $resultDescription = ! $ledgerValid
                            ? 'The record history could not be verified. Do not rely on this verification result.'
                            : match ($identity->status) {
                                'verified' => 'An administrator approved this identity in DID ChainVault.',
                                'pending' => 'This identity has been submitted but has not yet been approved.',
                                'rejected' => 'The administrator did not approve this identity.',
                                'revoked' => 'An earlier approval was revoked and is no longer valid.',
                                default => 'There is no current approval for this identity.',
                            };
                    @endphp

                    <section class="rounded-2xl border p-6 sm:p-8 {{ $isVerified ? 'border-emerald-400/40 bg-emerald-400/10' : 'border-amber-400/30 bg-amber-400/10' }}">
                        <p class="text-xs font-semibold uppercase tracking-wide text-zinc-300">Verification result</p>
                        <h2 class="mt-3 text-2xl font-semibold {{ $isVerified ? 'text-emerald-300' : 'text-amber-200' }}">{{ $resultTitle }}</h2>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-zinc-200">{{ $resultDescription }}</p>
                        <div class="mt-6 rounded-xl bg-zinc-950/60 p-4">
                            <p class="text-xs text-zinc-400">Digital Identity ID (DID)</p>
                            <p class="mt-2 break-all font-mono text-sm">{{ $identity->did }}</p>
                        </div>
                        <div class="mt-4 rounded-xl bg-zinc-950/60 p-4">
                            <p class="text-sm font-semibold">{{ $ledgerValid ? 'Record history: checks passed' : 'Record history: check failed' }}</p>
                            <p class="mt-1 text-xs leading-5 text-zinc-400">This checks the integrity of the local proof records. It is not proof that a transaction has been confirmed on a public blockchain.</p>
                        </div>
                    </section>
                @endunless
            </div>
        @endif

        <p class="mt-8 text-xs leading-6 text-zinc-400">Important: This page reports what DID ChainVault has recorded. It does not independently confirm a person's real-world identity or authenticate the original ID document.</p>
    </main>
</body>
</html>
