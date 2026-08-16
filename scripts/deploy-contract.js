import fs from "node:fs";
import path from "node:path";
import { artifacts, network } from "hardhat";

const { ethers } = await network.getOrCreate();
const registry = await ethers.deployContract("DecentralizedIdentityRegistry");
await registry.waitForDeployment();

const address = await registry.getAddress();
const artifact = await artifacts.readArtifact("DecentralizedIdentityRegistry");
const outputDirectory = path.join(process.cwd(), "storage", "app", "blockchain");

fs.mkdirSync(outputDirectory, { recursive: true });
fs.writeFileSync(
    path.join(outputDirectory, "DecentralizedIdentityRegistry.abi.json"),
    JSON.stringify(artifact.abi, null, 2),
);
fs.writeFileSync(
    path.join(outputDirectory, "deployment.json"),
    JSON.stringify(
        {
            contract: "DecentralizedIdentityRegistry",
            address,
            network: process.env.BLOCKCHAIN_NETWORK ?? "hardhat-localhost",
            rpc_url: process.env.BLOCKCHAIN_RPC_URL ?? "http://127.0.0.1:8545",
            deployed_at: new Date().toISOString(),
        },
        null,
        2,
    ),
);

console.log(JSON.stringify({ address }));
