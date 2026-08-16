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
        Schema::table('digital_identities', function (Blueprint $table) {
            $table->string('contract_address')->nullable()->after('blockchain_network');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_identities', function (Blueprint $table) {
            $table->dropColumn('contract_address');
        });
    }
};
