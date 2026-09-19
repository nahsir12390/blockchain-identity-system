<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ChainVault · Decentralized identity</title>
    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="chain-surface min-h-screen bg-zinc-950 text-white antialiased">
    <div class="mx-auto max-w-7xl px-5 sm:px-10">
        <nav aria-label="Main navigation" class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 py-6">
            <a href="{{ route('home') }}" class="flex items-center gap-3"><span class="flex size-10 items-center justify-center rounded-xl border border-emerald-300/30 bg-emerald-300/10 text-emerald-300"><x-app-logo-icon class="size-6" /></span><span class="text-lg font-semibold tracking-tight">ChainVault<span class="ml-2 font-mono text-xs font-normal text-zinc-400">/ IDENTITY</span></span></a>
            <div class="flex items-center gap-5 text-sm"><a href="{{ route('verify.index') }}" class="text-zinc-300 hover:text-emerald-300">Verify proof</a>@auth<a href="{{ route('dashboard') }}" class="chain-button secondary">Dashboard ↗︎</a>@else<a href="{{ route('login') }}" class="chain-button secondary">Sign in ↗︎</a>@endauth</div>
        </nav>
        <main>
            <section class="grid items-center gap-12 py-16 lg:grid-cols-[1.1fr_1fr] lg:py-24">
                <div>
                    <p class="chain-eyebrow flex items-center gap-3"><span class="size-2 rounded-full bg-emerald-300"></span>Decentralized identity infrastructure</p>
                    <h1 class="mt-7 text-5xl font-semibold leading-[1.08] tracking-tight sm:text-6xl lg:text-7xl">Your identity.<br>Your proof.<br><span class="text-emerald-300">Built on trust.</span></h1>
                    <p class="mt-7 max-w-lg text-base leading-8 text-zinc-400">A private identity. A verifiable record. Create your decentralized identifier and trace its journey through a tamper-evident blockchain ledger.</p>
                    <div class="mt-9 flex flex-wrap gap-3"><a href="{{ route('register') }}" class="chain-button">Create your identity <span aria-hidden="true">↗︎</span></a><a href="{{ route('verify.index') }}" class="chain-button secondary">Explore a proof <span aria-hidden="true">→</span></a></div>
                    <p class="mt-6 font-mono text-xs text-zinc-500">PRIVATE DOCUMENTS · PUBLICLY VERIFIABLE PROOFS</p>
                </div>
                <div class="chain-card chain-grid overflow-hidden">
                    <div class="flex items-center justify-between border-b border-white/10 px-6 py-5"><span class="chain-eyebrow">The proof network</span><span class="rounded-full border border-white/10 px-3 py-1 font-mono text-[10px] text-zinc-400">ILLUSTRATION</span></div>
                    <div class="px-4 py-6"><x-chain-network /></div>
                    <div class="mx-6 mb-6 rounded-xl border border-white/10 bg-zinc-950/80 p-5">
                        <div class="flex items-center justify-between gap-3"><span class="text-sm font-medium">One identity. Connected proofs.</span><span class="text-emerald-300" aria-hidden="true">◇</span></div>
                        <div class="mt-5 grid grid-cols-3 gap-2 border-t border-white/10 pt-4 font-mono text-[10px] text-zinc-400"><span><span class="mb-2 block text-emerald-300">01 / CREATE</span>Identity hash</span><span><span class="mb-2 block text-emerald-300">02 / RECORD</span>Ledger entry</span><span><span class="mb-2 block text-emerald-300">03 / ANCHOR</span>Contract proof</span></div>
                    </div>
                </div>
            </section>
            <section aria-label="How ChainVault protects your identity" class="grid gap-6 border-t border-white/10 py-10 md:grid-cols-3">
                @foreach ([['01','Privacy by design','Your documents stay off-chain. Only cryptographic proofs are anchored to the blockchain.'],['02','A traceable lifecycle','Follow submission, review, and verification through linked identity records.'],['03','Independently verifiable','Look up a DID or transaction hash to inspect its status and supporting proof.']] as [$number,$heading,$description])
                    <article class="rounded-xl border border-white/10 bg-white/[0.02] p-6"><span class="chain-eyebrow">{{ $number }} / PROTOCOL</span><h2 class="mt-4 text-lg font-semibold">{{ $heading }}</h2><p class="mt-3 text-sm leading-7 text-zinc-400">{{ $description }}</p></article>
                @endforeach
            </section>
        </main>
        <footer class="flex flex-wrap justify-between gap-3 border-t border-white/10 py-6 font-mono text-xs text-zinc-500"><span>ChainVault / Decentralized identity management</span><a href="{{ route('verify.index') }}" class="hover:text-emerald-300">Open public verifier ↗︎</a></footer>
    </div>
</body>
</html>
