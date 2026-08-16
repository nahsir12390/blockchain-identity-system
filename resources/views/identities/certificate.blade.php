<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Identity Certificate - {{ config('app.name') }}</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            @media print {
                .no-print {
                    display: none !important;
                }

                body {
                    background: #ffffff !important;
                    padding: 0 !important;
                }

                main {
                    box-shadow: none !important;
                    border-radius: 0 !important;
                }
            }
        </style>
    </head>
    <body class="min-h-screen bg-zinc-950 px-4 py-8 text-zinc-950">
        <div class="no-print mx-auto mb-5 flex max-w-5xl justify-end gap-3">
            <button onclick="window.print()" class="rounded-lg bg-emerald-400 px-4 py-2 text-sm font-semibold text-zinc-950 hover:bg-emerald-300">Print or save PDF</button>
            <a href="{{ route('identity.index') }}" class="rounded-lg border border-white/10 bg-white/10 px-4 py-2 text-sm font-medium text-white hover:bg-white/15">Back</a>
        </div>

        <main class="mx-auto max-w-5xl overflow-hidden rounded-2xl border border-zinc-300 bg-white shadow-2xl shadow-black/40">
            <header class="bg-zinc-950 p-8 text-white">
                <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-emerald-300">Verified DID Certificate</p>
                        <h1 class="mt-4 max-w-3xl text-3xl font-semibold leading-tight">
                            Blockchain-Based Cybersecurity System for Decentralized Identity Management
                        </h1>
                        <p class="mt-4 max-w-2xl text-sm leading-6 text-zinc-300">
                            This certificate confirms that the identity below has been verified and recorded in a tamper-evident proof ledger.
                        </p>
                    </div>
                    <div class="w-fit rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3">
                        <p class="text-xs uppercase tracking-wide text-emerald-200">Status</p>
                        <p class="mt-1 text-xl font-semibold text-emerald-200">Verified</p>
                    </div>
                </div>
            </header>

            <section class="grid gap-4 p-8 md:grid-cols-3">
                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Certificate holder</p>
                    <p class="mt-2 text-xl font-semibold">{{ $identity->legal_name }}</p>
                    <p class="mt-1 break-all text-sm text-zinc-600">{{ $identity->user->email }}</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Verified date</p>
                    <p class="mt-2 text-xl font-semibold">{{ $identity->verified_at?->format('M d, Y') }}</p>
                    <p class="mt-1 text-sm text-zinc-600">{{ $identity->verified_at?->format('H:i') }}</p>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Identity type</p>
                    <p class="mt-2 text-xl font-semibold">{{ $identity->identity_type }}</p>
                    <p class="mt-1 text-sm text-zinc-600">Zero-trust proof record</p>
                </div>
            </section>

            <section class="mx-8 rounded-2xl border border-zinc-200">
                <div class="border-b border-zinc-200 bg-zinc-50 px-5 py-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Cryptographic proof passport</p>
                </div>
                <dl class="grid gap-0 divide-y divide-zinc-200 text-sm">
                    <div class="p-5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Decentralized Identifier</dt>
                        <dd class="mt-2 break-all font-mono">{{ $identity->did }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Transaction Hash</dt>
                        <dd class="mt-2 break-all font-mono">{{ $identity->transaction_hash ?: 'Local ledger anchor' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Block Hash</dt>
                        <dd class="mt-2 break-all font-mono">{{ $identity->block_hash ?: 'Pending block hash' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Document Proof Hash</dt>
                        <dd class="mt-2 break-all font-mono">{{ $identity->document_hash ?: 'No document hash attached' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="grid gap-6 p-8 md:grid-cols-2">
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-800">Verified by</p>
                    <p class="mt-2 text-sm font-semibold text-emerald-950">{{ $identity->verifier?->name ?? 'System Administrator' }}</p>
                    <p class="mt-2 text-xs leading-5 text-emerald-900">Verification decisions are recorded as ledger entries for later audit and public proof checking.</p>
                </div>
                <div class="rounded-xl border border-zinc-200 bg-zinc-50 p-5">
                    <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500">Public verification</p>
                    <p class="mt-2 break-all text-sm">{{ route('verify.index', ['q' => $identity->did]) }}</p>
                </div>
            </section>
        </main>
    </body>
</html>
