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
        Schema::table('energy_audits', function (Blueprint $table) {
            if (! Schema::hasColumn('energy_audits', 'offer_id')) {
                $table->foreignId('offer_id')->nullable()->constrained('offers')->nullOnDelete()->after('auditor_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('energy_audits', function (Blueprint $table) {
            if (Schema::hasColumn('energy_audits', 'offer_id')) {
                $table->dropForeign(['offer_id']);
                $table->dropColumn('offer_id');
            }
        });
    }
};
