@props([
    'sidebar' => false,
])

@if($sidebar)
    <a {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-xl border border-emerald-400/20 bg-zinc-950 px-3 py-3 text-white shadow-sm transition hover:border-emerald-300/40']) }}>
        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-emerald-400 text-zinc-950">
            <x-app-logo-icon class="size-5 fill-current" />
        </span>
        <span class="min-w-0">
            <span class="block truncate text-sm font-semibold leading-5">DID ChainVault</span>
            <span class="block truncate font-mono text-[11px] text-emerald-200">zero-trust identity</span>
        </span>
    </a>
@else
    <flux:brand name="DID ChainVault" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-zinc-950 text-emerald-300 dark:bg-emerald-400 dark:text-zinc-950">
            <x-app-logo-icon class="size-5 fill-current text-white dark:text-black" />
        </x-slot>
    </flux:brand>
@endif
