<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_companies', function (Blueprint $table) {
            $table->foreignId('system_company_id')
                ->nullable()
                ->after('customer_type_id')
                ->constrained('companies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crm_companies', function (Blueprint $table) {
            $table->dropConstrainedForeignId('system_company_id');
        });
    }
};
