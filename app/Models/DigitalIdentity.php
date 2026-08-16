<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'did',
    'legal_name',
    'wallet_address',
    'public_key',
    'identity_type',
    'identity_number_hash',
    'document_hash',
    'document_path',
    'document_original_name',
    'document_mime_type',
    'document_size',
    'status',
    'blockchain_network',
    'contract_address',
    'block_hash',
    'transaction_hash',
    'rejection_reason',
    'submitted_at',
    'verified_at',
    'verified_by',
])]
class DigitalIdentity extends Model
{
    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(LedgerEntry::class)->latest('block_number');
    }

    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }
}
