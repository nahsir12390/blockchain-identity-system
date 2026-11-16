<?php

namespace App\Http\Controllers;

use App\Models\DigitalIdentity;
use App\Services\IdentityLedger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PublicVerificationController extends Controller
{
    private readonly IdentityLedger $ledger;

    public function __construct(IdentityLedger $ledger)
    {
        $this->ledger = $ledger;
    }

    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q'));
        $identity = null;
        $ledgerValid = null;

        if ($query !== '') {
            $identity = DigitalIdentity::query()
                ->with('ledgerEntries')
                ->where('did', $query)
                ->orWhere('transaction_hash', $query)
                ->orWhere('block_hash', $query)
                ->first();

            $ledgerValid = $identity ? $this->ledger->inspect($identity)['valid'] : false;
        }

        return view('verify.index', [
            'query' => $query,
            'identity' => $identity,
            'ledgerValid' => $ledgerValid,
        ]);
    }
}
