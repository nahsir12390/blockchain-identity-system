<?php

return [
    'enabled' => env('BLOCKCHAIN_ENABLED', false),
    'network' => env('BLOCKCHAIN_NETWORK', 'hardhat-localhost'),
    'rpc_url' => env('BLOCKCHAIN_RPC_URL', 'http://127.0.0.1:8545'),
    'private_key' => env('BLOCKCHAIN_PRIVATE_KEY'),
    'contract_address' => env('BLOCKCHAIN_CONTRACT_ADDRESS'),
    'deployment_path' => storage_path('app/blockchain/deployment.json'),
    'abi_path' => storage_path('app/blockchain/DecentralizedIdentityRegistry.abi.json'),
];
