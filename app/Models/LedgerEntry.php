<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'digital_identity_id',
    'actor_id',
    'action',
    'canonical_payload',
    'hash_algorithm',
    'payload_hash',
    'previous_hash',
    'entry_hash',
    'blockchain_network',
    'blockchain_tx_hash',
    'contract_address',
    'onchain_status',
    'onchain_receipt',
    'block_number',
    'metadata',
    'recorded_at',
])]
class LedgerEntry extends Model
{
    protected function casts(): array
    {
        return [
            'canonical_payload' => 'array',
            'metadata' => 'array',
            'onchain_receipt' => 'array',
            'recorded_at' => 'datetime',
        ];
    }

    public function digitalIdentity(): BelongsTo
    {
        return $this->belongsTo(DigitalIdentity::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
