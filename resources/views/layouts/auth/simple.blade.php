<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-950 antialiased text-white">
        <div class="grid min-h-svh lg:grid-cols-[1.05fr_.95fr]">
            <section class="chain-grid relative hidden overflow-hidden border-r border-white/10 bg-zinc-950 p-10 lg:flex lg:flex-col lg:justify-between">
                <div>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                        <span class="flex size-11 items-center justify-center rounded-xl bg-emerald-400 text-zinc-950">
                            <x-app-logo-icon class="size-6 fill-current" />
                        </span>
                        <span>
                            <span class="block text-base font-semibold">DID ChainVault</span>
                            <span class="block font-mono text-xs text-emerald-200">zero-trust identity access</span>
                        </span>
                    </a>

                    <div class="mt-20 max-w-xl">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">Secure Identity Gateway</p>
                        <h1 class="mt-4 text-4xl font-semibold leading-tight">Your identity starts here.</h1>
                        <p class="mt-5 text-sm leading-7 text-zinc-300">
                            Authenticate securely, enroll a DID, upload private proof documents, and track every identity decision through the tamper-evident ledger.
                        </p>
                    </div>
                </div>

                <x-chain-network />
                <div class="grid gap-3">
                    @foreach ([
                        ['01', 'Password and session protection'],
                        ['02', 'Two-factor ready account security'],
                        ['03', 'DID proof ledger and smart-contract anchors'],
                    ] as [$step, $label])
                        <div class="flex items-center gap-3 rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <span class="flex size-10 items-center justify-center rounded-lg bg-emerald-400/10 font-mono text-xs font-semibold text-emerald-200">{{ $step }}</span>
                            <span class="text-sm text-zinc-200">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="flex min-h-svh items-center justify-center bg-zinc-100 px-5 py-8 text-zinc-950 dark:bg-zinc-950 dark:text-white sm:px-8">
                <div class="w-full max-w-md">
                    <a href="{{ route('home') }}" class="mb-8 flex items-center justify-center gap-3 lg:hidden">
                        <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-400 text-zinc-950">
                            <x-app-logo-icon class="size-5 fill-current" />
                        </span>
                        <span class="font-semibold">DID ChainVault</span>
                    </a>

                    <div class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-xl shadow-black/5 dark:border-zinc-800 dark:bg-zinc-900 sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </section>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
