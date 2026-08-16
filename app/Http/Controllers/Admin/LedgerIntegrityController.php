<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalIdentity;
use App\Services\IdentityLedger;

class LedgerIntegrityController extends Controller
{
    public function __construct(private readonly IdentityLedger $ledger) {}

    public function index()
    {
        $identities = DigitalIdentity::query()
            ->with('user')
            ->withCount('ledgerEntries')
            ->latest()
            ->get()
            ->map(function (DigitalIdentity $identity): array {
                return [
                    'identity' => $identity,
                    'inspection' => $this->ledger->inspect($identity),
                ];
            });

        return view('admin.ledger.index', [
            'identities' => $identities,
            'validCount' => $identities->where('inspection.valid', true)->count(),
            'invalidCount' => $identities->where('inspection.valid', false)->count(),
        ]);
    }
}
