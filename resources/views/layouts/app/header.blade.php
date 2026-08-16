<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
        <flux:header container class="border-b border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <flux:sidebar.toggle class="mr-2 lg:hidden" icon="bars-2" inset="left" />

            <x-app-logo href="{{ route('dashboard') }}" />

            <flux:navbar class="-mb-px max-lg:hidden">
                <flux:navbar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                    {{ __('Command Center') }}
                </flux:navbar.item>
                <flux:navbar.item icon="layout-grid" :href="route('identity.index')" :current="request()->routeIs('identity.*')">
                    {{ __('DID Wallet') }}
                </flux:navbar.item>
            </flux:navbar>

            <flux:spacer />

            <flux:navbar class="me-1.5 space-x-0.5 py-0! rtl:space-x-reverse">
                <flux:tooltip :content="__('Public verifier')" position="bottom">
                    <flux:navbar.item
                        class="h-10 [&>div>svg]:size-5"
                        icon="book-open-text"
                        :href="route('verify.index')"
                        target="_blank"
                        :label="__('Public verifier')"
                    />
                </flux:tooltip>
            </flux:navbar>

            <x-desktop-user-menu />
        </flux:header>

        <flux:sidebar collapsible="mobile" sticky class="border-e border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950 lg:hidden">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" />
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Identity Network')">
                    <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                        {{ __('Command Center') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="layout-grid" :href="route('identity.index')" :current="request()->routeIs('identity.*')">
                        {{ __('DID Wallet') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="book-open-text" :href="route('verify.index')" target="_blank">
                        {{ __('Public Verifier') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                @if (auth()->user()->is_admin)
                    <flux:sidebar.group :heading="__('Security Operations')">
                        <flux:sidebar.item icon="folder-git-2" :href="route('admin.identities.index')" :current="request()->routeIs('admin.identities.*')">
                            {{ __('Review Queue') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="book-open-text" :href="route('admin.audit.index')" :current="request()->routeIs('admin.audit.*')">
                            {{ __('Threat Audit') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="book-open-text" :href="route('admin.ledger.index')" :current="request()->routeIs('admin.ledger.*')">
                            {{ __('Proof Explorer') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="folder-git-2" :href="route('admin.smart-contract.index')" :current="request()->routeIs('admin.smart-contract.*')">
                            {{ __('Contract Console') }}
                        </flux:sidebar.item>
                        <flux:sidebar.item icon="layout-grid" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')">
                            {{ __('Access Control') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.nav>

            <flux:spacer />

            <div class="mx-3 mb-3 rounded-xl border border-emerald-400/20 bg-zinc-950 p-4 text-white">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">Chain Status</p>
                <p class="mt-2 text-sm font-semibold">{{ config('blockchain.enabled') ? 'Anchoring online' : 'Local proof mode' }}</p>
                <p class="mt-2 truncate font-mono text-[11px] text-zinc-400">{{ config('blockchain.network') }}</p>
            </div>
        </flux:sidebar>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
