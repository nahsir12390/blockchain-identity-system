import fs from "node:fs";
import path from "node:path";
import { ethers } from "ethers";
import "dotenv/config";

const [, , action, didHash, identityHash, documentHash, blockHash] = process.argv;

const deploymentPath = path.join(process.cwd(), "storage", "app", "blockchain", "deployment.json");
const abiPath = path.join(process.cwd(), "storage", "app", "blockchain", "DecentralizedIdentityRegistry.abi.json");
const deployment = JSON.parse(fs.readFileSync(deploymentPath, "utf8"));
const abi = JSON.parse(fs.readFileSync(abiPath, "utf8"));

const rpcUrl = process.env.BLOCKCHAIN_RPC_URL ?? deployment.rpc_url;
const privateKey = process.env.BLOCKCHAIN_PRIVATE_KEY;

if (!privateKey) {
    throw new Error("BLOCKCHAIN_PRIVATE_KEY is required to anchor identity proofs.");
}

const provider = new ethers.JsonRpcProvider(rpcUrl);
const signer = new ethers.Wallet(privateKey, provider);
const registry = new ethers.Contract(deployment.address, abi, signer);

const emptyHash = "0x" + "0".repeat(64);
let tx;

switch (action) {
    case "identity.submitted":
    case "identity.resubmitted":
        tx = await registry.submitIdentity(didHash, identityHash, documentHash || emptyHash);
        break;
    case "identity.verified":
        tx = await registry.verifyIdentity(didHash, blockHash);
        break;
    case "identity.rejected":
        tx = await registry.rejectIdentity(didHash);
        break;
    case "identity.revoked":
        tx = await registry.revokeIdentity(didHash);
        break;
    default:
        throw new Error(`Unsupported identity action: ${action}`);
}

const receipt = await tx.wait();

console.log(JSON.stringify({
    transaction_hash: receipt.hash,
    block_number: receipt.blockNumber,
    contract_address: deployment.address,
    network: process.env.BLOCKCHAIN_NETWORK ?? deployment.network,
    status: receipt.status === 1 ? "confirmed" : "failed",
}));
