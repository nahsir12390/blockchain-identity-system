<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('digital_identities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('did')->unique();
            $table->string('legal_name');
            $table->string('wallet_address')->nullable();
            $table->string('public_key')->nullable();
            $table->string('identity_type');
            $table->string('identity_number_hash');
            $table->string('document_hash')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('blockchain_network')->default('Local Proof Chain');
            $table->string('block_hash')->nullable();
            $table->string('transaction_hash')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('digital_identities');
    }
};
