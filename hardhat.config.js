import hardhatEthers from "@nomicfoundation/hardhat-ethers";
import "dotenv/config";

const localhostUrl = process.env.BLOCKCHAIN_RPC_URL ?? "http://127.0.0.1:8545";
const deployerPrivateKey = process.env.BLOCKCHAIN_PRIVATE_KEY;

/** @type import("hardhat/config").HardhatUserConfig */
export default {
    plugins: [hardhatEthers],
    solidity: {
        version: "0.8.24",
        settings: {
            optimizer: {
                enabled: true,
                runs: 200,
            },
        },
    },
    networks: {
        localhost: {
            url: localhostUrl,
            accounts: deployerPrivateKey ? [deployerPrivateKey] : undefined,
        },
    },
};
