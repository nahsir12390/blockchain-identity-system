<x-layouts::app :title="__('Smart Contract')">
    <div class="space-y-6">
        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-950 text-white shadow-sm dark:border-zinc-700">
            <div class="grid gap-0 lg:grid-cols-[1.2fr_.8fr]">
                <div class="p-6 sm:p-8">
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-300">Ethereum-Compatible Registry</p>
                    <h1 class="mt-3 text-3xl font-semibold">Smart Contract Integration</h1>
                    <p class="mt-3 max-w-3xl text-sm leading-6 text-zinc-300">
                        Anchor DID lifecycle proofs on-chain while keeping personally identifiable information off-chain.
                    </p>
                </div>
                <div class="border-t border-white/10 bg-zinc-900 p-6 lg:border-s lg:border-t-0">
                    <p class="text-xs text-zinc-400">Contract status</p>
                    <p class="mt-3 text-2xl font-semibold {{ $blockchainReady ? 'text-emerald-300' : 'text-amber-300' }}">
                        {{ $blockchainReady ? 'Ready' : 'Fallback active' }}
                    </p>
                    <p class="mt-3 break-all font-mono text-xs text-zinc-300">{{ $deployment['address'] ?? config('blockchain.contract_address') ?? 'Not deployed yet' }}</p>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Network</p>
                <p class="mt-3 font-semibold text-zinc-950 dark:text-white">{{ config('blockchain.network') }}</p>
                <p class="mt-2 break-all text-xs text-zinc-500 dark:text-zinc-400">{{ config('blockchain.rpc_url') }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Contract artifact</p>
                <p class="mt-3 font-semibold text-zinc-950 dark:text-white">DecentralizedIdentityRegistry.sol</p>
                <p class="mt-2 break-all font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $contractPath }}</p>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">Blockchain mode</p>
                <p class="mt-3 font-semibold {{ $blockchainEnabled ? 'text-emerald-700 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-300' }}">
                    {{ $blockchainEnabled ? 'Enabled' : 'Disabled' }}
                </p>
                <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $blockchainEnabled ? 'The application will attempt on-chain anchors.' : 'Local ledger still records all proofs.' }}</p>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Deployment console</h2>
                <div class="mt-4 space-y-3 rounded-xl bg-zinc-950 p-4 font-mono text-xs text-emerald-200">
                    <p>npm run blockchain:compile</p>
                    <p>npm run blockchain:node</p>
                    <p>npm run blockchain:deploy</p>
                    <p>php artisan config:clear</p>
                </div>
            </div>
            <div class="rounded-xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Deployment files</h2>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">ABI</dt>
                        <dd class="mt-1 break-all font-mono text-xs text-zinc-950 dark:text-white">{{ $abiPath }}</dd>
                    </div>
                    <div>
                        <dt class="text-zinc-500 dark:text-zinc-400">Deployment</dt>
                        <dd class="mt-1 break-all font-mono text-xs text-zinc-950 dark:text-white">{{ $deploymentPath }}</dd>
                    </div>
                </dl>
            </div>
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
                <p class="text-xs font-semibold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">Registry ABI</p>
                <h2 class="mt-2 font-semibold text-zinc-950 dark:text-white">Contract functions</h2>
            </div>
            <div class="grid gap-0 divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach ([
                    'submitIdentity(bytes32 didHash, bytes32 identityHash, bytes32 documentHash)' => 'Creates a pending proof using only hashed identity data.',
                    'verifyIdentity(bytes32 didHash, bytes32 blockHash)' => 'Marks a DID as verified and anchors the local ledger block hash.',
                    'rejectIdentity(bytes32 didHash)' => 'Records a negative lifecycle decision without exposing personal data.',
                    'revokeIdentity(bytes32 didHash)' => 'Invalidates a previously verified decentralized identity proof.',
                ] as $signature => $description)
                    <div class="p-5">
                        <p class="break-all font-mono text-sm font-semibold text-zinc-950 dark:text-white">{{ $signature }}</p>
                        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="border-b border-zinc-200 p-5 dark:border-zinc-700">
                <h2 class="font-semibold text-zinc-950 dark:text-white">Recent identity anchor payloads</h2>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">These values are ready for smart contract calls.</p>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse ($recentIdentities as $identity)
                    <div class="grid gap-4 p-5 lg:grid-cols-[.8fr_1.2fr_1.2fr]">
                        <div>
                            <p class="font-medium text-zinc-950 dark:text-white">{{ $identity->user->name }}</p>
                            <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ ucfirst($identity->status) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">didHash</p>
                            <p class="mt-1 truncate font-mono text-xs text-zinc-950 dark:text-white">0x{{ hash('sha256', $identity->did) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">blockHash</p>
                            <p class="mt-1 truncate font-mono text-xs text-zinc-950 dark:text-white">{{ $identity->block_hash ? '0x'.$identity->block_hash : 'Pending' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-zinc-500 dark:text-zinc-400">No identity anchor payloads yet.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts::app>
