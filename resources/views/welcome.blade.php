<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }}</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-zinc-950 text-white">
        <main class="mx-auto flex min-h-screen w-full max-w-6xl flex-col px-6 py-8">
            <nav class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="font-semibold">DID ChainVault</a>
                <div class="flex items-center gap-4 text-sm">
                    <a href="{{ route('verify.index') }}" class="text-zinc-300 hover:text-white">Verify DID</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-lg bg-white px-4 py-2 font-medium text-zinc-950 hover:bg-zinc-200">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-lg bg-white px-4 py-2 font-medium text-zinc-950 hover:bg-zinc-200">Login</a>
                    @endauth
                </div>
            </nav>

            <section class="grid flex-1 items-center gap-10 py-16 lg:grid-cols-[1.1fr_0.9fr]">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-300">Final year project system</p>
                    <h1 class="mt-4 max-w-4xl text-4xl font-semibold leading-tight sm:text-5xl">
                        Blockchain-Based Cybersecurity System for Decentralized Identity Management
                    </h1>
                    <p class="mt-5 max-w-2xl text-base leading-7 text-zinc-300">
                        A secure identity platform that issues decentralized identifiers, hashes sensitive records, verifies identity submissions, and records every decision in a tamper-evident proof ledger.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('verify.index') }}" class="rounded-lg bg-emerald-400 px-5 py-3 text-sm font-semibold text-zinc-950 hover:bg-emerald-300">Verify an identity</a>
                        <a href="{{ route('register') }}" class="rounded-lg border border-white/20 px-5 py-3 text-sm font-semibold text-white hover:bg-white/10">Create account</a>
                    </div>
                </div>

                <div class="rounded-2xl border border-emerald-400/20 bg-white/5 p-6 shadow-2xl shadow-black/40">
                    <div class="rounded-xl bg-zinc-900 p-5">
                        <div class="flex items-center justify-between border-b border-white/10 pb-4">
                            <div>
                                <p class="text-xs text-zinc-400">Proof ledger</p>
                                <p class="mt-1 font-mono text-sm text-emerald-300">GENESIS -> DID -> VERIFIED</p>
                            </div>
                            <span class="rounded-full bg-emerald-400/10 px-3 py-1 text-xs font-semibold text-emerald-300">Valid</span>
                        </div>
                        <div class="mt-5 grid grid-cols-3 gap-2">
                            @foreach (['Identity hash', 'Ledger block', 'Contract anchor'] as $label)
                                <div class="rounded-lg border border-white/10 bg-white/[0.03] p-3">
                                    <span class="block h-1.5 w-10 rounded-full bg-emerald-300"></span>
                                    <p class="mt-3 text-[11px] font-semibold uppercase tracking-wide text-zinc-400">{{ $label }}</p>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-5 space-y-4 text-sm">
                            <div>
                                <p class="text-zinc-500">Decentralized Identifier</p>
                                <p class="mt-1 break-all font-mono text-zinc-100">did:trustwall:01k...</p>
                            </div>
                            <div>
                                <p class="text-zinc-500">Transaction Hash</p>
                                <p class="mt-1 break-all font-mono text-zinc-100">9f8a0c2e6b4a...</p>
                            </div>
                            <div>
                                <p class="text-zinc-500">Cybersecurity Controls</p>
                                <p class="mt-1 text-zinc-100">2FA, password confirmation, hashed identity records, audit trail</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </body>
</html>
