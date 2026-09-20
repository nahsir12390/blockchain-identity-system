# Blockchain-Based Identity Management System - Complete Project Documentation

**Version:** 1.0  
**Date:** August 2026  
**Status:** School Project - Deployed with SQLite  
**Technology Stack:** Laravel 13.17 + Livewire 4.1 + Hardhat + Solidity 0.8.24

---

## Table of Contents
1. [Project Overview](#project-overview)
2. [Architecture & Technology Stack](#architecture--technology-stack)
3. [Database Design](#database-design)
4. [Smart Contract System](#smart-contract-system)
5. [Core Services & Business Logic](#core-services--business-logic)
6. [API Routes & Controllers](#api-routes--controllers)
7. [User Workflow](#user-workflow)
8. [Setup & Deployment Instructions](#setup--deployment-instructions)
9. [SQLite Deployment Notes](#sqlite-deployment-notes)
10. [Security Considerations](#security-considerations)

---

## Project Overview

### What is This Project?

This is a **Blockchain-Based Decentralized Identity Management System** that combines traditional web application security with blockchain technology to create a tamper-proof, auditable identity verification system.

### Purpose

- **Digital Identity Management:** Users can create and submit verified digital identities
- **Admin Verification:** Administrators can review, verify, reject, or revoke identities
- **Blockchain Anchoring:** Critical identity state transitions are recorded on a blockchain for immutability
- **Audit Trail:** Complete ledger entries track every action taken on identities
- **Public Verification:** Third parties can verify identities using DID (Decentralized Identifier) or transaction hashes

### Key Features

✅ User registration and authentication (Laravel Fortify)  
✅ Digital identity creation with document upload  
✅ Multi-stage identity verification workflow  
✅ Blockchain smart contract integration (Hardhat + Solidity)  
✅ Immutable ledger system for audit trails  
✅ Admin dashboard for identity review  
✅ Public verification interface  
✅ Ledger integrity verification  
✅ SQLite database for cost-free deployment  
✅ Livewire 4.1 for reactive UI components  

---

## Architecture & Technology Stack

### Frontend
- **Livewire 4.1:** Reactive PHP components (no JavaScript required for interactivity)
- **Flux UI Components:** High-quality UI component library
- **Blaze:** Livewire component library
- **Tailwind CSS 4.0:** Utility-first CSS framework
- **Vite:** Modern build tool for assets

### Backend
- **Laravel 13.17:** PHP web framework
- **PHP 8.3+:** Server-side runtime
- **Eloquent ORM:** Database abstraction layer
- **Laravel Fortify:** Authentication and account management
- **Pest PHP:** Testing framework

### Blockchain & Cryptography
- **Hardhat 3.12:** Ethereum development environment
- **Solidity 0.8.24:** Smart contract language
- **Ethers.js 6.17:** Blockchain interaction library
- **OpenZeppelin:** Smart contract standards

### Database
- **SQLite 3:** Lightweight, file-based database (cost-free deployment)
- **Database Path:** `database/database.sqlite`
- **Foreign Key Constraints:** Enabled by default

### Cryptographic Hashing
- **SHA-256:** Used for all hashing operations (identity numbers, documents, ledger blocks)
- **HMAC-SHA256:** Used for signed hashing with application key

---

## Database Design

### Core Tables

#### 1. `users`
Stores user accounts with authentication credentials.

```sql
- id (bigint, PK)
- name (string)
- email (string, unique)
- email_verified_at (timestamp)
- password (string, hashed)
- two_factor_secret (text)
- two_factor_recovery_codes (text)
- is_admin (boolean) -- Added for admin role
- remember_token (string)
- created_at, updated_at (timestamps)
```

**Relationships:**
- Has one `DigitalIdentity`
- Has many `LedgerEntry` (as actor)
- Can verify many `DigitalIdentity` (as verifier)

---

#### 2. `digital_identities`
Stores digital identity records submitted by users.

```sql
- id (bigint, PK)
- user_id (bigint, FK → users, unique)
- did (string, unique) -- Format: "did:trustwall:ULID"
- legal_name (string)
- wallet_address (string, nullable) -- Ethereum wallet for blockchain
- public_key (string, nullable) -- User's public key if provided
- identity_type (string) -- e.g., "passport", "driver_license", "national_id"
- identity_number_hash (string) -- SHA256 hash of identity number
- document_hash (string, nullable) -- SHA256 hash of uploaded document
- document_path (string, nullable) -- Storage path to uploaded file
- document_original_name (string, nullable)
- document_mime_type (string, nullable)
- document_size (int, nullable)
- status (string) -- Enum: draft → pending → verified/rejected → revoked
- blockchain_network (string) -- "hardhat-localhost" or "Local Proof Chain"
- contract_address (string, nullable)
- block_hash (string, nullable) -- Local block hash
- transaction_hash (string, nullable) -- Blockchain transaction hash
- rejection_reason (text, nullable)
- submitted_at (timestamp)
- verified_at (timestamp, nullable)
- verified_by (bigint, FK → users, nullable) -- Admin who verified
- created_at, updated_at (timestamps)
```

**Indexes:**
- `user_id` (unique)
- `did` (unique)
- `status` (for filtering)

**Status Workflow:**
```
draft → pending → (verified OR rejected)
rejected → pending (resubmit allowed)
verified → revoked
revoked → (end state)
```

---

#### 3. `ledger_entries`
Blockchain-inspired ledger recording all identity state transitions.

```sql
- id (bigint, PK)
- digital_identity_id (bigint, FK → digital_identities)
- actor_id (bigint, FK → users) -- User who performed the action
- action (string) -- "identity.submitted", "identity.verified", etc.
- canonical_payload (json) -- Normalized action data
- hash_algorithm (string) -- "sha256"
- payload_hash (string) -- Hash of canonical payload
- previous_hash (string, nullable) -- Hash of previous entry (chain)
- entry_hash (string, unique) -- Hash of entire entry (like block hash)
- blockchain_network (string) -- Which blockchain network was used
- blockchain_tx_hash (string, nullable) -- Actual blockchain transaction hash
- contract_address (string, nullable)
- onchain_status (string, nullable) -- "confirmed", "failed", etc.
- onchain_receipt (json, nullable) -- Full blockchain receipt
- block_number (bigint) -- Sequence number in chain
- metadata (json, nullable) -- Additional context
- recorded_at (timestamp)
- created_at, updated_at (timestamps)
```

**Canonical Payload Structure:**
```json
{
  "identity_id": 1,
  "did": "did:trustwall:01hjpvvzyw3n8hh9k8m1q2r3",
  "status": "verified",
  "action": "identity.verified",
  "actor_id": 2,
  "metadata": { ... },
  "previous_hash": "abc123...",
  "block_number": 2,
  "recorded_at": "2026-08-14T10:30:00.000000Z"
}
```

**Hash Chain Logic:**
- Each entry references the previous entry's hash
- Forms an immutable chain (tamper detection)
- Like blockchain blocks but stored in database

---

#### 4. Supporting Tables
- `cache` - Laravel cache system
- `jobs` - Queue jobs
- `password_reset_tokens` - Password recovery
- `sessions` - User session data

---

### Schema Migrations

All migrations are timestamped and run in sequence:

1. **0001_01_01_000000** - `create_users_table`
2. **0001_01_01_000001** - `create_cache_table`
3. **0001_01_01_000002** - `create_jobs_table`
4. **2025_08_14_170933** - `add_two_factor_columns_to_users_table`
5. **2026_08_10_031240** - `add_is_admin_to_users_table`
6. **2026_08_10_031242** - `create_digital_identities_table`
7. **2026_08_10_031243** - `create_ledger_entries_table`
8. **2026_08_10_050000** - `add_blockchain_anchor_fields_to_ledger_entries`
9. **2026_08_10_050001** - `add_contract_address_to_digital_identities`
10. **2026_08_10_051000** - `add_canonical_hash_fields_to_ledger_entries`
11. **2026_08_11_000000** - `add_document_upload_fields_to_digital_identities`

---

## Smart Contract System

### Solidity Smart Contract: `DecentralizedIdentityRegistry.sol`

**Location:** `contracts/DecentralizedIdentityRegistry.sol`

#### Contract Purpose
Records cryptographic proofs of digital identity state on the blockchain. **Important:** Only hashes are written on-chain; raw data remains in the database.

#### Data Structures

**IdentityStatus Enum:**
```solidity
enum IdentityStatus {
    Pending,      // 0
    Verified,     // 1
    Rejected,     // 2
    Revoked       // 3
}
```

**IdentityProof Struct:**
```solidity
struct IdentityProof {
    bytes32 identityHash;      // SHA256 of identity number
    bytes32 documentHash;      // SHA256 of document
    bytes32 blockHash;         // Local block hash from Laravel
    IdentityStatus status;     // Current status
    address issuer;            // Address that submitted
    uint256 updatedAt;         // Block timestamp
}
```

#### State Variables
```solidity
mapping(bytes32 => IdentityProof) private proofs;
// Key: DID hash (sha256(did))
// Value: IdentityProof structure
```

#### Key Functions

**1. `submitIdentity(bytes32 didHash, bytes32 identityHash, bytes32 documentHash)`**

Submits a new identity proof to the blockchain.

```solidity
- Creates new IdentityProof with Pending status
- Stores issuer address (blockchain signer)
- Emits IdentitySubmitted event
- Updates timestamp
```

**Usage Flow:**
```
User submits identity in Laravel app
  ↓
Laravel creates LedgerEntry
  ↓
Node.js script calls submitIdentity()
  ↓
Proof recorded on blockchain
  ↓
Transaction hash stored in LedgerEntry
```

---

**2. `verifyIdentity(bytes32 didHash, bytes32 blockHash)`**

Admin verifies an identity, changing status to Verified.

```solidity
- Retrieves IdentityProof by DID hash
- Validates proof exists
- Sets status to Verified
- Records block hash for verification
- Emits IdentityVerified event
```

---

**3. `rejectIdentity(bytes32 didHash)`**

Rejects an identity submission.

```solidity
- Sets status to Rejected
- Does not require additional data
- Emits IdentityRejected event
```

---

**4. `revokeIdentity(bytes32 didHash)`**

Revokes a previously verified identity.

```solidity
- Sets status to Revoked
- Marks as final state
- Emits IdentityRevoked event
```

#### Events

```solidity
event IdentitySubmitted(bytes32 indexed didHash, bytes32 identityHash, address indexed issuer);
event IdentityVerified(bytes32 indexed didHash, bytes32 blockHash, address indexed verifier);
event IdentityRejected(bytes32 indexed didHash, address indexed verifier);
event IdentityRevoked(bytes32 indexed didHash, address indexed verifier);
```

---

### Blockchain Integration Scripts

#### Deploy Script: `scripts/deploy-contract.js`

**Purpose:** Deploy the smart contract to the blockchain

**Process:**
1. Compiles Solidity contract
2. Deploys using configured signer (private key)
3. Saves contract ABI to `storage/app/blockchain/DecentralizedIdentityRegistry.abi.json`
4. Saves deployment info to `storage/app/blockchain/deployment.json`

**Output:**
```json
{
  "contract": "DecentralizedIdentityRegistry",
  "address": "0x5FbDB2315678afecb367f032d93F642f64180aa3",
  "network": "hardhat-localhost",
  "rpc_url": "http://127.0.0.1:8545",
  "deployed_at": "2026-08-14T10:30:00.000Z"
}
```

**Command:**
```bash
npm run blockchain:deploy
```

---

#### Anchor Script: `scripts/anchor-identity.js`

**Purpose:** Execute smart contract functions when identities change state

**Process:**
1. Receives action type and identity data
2. Loads deployment configuration and ABI
3. Connects to blockchain RPC via ethers.js
4. Calls appropriate contract function
5. Waits for transaction confirmation
6. Returns receipt to Laravel

**Supported Actions:**
```javascript
- "identity.submitted"    → submitIdentity()
- "identity.resubmitted"  → submitIdentity()
- "identity.verified"     → verifyIdentity()
- "identity.rejected"     → rejectIdentity()
- "identity.revoked"      → revokeIdentity()
```

**Example Execution:**
```bash
node scripts/anchor-identity.js \
  "identity.verified" \
  "0x<didHash>" \
  "0x<identityHash>" \
  "0x<documentHash>" \
  "0x<blockHash>"
```

**Returns:**
```json
{
  "transaction_hash": "0x123abc...",
  "block_number": 12345,
  "contract_address": "0x5FbDB2315678afecb367f032d93F642f64180aa3",
  "network": "hardhat-localhost",
  "status": "confirmed"
}
```

---

### Blockchain Configuration

**File:** `.env`

```env
BLOCKCHAIN_ENABLED=true                                    # Enable/disable blockchain
BLOCKCHAIN_NETWORK=hardhat-localhost                      # Network name
BLOCKCHAIN_RPC_URL=http://127.0.0.1:8545                  # Node RPC endpoint
BLOCKCHAIN_PRIVATE_KEY=ac0974bec39a17e36ba4a6b4d23...    # Signer private key
BLOCKCHAIN_CONTRACT_ADDRESS=0x5FbDB2315678afecb3...       # Deployed contract
```

**File:** `config/blockchain.php`

```php
return [
    'enabled' => env('BLOCKCHAIN_ENABLED', false),
    'network' => env('BLOCKCHAIN_NETWORK', 'hardhat-localhost'),
    'rpc_url' => env('BLOCKCHAIN_RPC_URL', 'http://127.0.0.1:8545'),
    'private_key' => env('BLOCKCHAIN_PRIVATE_KEY'),
    'contract_address' => env('BLOCKCHAIN_CONTRACT_ADDRESS'),
    'deployment_path' => storage_path('app/blockchain/deployment.json'),
    'abi_path' => storage_path('app/blockchain/DecentralizedIdentityRegistry.abi.json'),
];
```

---

## Core Services & Business Logic

### Service Architecture

All business logic is encapsulated in service classes following SOLID principles.

---

### 1. `IdentityHasher` Service

**Location:** `app/Services/IdentityHasher.php`

**Purpose:** Consistent cryptographic hashing across the system

**Key Methods:**

```php
public function hash(string $value): string
```

- Normalizes input (lowercase, trim)
- Uses HMAC-SHA256 with app key
- Returns 64-character hexadecimal hash
- Deterministic: same input = same output

**Usage Examples:**
```php
$hasher->hash('1234567890');     // Hash identity number
$hasher->hash('john@example.com'); // Hash for lookups
```

**Security Notes:**
- App key used as HMAC secret for additional security layer
- Prevents rainbow table attacks
- Different across app deployments

---

### 2. `IdentityLedger` Service

**Location:** `app/Services/IdentityLedger.php`

**Purpose:** Manages immutable ledger entries and blockchain-like chain verification

#### Key Methods

**`record(DigitalIdentity $identity, string $action, ?User $actor, array $metadata)`**

Creates a new ledger entry for identity state change.

**Process:**
1. Retrieves last ledger entry for the identity
2. Increments block number
3. Normalizes metadata (sorted, consistent JSON)
4. Creates canonical payload:
   ```php
   [
       'identity_id' => 1,
       'did' => 'did:trustwall:...',
       'status' => 'verified',
       'action' => 'identity.verified',
       'actor_id' => 2,
       'metadata' => [...],
       'previous_hash' => 'abc123...',
       'block_number' => 2,
       'recorded_at' => '2026-08-14T10:30:00Z'
   ]
   ```
5. Hashes canonical payload
6. Creates entry hash (hash of payload + previous hash + block number)
7. Stores as new LedgerEntry

**Returns:** `LedgerEntry` model instance

---

**`inspect(DigitalIdentity $identity): array`**

Validates entire ledger chain for tampering.

**Validation Checks:**
- Ledger not empty
- Block numbers sequential (1, 2, 3, ...)
- Previous hashes properly linked
- Entry hashes match recalculated hashes
- Canonical payloads match hashes

**Returns:**
```php
[
    'valid' => true,              // Chain integrity OK
    'status' => 'valid',          // 'valid', 'empty', 'invalid'
    'issues' => [],               // Error details if invalid
    'blocks' => 3,                // Number of entries
    'latest_hash' => 'abc123...'  // Most recent hash
]
```

---

### 3. `IdentityWorkflow` Service

**Location:** `app/Services/IdentityWorkflow.php`

**Purpose:** State machine validation for identity status transitions

**Status Transitions:**
```
draft → pending
pending → verified OR rejected
rejected → pending (resubmit allowed)
verified → revoked
revoked → (no transitions, final state)
```

#### Key Methods

**`can(DigitalIdentity $identity, string $nextStatus): bool`**

Checks if transition is valid.

```php
if ($workflow->can($identity, 'verified')) {
    // Can transition
}
```

**`assertCan(DigitalIdentity $identity, string $nextStatus): void`**

Throws 422 exception if transition invalid.

```php
$workflow->assertCan($identity, 'verified');
// Throws if not allowed
```

---

### 4. `BlockchainIdentityRegistry` Service

**Location:** `app/Services/BlockchainIdentityRegistry.php`

**Purpose:** Blockchain integration and smart contract interaction

#### Key Methods

**`anchor(DigitalIdentity $identity, string $action, string $localBlockHash): ?array`**

Anchors identity state to blockchain via smart contract.

**Process:**
1. Checks if blockchain is enabled and configured
2. Validates deployment files exist
3. Checks private key is configured
4. Spawns Node.js subprocess running `anchor-identity.js`
5. Passes arguments:
   - Action (identity.submitted, identity.verified, etc.)
   - DID hash (SHA256)
   - Identity number hash
   - Document hash (or empty if none)
   - Local block hash
6. Subprocess executes smart contract function
7. Waits for transaction confirmation
8. Parses JSON response

**Returns:**
```php
[
    'transaction_hash' => '0x123abc...',
    'block_number' => 12345,
    'contract_address' => '0x5FbDB2315...',
    'network' => 'hardhat-localhost',
    'status' => 'confirmed'
]
// Or null if blockchain disabled or error
```

**Usage in Controllers:**
```php
$anchor = $blockchain->anchor($identity, 'identity.verified', $localBlockHash);
if ($anchor) {
    $entry->update([
        'blockchain_tx_hash' => $anchor['transaction_hash'],
        'onchain_status' => $anchor['status'],
        'onchain_receipt' => $anchor,
    ]);
}
```

---

**`isReady(): bool`**

Checks if blockchain system is fully configured.

```php
if ($blockchain->isReady()) {
    // Safe to call anchor()
}
```

**Checks:**
- Blockchain enabled in config
- deployment.json exists
- DecentralizedIdentityRegistry.abi.json exists
- Private key configured

---

## API Routes & Controllers

### Route Structure

**File:** `routes/web.php`

#### Public Routes
```
GET  /                          → welcome (landing page)
GET  /verify?q=<did|hash>       → PublicVerificationController@index
```

#### Authenticated Routes
```
GET  /dashboard                 → dashboard (home for logged-in users)

Identity Management:
GET    /identity                → IdentityController@index (view my identity)
GET    /identity/create         → IdentityController@create (form to submit)
POST   /identity                → IdentityController@store (submit identity)
GET    /identity/{id}/document  → IdentityController@document (download doc)
GET    /identity/{id}/certificate → IdentityController@certificate (proof)
PATCH  /identity/{id}/resubmit  → IdentityController@resubmit (after rejection)

Admin Routes (requires admin role):
GET    /admin/identities                    → IdentityReviewController@index (list)
GET    /admin/identities/{id}               → IdentityReviewController@show (detail)
PATCH  /admin/identities/{id}/verify        → IdentityReviewController@verify
PATCH  /admin/identities/{id}/reject        → IdentityReviewController@reject
PATCH  /admin/identities/{id}/revoke        → IdentityReviewController@revoke
GET    /admin/audit-log                     → AuditLogController@index
GET    /admin/ledger-integrity              → LedgerIntegrityController@index
GET    /admin/smart-contract                → SmartContractController@index
GET    /admin/users                         → UserManagementController@index
PATCH  /admin/users/{id}/promote            → UserManagementController@promote
PATCH  /admin/users/{id}/demote             → UserManagementController@demote
```

---

### Controller Details

#### 1. `IdentityController`

**Location:** `app/Http/Controllers/IdentityController.php`

Dependencies (injected):
- `IdentityLedger $ledger`
- `BlockchainIdentityRegistry $blockchain`
- `IdentityHasher $hasher`
- `IdentityWorkflow $workflow`

---

**`index()`** - Display user's identity

```php
public function index()
{
    $identity = Auth::user()->digitalIdentity?->load('ledgerEntries.actor');
    return view('identities.index', ['identity' => $identity]);
}
```

**Response:** View showing:
- Current identity status
- Personal data
- Document info
- Ledger history with actor names
- Verification status
- Rejection reasons (if any)

---

**`create()`** - Show identity submission form

```php
public function create()
{
    abort_if(Auth::user()->digitalIdentity()->exists(), 403);
    return view('identities.create');
}
```

**Access:** Only allowed if user has no existing identity
**Fields in Form:**
- Legal Name (required)
- Identity Type (dropdown: passport, driver_license, etc.)
- Identity Number (required, hashed before storage)
- Wallet Address (optional)
- Public Key (optional)
- Document Reference (optional text)
- Document File (optional: PDF, JPG, PNG, WebP, max 4MB)

---

**`store(Request $request)`** - Submit new identity

**Validation:**
```php
[
    'legal_name' => ['required', 'string', 'max:255'],
    'identity_type' => ['required', 'string', 'max:80'],
    'identity_number' => ['required', 'string', 'max:120'],
    'wallet_address' => ['nullable', 'string', 'max:255'],
    'public_key' => ['nullable', 'string', 'max:500'],
    'document_reference' => ['nullable', 'string', 'max:255'],
    'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
]
```

**Process:**
1. Validate input
2. Handle document upload:
   - SHA256 hash of file content
   - Store in `storage/app/identity-documents/{user_id}`
   - Generate unique filename (ULID + extension)
3. Hash identity number using IdentityHasher
4. Create DID (Decentralized Identifier):
   - Format: `did:trustwall:<ULID>`
   - ULID = sortable, unique identifier
5. Create DigitalIdentity record with status=pending
6. Record ledger entry: "identity.submitted"
7. If blockchain enabled: anchor to blockchain
8. Redirect with success message

**Storage Structure:**
```
storage/app/identity-documents/
└── {user_id}/
    ├── 01hjpvvzyw3n8hh9k8m1q2r3.pdf
    ├── 01hjpvvzyw3n8hh9k8m1q2r4.jpg
    └── 01hjpvvzyw3n8hh9k8m1q2r5.png
```

---

**`document(DigitalIdentity $identity)`** - Download submitted document

```php
public function document(DigitalIdentity $identity)
{
    // Authorization: user owns identity
    // Returns file download
}
```

---

**`resubmit(Request $request, DigitalIdentity $identity)`** - Resubmit after rejection

Only allowed if status = 'rejected'

Process:
1. Updates identity status back to 'pending'
2. Clears rejection reason
3. Records new ledger entry: "identity.resubmitted"
4. Anchors to blockchain if enabled

---

#### 2. `IdentityReviewController` (Admin)

**Location:** `app/Http/Controllers/Admin/IdentityReviewController.php`

Dependencies:
- `IdentityLedger $ledger`
- `BlockchainIdentityRegistry $blockchain`
- `IdentityWorkflow $workflow`

---

**`index()`** - List all identities

```php
public function index()
{
    return view('admin.identities.index', [
        'identities' => DigitalIdentity::query()
            ->with('user', 'verifier')
            ->latest()
            ->paginate(15),
    ]);
}
```

**Displays:**
- Paginated list (15 per page)
- User email/name
- Status badge (pending, verified, rejected, revoked)
- Submission date
- Actions (view, verify, reject)

---

**`show(DigitalIdentity $identity)`** - View identity details

**Shows:**
- All identity data
- Document preview/download
- Ledger entries with actors
- Blockchain transaction status
- Verification history
- Rejection reasons

---

**`verify(DigitalIdentity $identity)`** - Approve identity

**Process:**
1. Check workflow allows pending → verified
2. Update identity:
   - status = 'verified'
   - verified_at = now()
   - verified_by = current_user
   - rejection_reason = null
3. Record ledger entry: "identity.verified"
4. Anchor to blockchain:
   - Calls smart contract `verifyIdentity()`
   - Passes block hash
5. Update ledger with blockchain receipt
6. Update identity with contract address

**Blockchain Action:**
```javascript
registry.verifyIdentity(didHash, blockHash)
// Emits: IdentityVerified event
// Status on-chain: Verified
```

---

**`reject(Request $request, DigitalIdentity $identity)`** - Reject identity

**Validation:**
```php
['rejection_reason' => ['required', 'string', 'max:1000']]
```

**Process:**
1. Check workflow allows pending → rejected
2. Update identity:
   - status = 'rejected'
   - rejection_reason = entered text
   - verified_at = null
   - verified_by = null
3. Record ledger entry with reason hash
4. Anchor to blockchain:
   - Calls `rejectIdentity()`
5. User can resubmit after rejection

---

**`revoke(DigitalIdentity $identity)`** - Revoke verified identity

Moves verified → revoked (end state)

**Uses:** For identity compromise or cancellation

---

#### 3. `PublicVerificationController`

**Location:** `app/Http/Controllers/PublicVerificationController.php`

**Purpose:** Allow public verification without authentication

**`index(Request $request)`** - Public verification interface

**Search Query:** URL parameter `?q=<search>`

**Searchable by:**
- DID (e.g., `did:trustwall:01hjpvvzyw3n8hh9k8m1q2r3`)
- Transaction hash (blockchain tx)
- Block hash (local block hash)

**Returns:**
- Identity found or "not found"
- If found:
  - All public data
  - Verification status
  - Ledger integrity result
  - Blockchain anchors
- Ledger chain integrity validation

**Privacy Notes:**
- Only shows status, not identity number hash or document
- Verifies ledger hasn't been tampered
- No authentication required

---

#### 4. Admin Controllers

**LedgerIntegrityController**
- Lists all identities with ledger inspection results
- Shows valid vs invalid counts
- Allows integrity verification

**SmartContractController**
- Displays contract deployment info
- Shows ABI and deployment paths
- Lists recent identities with blockchain status

**UserManagementController**
- Lists all users
- Promote/demote admin status

---

## User Workflow

### Complete User Journey

#### Phase 1: Registration

1. **Access Registration:** User visits `/register`
2. **Fill Form:**
   - Email
   - Password (min 12 characters, confirmed)
   - Accept terms
3. **Account Created:**
   - Password hashed with bcrypt
   - Email verification email sent
   - User not yet authenticated
4. **Verify Email:**
   - Click link in email
   - Email verification token validated
5. **Redirect to Dashboard**

---

#### Phase 2: Digital Identity Submission

1. **Navigate:** Authenticated user clicks "Create Identity"
2. **Fill Form:**
   - Legal Name (required)
   - Identity Type (required)
   - Identity Number (required)
   - Optional: Wallet address, public key
   - Optional: Upload document file
3. **System Processing:**
   - Hashes identity number (never stored raw)
   - SHA256 hashes document if uploaded
   - Generates DID: `did:trustwall:ULID`
   - Creates DigitalIdentity record (status = pending)
   - Records LedgerEntry: "identity.submitted"
   - Anchors to blockchain (if enabled)
4. **Confirmation:**
   - User sees success message
   - Redirected to identity view
   - Status shows "pending"

---

#### Phase 3: Admin Review (Verification/Rejection)

**Admin Flow:**
1. **Navigate:** Admin visits `/admin/identities`
2. **View Pending:** Filter for "pending" status
3. **Review Identity:** Click to view details
   - See all submitted data
   - Download document
   - Check blockchain anchor
   - Review ledger history
4. **Decision:**

**Option A: Verify**
- Click "Approve"
- System:
  - Changes status to "verified"
  - Sets verified_by and verified_at
  - Records ledger: "identity.verified"
  - Calls blockchain: `verifyIdentity()`
  - Certificate generated
- User sees "Verified" badge

**Option B: Reject**
- Click "Reject"
- Enter reason (max 1000 chars)
- System:
  - Changes status to "rejected"
  - Stores rejection reason
  - Records ledger: "identity.rejected"
  - Calls blockchain: `rejectIdentity()`
- User receives notification

---

#### Phase 4: User Actions After Status Changes

**If Verified:**
- View identity with "Verified" badge
- Download certificate (proof of verification)
- Cannot modify identity
- Can view ledger history

**If Rejected:**
- View rejection reason
- Click "Resubmit"
- System resets to "pending"
- Resubmit with corrections
- Returns to admin review

---

#### Phase 5: Public Verification (Third Party)

1. **Access:** Anyone visits `/verify`
2. **Search:** Enter DID or transaction hash
3. **System Lookup:**
   - Finds identity record
   - Validates ledger chain
   - Checks blockchain anchor
4. **Display:**
   - Verification status
   - Ledger integrity (valid/invalid)
   - Blockchain confirmation status
5. **Verification:** Third party can confirm identity without database access

---

### Status Diagram

```
┌─────────────────────────────────────────────────────────┐
│                    Draft (initial)                      │
│              (no identity yet created)                  │
└──────────────────────────┬──────────────────────────────┘
                           │ User submits form
                           ▼
┌─────────────────────────────────────────────────────────┐
│                    Pending                              │
│         (awaiting admin verification)                   │
│         User can resubmit if rejected                   │
└──────────┬──────────────────────────────────┬───────────┘
           │                                  │
    (Admin Verify)                      (Admin Reject)
           │                                  │
           ▼                                  ▼
┌─────────────────────┐          ┌─────────────────────┐
│     Verified        │          │     Rejected        │
│ • Certificate       │          │ • Reason shown      │
│ • Ledger sealed     │          │ • Can resubmit      │
│ • Public proof      │          └─────────────────────┘
│ (Final: can revoke) │
└──────────┬──────────┘
           │ (Admin Revoke)
           ▼
┌─────────────────────┐
│     Revoked         │
│ (Final state)       │
└─────────────────────┘
```

---

## Setup & Deployment Instructions

### Prerequisites

- **PHP 8.3+** with extensions: pdo, pdo_sqlite, bcmath, json, openssl
- **Node.js 18+** and npm
- **Composer 2.x**
- **SQLite 3** (usually pre-installed)

---

### 1. Initial Setup

#### Clone Repository
```bash
cd /path/to/project
git clone <repo-url> .
```

#### Install PHP Dependencies
```bash
composer install
```

#### Install Node Dependencies
```bash
npm install
```

---

### 2. Environment Configuration

#### Copy Environment File
```bash
cp .env.example .env
```

#### Generate Application Key
```bash
php artisan key:generate
```

#### Edit `.env` - Critical Settings

**Database (SQLite - already default):**
```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

**App Settings:**
```env
APP_NAME="Blockchain Identity System"
APP_DEBUG=true (development only)
APP_URL=http://localhost:8000
APP_LOCALE=en
```

**Authentication:**
```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
```

**Blockchain (Optional):**
```env
BLOCKCHAIN_ENABLED=true
BLOCKCHAIN_NETWORK=hardhat-localhost
BLOCKCHAIN_RPC_URL=http://127.0.0.1:8545
BLOCKCHAIN_PRIVATE_KEY=ac0974bec39a17e36ba4a6b4d238ff944bacb478cbed5efcae784d7bf4f2ff80
```

---

### 3. Database Setup

#### Create SQLite Database and Run Migrations
```bash
php artisan migrate
```

This will:
- Create `database/database.sqlite`
- Create all tables
- Set up foreign keys
- Create indexes

#### Seed Demo Data (Optional)
```bash
php artisan tinker
# In tinker shell:
> User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'is_admin' => true, 'password' => bcrypt('password')])
```

---

### 4. Blockchain Setup (Optional)

**Note:** Blockchain is optional. System works fine with SQLite-only ledger.

#### Start Hardhat Local Blockchain (Terminal 1)
```bash
npm run blockchain:node
```

This starts a local Ethereum node on `http://127.0.0.1:8545`

#### Deploy Smart Contract (Terminal 2)
```bash
npm run blockchain:compile
npm run blockchain:deploy
```

This:
- Compiles Solidity contract
- Deploys to local chain
- Saves deployment info to `storage/app/blockchain/`

#### Verify Deployment
```bash
cat storage/app/blockchain/deployment.json
# Should show contract address
```

---

### 5. Build Frontend Assets

```bash
npm run build     # Production
npm run dev       # Development (watch mode)
```

---

### 6. Start Development Server

```bash
php artisan serve
```

Starts server at `http://localhost:8000`

---

### 7. Access Application

1. **Homepage:** http://localhost:8000
2. **Register:** http://localhost:8000/register
3. **Login:** http://localhost:8000/login
4. **Admin Dashboard:** http://localhost:8000/admin/identities (after promoting to admin)

---

### Automated Setup Command

```bash
composer run setup
```

This runs:
1. `composer install`
2. Copy `.env.example` to `.env`
3. Generate app key
4. Run migrations
5. `npm install`
6. Build assets

---

## SQLite Deployment Notes

### Why SQLite?

✅ **Cost-Free:** No database service fees (unlike AWS RDS, CloudSQL)  
✅ **Portable:** Single file, easy to backup and transfer  
✅ **Simple:** No server setup required  
✅ **Sufficient:** Perfect for school projects and small deployments  
❌ **Limitations:** Not ideal for high-concurrency production  

---

### SQLite Configuration

**File:** `config/database.php`

```php
'sqlite' => [
    'driver' => 'sqlite',
    'url' => env('DATABASE_URL'),
    'database' => env('DB_DATABASE', database_path('database.sqlite')),
    'prefix' => '',
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
    'busy_timeout' => null,
    'journal_mode' => null,
    'synchronous' => null,
    'transaction_mode' => 'DEFERRED',
],
```

**Key Settings:**
- `foreign_key_constraints` = true (enforces referential integrity)
- `transaction_mode` = DEFERRED (allows concurrent writes)

---

### Backup SQLite Database

```bash
# Simple file copy (database must not be in use)
cp database/database.sqlite database/database.sqlite.backup

# Or using SQLite command
sqlite3 database/database.sqlite ".backup database.backup"
```

---

### Restore from Backup

```bash
# Restore file copy
cp database/database.sqlite.backup database/database.sqlite

# Or from SQLite backup
sqlite3 database/database.sqlite "RESTORE FROM database.backup"
```

---

### Performance Optimization for SQLite

**File:** `.env`

```env
# SQLite performance settings
SQLITE_BUSY_TIMEOUT=5000
```

**File:** `config/database.php`

```php
'sqlite' => [
    // ... other config ...
    'busy_timeout' => 5000,  // 5 second timeout on locked database
    'synchronous' => 'NORMAL',  // Balance speed and safety
]
```

---

### Deployment Checklist

- [ ] Clone project
- [ ] Run `composer install`
- [ ] Copy `.env.example` to `.env`
- [ ] Set `APP_KEY` with `php artisan key:generate`
- [ ] Ensure `DB_CONNECTION=sqlite`
- [ ] Run `php artisan migrate`
- [ ] Run `npm install && npm run build`
- [ ] Set file permissions: `chmod -R 775 storage bootstrap/cache`
- [ ] Test with `php artisan serve`
- [ ] Back up `database/database.sqlite` regularly

---

## Security Considerations

### 1. Authentication & Authorization

✅ **Laravel Fortify** - Built-in authentication system  
✅ **Password Hashing** - Bcrypt with configurable rounds (BCRYPT_ROUNDS=12)  
✅ **Two-Factor Auth** - Supported via Fortify  
✅ **Email Verification** - Required for account activation  
✅ **Admin Middleware** - Restricts admin routes to admins only  

**Authorization Logic:**
```php
Route::middleware(['auth', 'verified', 'admin'])->group(function () {
    // Admin-only routes
});
```

---

### 2. Data Protection

✅ **Identity Number:** SHA256 hash only (never stored raw)  
✅ **Documents:** SHA256 hashed + stored in secure directory  
✅ **Passwords:** Bcrypt hashed (one-way)  
✅ **Session Data:** Encrypted (SESSION_ENCRYPT if enabled)  

---

### 3. Blockchain Security

✅ **Private Key Management:**
- Stored in `.env` (never in Git)
- Access restricted to Node.js scripts only
- Different per deployment

✅ **Transaction Verification:**
- Blockchain transaction hash stored
- Can verify on actual blockchain
- Smart contract emit events for audit trail

⚠️ **Local Hardhat Chain:**
- Development only (not suitable for production)
- Fixed private keys in development
- Replace with testnet (Sepolia) or mainnet in production

---

### 4. Database Security

✅ **Foreign Key Constraints** - Enabled, prevents orphaned records  
✅ **HTTPS** - Use in production (configure at server level)  
✅ **SQL Injection** - Prevented via Eloquent ORM parameterization  
✅ **File Permissions:**
  ```bash
  chmod -R 755 storage
  chmod -R 755 bootstrap/cache
  chmod 644 database/database.sqlite
  ```

---

### 5. Input Validation

**All user inputs validated:**

```php
// Identity submission
'legal_name' => ['required', 'string', 'max:255'],
'identity_type' => ['required', 'string', 'max:80'],
'document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:4096'],
```

**Protection against:**
- XSS (Cross-Site Scripting)
- Large file uploads
- Invalid file types
- SQL injection

---

### 6. Ledger Integrity

✅ **Cryptographic Hashing:**
- Entry hash depends on payload + previous hash
- Changing past entry breaks chain
- Easy to detect tampering

✅ **Inspection Function:**
```php
$ledger->inspect($identity);
// Returns: valid, issues, blocks
```

---

### 7. CSRF Protection

✅ **Laravel CSRF Token:**
- All forms include `@csrf`
- Validates request origin
- Prevents cross-site request forgery

```blade
<form method="POST" action="/identity">
    @csrf
    <!-- form fields -->
</form>
```

---

### 8. Rate Limiting (Recommended for Production)

Add to routes:
```php
Route::middleware('throttle:60,1')->group(function () {
    // 60 requests per minute
});
```

---

### 9. Environment Secrets

**Never commit to Git:**
- `.env` file
- Private keys
- Database credentials
- API tokens

**`.gitignore` includes:**
```
.env
.env.local
.env.*.php
database/database.sqlite
storage/app/blockchain/
node_modules/
vendor/
```

---

### 10. HTTPS/SSL (Production)

**Development:** HTTP is fine  
**Production:** Must use HTTPS

Configure in `.env`:
```env
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIES=true
```

Configure web server (Apache/Nginx) with SSL certificate.

---

### 11. Admin Account Security

✅ **Strong Passwords:** Require 12+ characters  
✅ **Two-Factor Auth:** Enable in production  
✅ **Audit Logs:** All admin actions recorded  
✅ **Limited Admin Access:** Promote only trusted users  

---

### 12. Document Upload Security

**Protections:**
- File type whitelist: pdf, jpg, jpeg, png, webp
- Max size: 4MB
- Stored outside public web root
- Named with ULID (unpredictable)
- SHA256 hash verified

**Access:** Only via authenticated download endpoint

---

## Project Development Summary

### Key Achievements

1. **Decentralized Identity System:** Users create digital identities with blockchain anchoring
2. **Immutable Ledger:** Every action recorded in tamper-proof ledger
3. **Admin Verification:** Streamlined review and approval workflow
4. **Public Verification:** Third parties can verify identities without backend access
5. **Smart Contracts:** Solidity contract manages identity lifecycle
6. **Blockchain Integration:** Hardhat + ethers.js for blockchain interaction
7. **Modern UI:** Livewire + Tailwind CSS for responsive interface
8. **Zero Database Costs:** SQLite deployment avoids monthly database fees

---

### File Structure Summary

```
├── app/
│   ├── Models/
│   │   ├── DigitalIdentity.php      (Identity record model)
│   │   ├── LedgerEntry.php          (Ledger entry model)
│   │   └── User.php                 (User account model)
│   ├── Http/
│   │   └── Controllers/
│   │       ├── IdentityController.php           (User identity operations)
│   │       ├── PublicVerificationController.php (Public verify endpoint)
│   │       └── Admin/
│   │           ├── IdentityReviewController.php (Admin verify/reject)
│   │           ├── LedgerIntegrityController.php
│   │           ├── SmartContractController.php
│   │           └── UserManagementController.php
│   └── Services/
│       ├── IdentityHasher.php           (Cryptographic hashing)
│       ├── IdentityLedger.php           (Ledger operations)
│       ├── IdentityWorkflow.php         (State machine)
│       └── BlockchainIdentityRegistry.php (Smart contract calls)
├── config/
│   ├── blockchain.php                (Blockchain settings)
│   └── database.php                  (Database config)
├── database/
│   ├── migrations/                   (Schema versions)
│   └── database.sqlite               (SQLite file)
├── contracts/
│   └── DecentralizedIdentityRegistry.sol (Smart contract)
├── scripts/
│   ├── deploy-contract.js            (Deploy script)
│   └── anchor-identity.js            (Blockchain anchor script)
├── resources/
│   ├── views/                        (Blade templates)
│   ├── css/                          (Tailwind styles)
│   └── js/                           (Frontend JS)
└── storage/
    └── app/
        ├── blockchain/               (ABI, deployment info)
        └── identity-documents/       (Uploaded documents)
```

---

### Technology Stack Overview

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Frontend** | Livewire 4.1, Flux, Tailwind CSS | Reactive UI components |
| **Backend** | Laravel 13.17, PHP 8.3 | Web application logic |
| **Database** | SQLite 3 | Data persistence |
| **Blockchain** | Hardhat, Solidity 0.8.24, ethers.js | Smart contracts |
| **Build** | Vite, Node.js | Asset bundling |
| **Testing** | Pest PHP | Test suite |
| **Authentication** | Laravel Fortify | User auth/MFA |

---

### Deployment Recommendations

**Development:** ✅ Current setup works perfectly  
**Staging:** Use SQLite + Hardhat testnet (Sepolia)  
**Production:** Consider PostgreSQL + Ethereum mainnet  

For **school project with no costs:**
- ✅ SQLite is perfect
- ✅ Local Hardhat blockchain is sufficient
- ✅ Single-server deployment works fine

---

## Conclusion

This is a comprehensive, well-structured blockchain identity management system that demonstrates:

- Advanced Laravel architecture with service layer
- Blockchain integration patterns
- Cryptographic security practices
- State machine workflows
- Immutable audit trails
- Scalable, maintainable code

The system successfully balances innovation (blockchain) with practicality (SQLite for cost-free deployment), making it an excellent school project demonstrating full-stack development expertise.

---

**Documentation Created:** August 2026  
**For Questions:** Refer to inline code comments and BLOCKCHAIN_SETUP.md file

