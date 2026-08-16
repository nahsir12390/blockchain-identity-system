# Blockchain Setup

This project uses a local Ethereum-compatible Hardhat chain for development.
Only hashes are written on-chain. Raw identity numbers and documents remain off-chain.

## Commands

Compile the Solidity contract:

```bash
npm run blockchain:compile
```

Start the local blockchain node in a separate terminal:

```bash
npm run blockchain:node
```

Deploy the identity registry contract:

```bash
npm run blockchain:deploy
```

The deployment command writes:

- `storage/app/blockchain/DecentralizedIdentityRegistry.abi.json`
- `storage/app/blockchain/deployment.json`

## Laravel Environment

For the local Hardhat network, use:

```env
BLOCKCHAIN_ENABLED=true
BLOCKCHAIN_NETWORK=hardhat-localhost
BLOCKCHAIN_RPC_URL=http://127.0.0.1:8545
BLOCKCHAIN_PRIVATE_KEY=ac0974bec39a17e36ba4a6b4d238ff944bacb478cbed5efcae784d7bf4f2ff80
BLOCKCHAIN_CONTRACT_ADDRESS=0x5FbDB2315678afecb367f032d93F642f64180aa3
```

After changing `.env`, run:

```bash
php artisan config:clear
```

## What Gets Anchored

- `identity.submitted` calls `submitIdentity`.
- `identity.verified` calls `verifyIdentity`.
- `identity.rejected` calls `rejectIdentity`.
- `identity.revoked` calls `revokeIdentity`.

Each confirmed transaction is saved on the matching ledger entry as `blockchain_tx_hash`.
