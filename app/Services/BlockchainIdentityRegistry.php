<?php

namespace App\Services;

use App\Models\DigitalIdentity;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Throwable;

class BlockchainIdentityRegistry
{
    /**
     * @return array<string, mixed>|null
     */
    public function anchor(DigitalIdentity $identity, string $action, string $localBlockHash): ?array
    {
        if (! config('blockchain.enabled')) {
            return null;
        }

        if (! is_file(config('blockchain.deployment_path')) || ! is_file(config('blockchain.abi_path'))) {
            Log::warning('Blockchain anchoring skipped because the contract deployment files are missing.');

            return null;
        }

        if (blank(config('blockchain.private_key'))) {
            Log::warning('Blockchain anchoring skipped because BLOCKCHAIN_PRIVATE_KEY is not configured.');

            return null;
        }

        $process = new Process([
            'node',
            base_path('scripts/anchor-identity.js'),
            $action,
            $this->bytes32($identity->did),
            '0x'.$identity->identity_number_hash,
            $identity->document_hash ? '0x'.$identity->document_hash : '0x'.str_repeat('0', 64),
            '0x'.$localBlockHash,
        ], base_path());

        $process->setTimeout(90);

        try {
            $process->mustRun();

            /** @var array<string, mixed> $result */
            $result = json_decode($process->getOutput(), true, 512, JSON_THROW_ON_ERROR);

            return $result;
        } catch (Throwable $exception) {
            Log::error('Blockchain anchoring failed.', [
                'identity_id' => $identity->id,
                'action' => $action,
                'message' => $exception->getMessage(),
                'output' => $process->getOutput(),
                'error_output' => $process->getErrorOutput(),
            ]);

            return null;
        }
    }

    public function isReady(): bool
    {
        return (bool) config('blockchain.enabled')
            && is_file(config('blockchain.deployment_path'))
            && is_file(config('blockchain.abi_path'))
            && filled(config('blockchain.private_key'));
    }

    private function bytes32(string $value): string
    {
        return '0x'.hash('sha256', $value);
    }
}
