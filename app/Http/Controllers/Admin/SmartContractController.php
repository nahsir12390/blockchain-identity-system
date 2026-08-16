<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DigitalIdentity;
use App\Services\BlockchainIdentityRegistry;

class SmartContractController extends Controller
{
    public function __construct(private readonly BlockchainIdentityRegistry $blockchain) {}

    public function index()
    {
        $deployment = null;

        if (is_file(config('blockchain.deployment_path'))) {
            $deployment = json_decode((string) file_get_contents(config('blockchain.deployment_path')), true);
        }

        return view('admin.smart-contract.index', [
            'contractPath' => base_path('contracts/DecentralizedIdentityRegistry.sol'),
            'abiPath' => config('blockchain.abi_path'),
            'deploymentPath' => config('blockchain.deployment_path'),
            'deployment' => $deployment,
            'blockchainReady' => $this->blockchain->isReady(),
            'blockchainEnabled' => config('blockchain.enabled'),
            'recentIdentities' => DigitalIdentity::query()
                ->with('user')
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }
}
