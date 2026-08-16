<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Verify DID - {{ config('app.name') }}</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-zinc-950 text-white">
        <main class="mx-auto min-h-screen w-full max-w-6xl px-6 py-8">
            <nav class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="font-semibold">DID ChainVault</a>
                <a href="{{ route('login') }}" class="rounded-lg border border-white/10 px-4 py-2 text-sm text-zinc-200 hover:bg-white/10">Login</a>
            </nav>

            <section class="grid gap-8 py-12 lg:grid-cols-[1fr_.8fr] lg:items-end">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">Public verification portal</p>
                    <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-normal sm:text-5xl">Verify a decentralized identity proof</h1>
                    <p class="mt-4 max-w-2xl text-sm leading-6 text-zinc-300">
                        Search by DID, transaction hash, or block hash to confirm identity status, ledger continuity, and smart-contract anchoring.
                    </p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/[0.04] p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-400">Verifier endpoint</p>
                    <p class="mt-2 break-all font-mono text-xs text-zinc-300">{{ route('verify.index') }}</p>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        <div class="rounded-lg bg-zinc-900 p-4">
                            <p class="text-xs text-zinc-500">Network</p>
                            <p class="mt-1 text-sm font-semibold">{{ config('blockchain.network') }}</p>
                        </div>
                        <div class="rounded-lg bg-zinc-900 p-4">
                            <p class="text-xs text-zinc-500">Mode</p>
                            <p class="mt-1 text-sm font-semibold {{ config('blockchain.enabled') ? 'text-emerald-300' : 'text-amber-300' }}">{{ config('blockchain.enabled') ? 'On-chain' : 'Local' }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <form method="GET" action="{{ route('verify.index') }}" class="grid gap-3 rounded-2xl border border-white/10 bg-white/[0.06] p-4 shadow-2xl shadow-black/20 sm:grid-cols-[1fr_auto]">
                <input
                    name="q"
                    value="{{ $query }}"
                    class="min-h-12 rounded-lg border border-white/10 bg-zinc-900 px-4 font-mono text-sm text-white outline-none focus:border-emerald-400"
                    placeholder="did:trustwall:... or transaction hash"
                >
                <button class="rounded-lg bg-emerald-400 px-6 py-3 text-sm font-semibold text-zinc-950 hover:bg-emerald-300">Verify proof</button>
            </form>

            @if ($query !== '')
                <div class="mt-8">
                    @unless ($identity)
                        <section class="rounded-2xl border border-rose-400/30 bg-rose-400/10 p-6">
                            <h2 class="text-lg font-semibold text-rose-200">No matching identity proof found</h2>
                            <p class="mt-2 text-sm text-rose-100/80">The submitted DID or hash does not exist in this system's proof ledger.</p>
                        </section>
                    @else
                        <section class="grid gap-5 lg:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-white/[0.05] p-6 lg:col-span-2">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <p class="text-xs uppercase tracking-wide text-zinc-400">Verification result</p>
                                        <h2 class="mt-2 text-3xl font-semibold">{{ ucfirst($identity->status) }}</h2>
                                    </div>
                                    <span class="w-fit rounded-full px-3 py-1 text-xs font-semibold {{ $identity->status === 'verified' ? 'bg-emerald-400/10 text-emerald-300' : 'bg-amber-400/10 text-amber-300' }}">
                                        {{ $identity->status === 'verified' ? 'Trusted' : 'Not fully trusted' }}
                                    </span>
                                </div>

                                <dl class="mt-6 grid gap-4 text-sm">
                                    <div class="rounded-lg bg-zinc-900 p-4">
                                        <dt class="text-zinc-500">DID</dt>
                                        <dd class="mt-1 break-all font-mono text-zinc-100">{{ $identity->did }}</dd>
                                    </div>
                                    <div class="rounded-lg bg-zinc-900 p-4">
                                        <dt class="text-zinc-500">Transaction hash</dt>
                                        <dd class="mt-1 break-all font-mono text-zinc-100">{{ $identity->transaction_hash }}</dd>
                                    </div>
                                    <div class="rounded-lg bg-zinc-900 p-4">
                                        <dt class="text-zinc-500">Block hash</dt>
                                        <dd class="mt-1 break-all font-mono text-zinc-100">{{ $identity->block_hash }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="rounded-2xl border border-white/10 bg-white/[0.05] p-6">
                                <p class="text-xs uppercase tracking-wide text-zinc-400">Ledger integrity</p>
                                <p class="mt-3 text-2xl font-semibold {{ $ledgerValid ? 'text-emerald-300' : 'text-rose-300' }}">
                                    {{ $ledgerValid ? 'Valid chain' : 'Invalid chain' }}
                                </p>
                                <p class="mt-3 text-sm leading-6 text-zinc-300">
                                    {{ $ledgerValid ? 'Each block hash can be checked against the previous block.' : 'The proof ledger could not be validated.' }}
                                </p>
                                <div class="mt-5 grid gap-3">
                                    <div class="rounded-lg bg-zinc-900 p-4">
                                        <p class="text-xs text-zinc-500">Ledger blocks</p>
                                        <p class="mt-1 text-3xl font-semibold">{{ $identity->ledgerEntries->count() }}</p>
                                    </div>
                                    <div class="rounded-lg bg-zinc-900 p-4">
                                        <p class="text-xs text-zinc-500">Contract</p>
                                        <p class="mt-1 break-all font-mono text-xs">{{ $identity->contract_address ?: 'Local ledger only' }}</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    @endunless
                </div>
            @endif
        </main>
    </body>
</html>
