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
            $table->string('document_path')->nullable()->after('document_hash');
            $table->string('document_original_name')->nullable()->after('document_path');
            $table->string('document_mime_type')->nullable()->after('document_original_name');
            $table->unsignedBigInteger('document_size')->nullable()->after('document_mime_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('digital_identities', function (Blueprint $table) {
            $table->dropColumn([
                'document_path',
                'document_original_name',
                'document_mime_type',
                'document_size',
            ]);
        });
    }
};
