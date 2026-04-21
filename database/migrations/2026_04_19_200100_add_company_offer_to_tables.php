<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete()->after('crm_deal_id');
        });

        Schema::table('client_inquiries', function (Blueprint $table) {
            $table->foreignId('offer_id')->nullable()->constrained('offers')->nullOnDelete()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('client_inquiries', function (Blueprint $table) {
            $table->dropForeign(['offer_id']);
            $table->dropColumn('offer_id');
        });
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }
};
