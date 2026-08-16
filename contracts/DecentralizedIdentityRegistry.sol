// SPDX-License-Identifier: MIT
pragma solidity ^0.8.24;

contract DecentralizedIdentityRegistry {
    enum IdentityStatus {
        Pending,
        Verified,
        Rejected,
        Revoked
    }

    struct IdentityProof {
        bytes32 identityHash;
        bytes32 documentHash;
        bytes32 blockHash;
        IdentityStatus status;
        address issuer;
        uint256 updatedAt;
    }

    mapping(bytes32 => IdentityProof) private proofs;

    event IdentitySubmitted(bytes32 indexed didHash, bytes32 identityHash, address indexed issuer);
    event IdentityVerified(bytes32 indexed didHash, bytes32 blockHash, address indexed verifier);
    event IdentityRejected(bytes32 indexed didHash, address indexed verifier);
    event IdentityRevoked(bytes32 indexed didHash, address indexed verifier);

    function submitIdentity(bytes32 didHash, bytes32 identityHash, bytes32 documentHash) external {
        proofs[didHash] = IdentityProof({
            identityHash: identityHash,
            documentHash: documentHash,
            blockHash: bytes32(0),
            status: IdentityStatus.Pending,
            issuer: msg.sender,
            updatedAt: block.timestamp
        });

        emit IdentitySubmitted(didHash, identityHash, msg.sender);
    }

    function verifyIdentity(bytes32 didHash, bytes32 blockHash) external {
        IdentityProof storage proof = proofs[didHash];
        require(proof.identityHash != bytes32(0), "Identity proof not found");

        proof.blockHash = blockHash;
        proof.status = IdentityStatus.Verified;
        proof.updatedAt = block.timestamp;

        emit IdentityVerified(didHash, blockHash, msg.sender);
    }

    function rejectIdentity(bytes32 didHash) external {
        IdentityProof storage proof = proofs[didHash];
        require(proof.identityHash != bytes32(0), "Identity proof not found");

        proof.status = IdentityStatus.Rejected;
        proof.updatedAt = block.timestamp;

        emit IdentityRejected(didHash, msg.sender);
    }

    function revokeIdentity(bytes32 didHash) external {
        IdentityProof storage proof = proofs[didHash];
        require(proof.identityHash != bytes32(0), "Identity proof not found");

        proof.status = IdentityStatus.Revoked;
        proof.updatedAt = block.timestamp;

        emit IdentityRevoked(didHash, msg.sender);
    }

    function getIdentity(bytes32 didHash) external view returns (IdentityProof memory) {
        return proofs[didHash];
    }
}
