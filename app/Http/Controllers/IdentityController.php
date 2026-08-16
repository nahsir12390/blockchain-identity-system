<?php

namespace App\Http\Controllers;

use App\Models\DigitalIdentity;
use App\Services\BlockchainIdentityRegistry;
use App\Services\IdentityHasher;
use App\Services\IdentityLedger;
use App\Services\IdentityWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IdentityController extends Controller
{
    public function __construct(
        private readonly IdentityLedger $ledger,
        private readonly BlockchainIdentityRegistry $blockchain,
        private readonly IdentityHasher $hasher,
        private readonly IdentityWorkflow $workflow,
    ) {}

    public function index()
    {
        $identity = Auth::user()->digitalIdentity?->load('ledgerEntries.actor');

        return view('identities.index', [
            'identity' => $identity,
        ]);
    }

    public function create()
    {
        abort_if(Auth::user()->digitalIdentity()->exists(), 403);

        return view('identities.create');
    }

    public function store(Request $request)
    {
        abort_if(Auth::user()->digitalIdentity()->exists(), 403);

        $validated = $request->validate([
            'legal_name' => ['required', 'string', 'max:255'],
            'identity_type' => ['required', 'string', 'max:80'],
            'identity_number' => ['required', 'string', 'max:120'],
            'wallet_address' => ['nullable', 'string', 'max:255'],
            'public_key' => ['nullable', 'string', 'max:500'],
            'document_reference' => ['nullable', 'string', 'max:255'],
            'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $documentPath = null;
        $documentHash = filled($validated['document_reference'] ?? null)
            ? $this->hasher->hash($validated['document_reference'])
            : null;
        $documentOriginalName = null;
        $documentMimeType = null;
        $documentSize = null;

        if ($request->hasFile('document_file')) {
            $document = $request->file('document_file');
            $documentHash = hash_file('sha256', $document->getRealPath());
            $documentOriginalName = $document->getClientOriginalName();
            $documentMimeType = $document->getMimeType();
            $documentSize = $document->getSize();
            $documentPath = $document->storeAs(
                'identity-documents/'.Auth::id(),
                Str::lower((string) Str::ulid()).'.'.$document->getClientOriginalExtension(),
                'local',
            );
        }

        $identity = DigitalIdentity::create([
            'user_id' => Auth::id(),
            'did' => 'did:trustwall:'.Str::lower(Str::ulid()),
            'legal_name' => $validated['legal_name'],
            'identity_type' => $validated['identity_type'],
            'wallet_address' => $validated['wallet_address'] ?? null,
            'public_key' => $validated['public_key'] ?? null,
            'identity_number_hash' => $this->hasher->hash($validated['identity_number']),
            'document_hash' => $documentHash,
            'document_path' => $documentPath,
            'document_original_name' => $documentOriginalName,
            'document_mime_type' => $documentMimeType,
            'document_size' => $documentSize,
            'status' => 'pending',
            'submitted_at' => now(),
        ]);

        $entry = $this->ledger->record($identity, 'identity.submitted', Auth::user(), [
            'identity_type' => $identity->identity_type,
            'document_uploaded' => filled($identity->document_path),
            'wallet_linked' => filled($identity->wallet_address),
        ]);

        $localBlockHash = hash('sha256', $entry->entry_hash.'|'.$identity->did);
        $anchor = $this->blockchain->anchor($identity, 'identity.submitted', $localBlockHash);

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
            'contract_address' => $anchor['contract_address'] ?? null,
            'transaction_hash' => $anchor['transaction_hash'] ?? $entry->entry_hash,
            'block_hash' => $localBlockHash,
        ]);

        return to_route('identity.index')->with('status', 'Identity submitted for verification.');
    }

    public function document(DigitalIdentity $identity)
    {
        abort_unless($identity->user_id === Auth::id() || Auth::user()->is_admin, 403);
        abort_if(blank($identity->document_path) || ! Storage::disk('local')->exists($identity->document_path), 404);

        return Storage::disk('local')->download(
            $identity->document_path,
            $identity->document_original_name ?: 'identity-document',
        );
    }

    public function resubmit(Request $request, DigitalIdentity $identity)
    {
        abort_unless($identity->user_id === Auth::id(), 403);
        $this->workflow->assertCan($identity, 'pending');

        $identity->update([
            'status' => 'pending',
            'rejection_reason' => null,
            'submitted_at' => now(),
        ]);

        $entry = $this->ledger->record($identity, 'identity.resubmitted', Auth::user());
        $localBlockHash = hash('sha256', $entry->entry_hash.'|resubmitted|'.$identity->did);
        $anchor = $this->blockchain->anchor($identity, 'identity.resubmitted', $localBlockHash);

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
                'contract_address' => $anchor['contract_address'] ?? null,
                'transaction_hash' => $anchor['transaction_hash'] ?? $entry->entry_hash,
                'block_hash' => $localBlockHash,
            ]);
        }

        return to_route('identity.index')->with('status', 'Identity resubmitted for review.');
    }

    public function certificate(DigitalIdentity $identity)
    {
        abort_unless($identity->user_id === Auth::id() || Auth::user()->is_admin, 403);
        abort_unless($identity->status === 'verified', 404);

        return view('identities.certificate', [
            'identity' => $identity->load('user', 'verifier'),
        ]);
    }
}
