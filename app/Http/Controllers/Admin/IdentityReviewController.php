<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalIdentity;
use App\Services\BlockchainIdentityRegistry;
use App\Services\IdentityLedger;
use App\Services\IdentityWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IdentityReviewController extends Controller
{
    public function __construct(
        private readonly IdentityLedger $ledger,
        private readonly BlockchainIdentityRegistry $blockchain,
        private readonly IdentityWorkflow $workflow,
    ) {}

    public function index()
    {
        return view('admin.identities.index', [
            'identities' => DigitalIdentity::query()
                ->with('user', 'verifier')
                ->latest()
                ->paginate(15),
        ]);
    }

    public function show(DigitalIdentity $identity)
    {
        return view('admin.identities.show', [
            'identity' => $identity->load('user', 'verifier', 'ledgerEntries.actor'),
        ]);
    }

    public function verify(DigitalIdentity $identity)
    {
        $this->workflow->assertCan($identity, 'verified');

        $identity->update([
            'status' => 'verified',
            'verified_at' => now(),
            'verified_by' => Auth::id(),
            'rejection_reason' => null,
        ]);

        $entry = $this->ledger->record($identity, 'identity.verified', Auth::user());

        $localBlockHash = hash('sha256', $entry->entry_hash.'|verified|'.$identity->did);
        $anchor = $this->blockchain->anchor($identity, 'identity.verified', $localBlockHash);

        if ($anchor) {
            $entry->update([
                'blockchain_network' => $anchor['network'] ?? config('blockchain.network'),
                'blockchain_tx_hash' => $anchor['transaction_hash'] ?? null,
                'contract_address' => $anchor['contract_address'] ?? null,
                'onchain_status' => $anchor['status'] ?? null,
                'onchain_receipt' => $anchor,
            ]);
        }

        $identity->update([
            'blockchain_network' => $anchor['network'] ?? config('blockchain.network', 'Local Proof Chain'),
            'contract_address' => $anchor['contract_address'] ?? $identity->contract_address,
            'transaction_hash' => $anchor['transaction_hash'] ?? $entry->entry_hash,
            'block_hash' => $localBlockHash,
        ]);

        return to_route('admin.identities.show', $identity)->with('status', 'Identity verified and recorded on the proof ledger.');
    }

    public function reject(Request $request, DigitalIdentity $identity)
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->workflow->assertCan($identity, 'rejected');

        $identity->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'verified_at' => null,
            'verified_by' => null,
        ]);

        $entry = $this->ledger->record($identity, 'identity.rejected', Auth::user(), [
            'reason_hash' => hash_hmac('sha256', $validated['rejection_reason'], (string) config('app.key')),
        ]);
        $localBlockHash = hash('sha256', $entry->entry_hash.'|rejected|'.$identity->did);
        $anchor = $this->blockchain->anchor($identity, 'identity.rejected', $localBlockHash);

        if ($anchor) {
            $entry->update([
                'blockchain_network' => $anchor['network'] ?? config('blockchain.network'),
                'blockchain_tx_hash' => $anchor['transaction_hash'] ?? null,
                'contract_address' => $anchor['contract_address'] ?? null,
                'onchain_status' => $anchor['status'] ?? null,
                'onchain_receipt' => $anchor,
            ]);

            $identity->update([
                'blockchain_network' => $anchor['network'] ?? config('blockchain.network'),
                'contract_address' => $anchor['contract_address'] ?? $identity->contract_address,
                'transaction_hash' => $anchor['transaction_hash'] ?? $entry->entry_hash,
                'block_hash' => $localBlockHash,
            ]);
        }

        return to_route('admin.identities.show', $identity)->with('status', 'Identity rejected with audit proof.');
    }

    public function revoke(DigitalIdentity $identity)
    {
        $this->workflow->assertCan($identity, 'revoked');

        $identity->update([
            'status' => 'revoked',
        ]);

        $entry = $this->ledger->record($identity, 'identity.revoked', Auth::user());
        $localBlockHash = hash('sha256', $entry->entry_hash.'|revoked|'.$identity->did);
        $anchor = $this->blockchain->anchor($identity, 'identity.revoked', $localBlockHash);

        if ($anchor) {
            $entry->update([
                'blockchain_network' => $anchor['network'] ?? config('blockchain.network'),
                'blockchain_tx_hash' => $anchor['transaction_hash'] ?? null,
                'contract_address' => $anchor['contract_address'] ?? null,
                'onchain_status' => $anchor['status'] ?? null,
                'onchain_receipt' => $anchor,
            ]);

            $identity->update([
                'blockchain_network' => $anchor['network'] ?? config('blockchain.network'),
                'contract_address' => $anchor['contract_address'] ?? $identity->contract_address,
                'transaction_hash' => $anchor['transaction_hash'] ?? $entry->entry_hash,
                'block_hash' => $localBlockHash,
            ]);
        }

        return to_route('admin.identities.show', $identity)->with('status', 'Identity revoked and written to the proof ledger.');
    }
}
