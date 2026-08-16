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
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->string('blockchain_network')->nullable()->after('entry_hash');
            $table->string('blockchain_tx_hash')->nullable()->after('blockchain_network');
            $table->string('contract_address')->nullable()->after('blockchain_tx_hash');
            $table->string('onchain_status')->nullable()->after('contract_address');
            $table->json('onchain_receipt')->nullable()->after('onchain_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropColumn([
                'blockchain_network',
                'blockchain_tx_hash',
                'contract_address',
                'onchain_status',
                'onchain_receipt',
            ]);
        });
    }
};
