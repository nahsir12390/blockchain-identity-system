<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-950">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-950">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <flux:sidebar.group :heading="__('Identity Network')" class="grid">
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
                    <flux:sidebar.group :heading="__('Security Operations')" class="grid">
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
                <p class="mt-2 text-sm font-semibold">{{ config('blockchain.enabled') ? 'Anchoring enabled' : 'Local proof mode' }}</p>
                <p class="mt-2 truncate font-mono text-[11px] text-zinc-400">{{ config('blockchain.network') }}</p>
            </div>

            <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog">
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
