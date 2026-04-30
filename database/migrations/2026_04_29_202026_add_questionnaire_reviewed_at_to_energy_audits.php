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
            $table->timestamp('questionnaire_reviewed_at')->nullable()->after('questionnaire_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('energy_audits', function (Blueprint $table) {
            $table->dropColumn('questionnaire_reviewed_at');
        });
    }
};
