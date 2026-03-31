<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The original energy_audits migration created company_id as NOT NULL,
     * but the "New Audit" form allows selecting "Brak" (no company),
     * which submits an empty value → NULL → DB constraint violation → HTTP 500.
     */
    public function up(): void
    {
        Schema::table('energy_audits', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Only revert if all rows have a company_id set to avoid data loss
        Schema::table('energy_audits', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->nullable(false)->change();
        });
    }
};
