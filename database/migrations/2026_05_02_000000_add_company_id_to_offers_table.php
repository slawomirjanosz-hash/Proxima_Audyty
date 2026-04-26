<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('offers', 'company_id')) {
            Schema::table('offers', function (Blueprint $table): void {
                $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete()->after('crm_deal_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('offers', 'company_id')) {
            Schema::table('offers', function (Blueprint $table): void {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};
