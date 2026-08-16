<?php

namespace App\Services;

use App\Models\DigitalIdentity;

class IdentityWorkflow
{
    /**
     * @var array<string, array<int, string>>
     */
    private array $transitions = [
        'draft' => ['pending'],
        'pending' => ['verified', 'rejected'],
        'rejected' => ['pending'],
        'verified' => ['revoked'],
        'revoked' => [],
    ];

    public function assertCan(DigitalIdentity $identity, string $nextStatus): void
    {
        abort_unless($this->can($identity, $nextStatus), 422, $this->message($identity->status, $nextStatus));
    }

    public function can(DigitalIdentity $identity, string $nextStatus): bool
    {
        return in_array($nextStatus, $this->transitions[$identity->status] ?? [], true);
    }

    private function message(string $currentStatus, string $nextStatus): string
    {
        return "Identity cannot move from {$currentStatus} to {$nextStatus}.";
    }
}
