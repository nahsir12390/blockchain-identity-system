<?php

namespace App\Services;

use App\Models\DigitalIdentity;
use App\Models\LedgerEntry;
use App\Models\User;

class IdentityLedger
{
    private const HASH_ALGORITHM = 'sha256';

    /**
     * @param  array<string, mixed>  $metadata
     */
    public function record(DigitalIdentity $identity, string $action, ?User $actor = null, array $metadata = []): LedgerEntry
    {
        $lastEntry = $identity->ledgerEntries()->latest('block_number')->first();
        $blockNumber = ((int) ($lastEntry?->block_number ?? 0)) + 1;
        $recordedAt = now();
        $metadata = $this->canonicalize($metadata);

        $canonicalPayload = [
            'identity_id' => $identity->id,
            'did' => $identity->did,
            'status' => $identity->status,
            'action' => $action,
            'actor_id' => $actor?->id,
            'metadata' => $metadata,
            'previous_hash' => $lastEntry?->entry_hash,
            'block_number' => $blockNumber,
            'recorded_at' => $recordedAt->toJSON(),
        ];

        $payloadHash = $this->hash($canonicalPayload);

        $entryHash = $this->hash([
            'payload_hash' => $payloadHash,
            'previous_hash' => $lastEntry?->entry_hash,
            'block_number' => $blockNumber,
            'algorithm' => self::HASH_ALGORITHM,
        ]);

        return LedgerEntry::create([
            'digital_identity_id' => $identity->id,
            'actor_id' => $actor?->id,
            'action' => $action,
            'canonical_payload' => $canonicalPayload,
            'hash_algorithm' => self::HASH_ALGORITHM,
            'payload_hash' => $payloadHash,
            'previous_hash' => $lastEntry?->entry_hash,
            'entry_hash' => $entryHash,
            'block_number' => $blockNumber,
            'metadata' => $metadata,
            'recorded_at' => $recordedAt,
        ]);
    }

    /**
     * @return array{valid: bool, status: string, issues: array<int, string>, blocks: int, latest_hash: string|null}
     */
    public function inspect(DigitalIdentity $identity): array
    {
        $entries = $identity->ledgerEntries()->oldest('block_number')->get();
        $issues = [];
        $previousHash = null;
        $expectedBlockNumber = 1;

        if ($entries->isEmpty()) {
            return [
                'valid' => false,
                'status' => 'empty',
                'issues' => ['No ledger blocks were found for this identity.'],
                'blocks' => 0,
                'latest_hash' => null,
            ];
        }

        foreach ($entries as $entry) {
            if ((int) $entry->block_number !== $expectedBlockNumber) {
                $issues[] = "Expected block {$expectedBlockNumber}, found block {$entry->block_number}.";
            }

            if ($entry->previous_hash !== $previousHash) {
                $issues[] = "Block {$entry->block_number} does not point to the previous block hash.";
            }

            if (blank($entry->payload_hash) || blank($entry->entry_hash)) {
                $issues[] = "Block {$entry->block_number} has an incomplete hash payload.";
            }

            if (blank($entry->canonical_payload)) {
                $issues[] = "Block {$entry->block_number} was created with the legacy hash format and cannot be fully recomputed.";
            } else {
                $expectedPayloadHash = $this->hash($entry->canonical_payload);
                $expectedEntryHash = $this->hash([
                    'payload_hash' => $expectedPayloadHash,
                    'previous_hash' => $entry->previous_hash,
                    'block_number' => (int) $entry->block_number,
                    'algorithm' => $entry->hash_algorithm ?: self::HASH_ALGORITHM,
                ]);

                if (! hash_equals($entry->payload_hash, $expectedPayloadHash)) {
                    $issues[] = "Block {$entry->block_number} payload hash has been altered.";
                }

                if (! hash_equals($entry->entry_hash, $expectedEntryHash)) {
                    $issues[] = "Block {$entry->block_number} entry hash has been altered.";
                }
            }

            $previousHash = $entry->entry_hash;
            $expectedBlockNumber++;
        }

        $latestHash = $entries->last()?->entry_hash;

        $latestEntry = $entries->last();
        $expectedTransactionHash = $latestEntry?->blockchain_tx_hash ?: $latestHash;

        if ($identity->transaction_hash && $identity->status === 'verified' && $identity->transaction_hash !== $expectedTransactionHash) {
            $issues[] = 'The verified identity transaction hash does not match the latest local or on-chain ledger proof.';
        }

        return [
            'valid' => $issues === [],
            'status' => $issues === [] ? 'valid' : 'compromised',
            'issues' => $issues,
            'blocks' => $entries->count(),
            'latest_hash' => $latestHash,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    private function hash(array $payload): string
    {
        return hash(self::HASH_ALGORITHM, json_encode($this->canonicalize($payload), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param  array<string, mixed>  $value
     * @return array<string, mixed>
     */
    private function canonicalize(array $value): array
    {
        ksort($value);

        foreach ($value as $key => $item) {
            if (is_array($item)) {
                $value[$key] = $this->canonicalize($item);
            }
        }

        return $value;
    }
}
