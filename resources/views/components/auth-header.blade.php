@props([
    'title',
    'description',
])

<div class="flex w-full flex-col">
    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600 dark:text-emerald-300">DID ChainVault</p>
    <flux:heading size="xl" class="mt-2">{{ $title }}</flux:heading>
    <flux:subheading class="mt-2 leading-6">{{ $description }}</flux:subheading>
</div>
