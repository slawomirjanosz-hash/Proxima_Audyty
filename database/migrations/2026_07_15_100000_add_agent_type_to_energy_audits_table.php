<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('energy_audits', 'agent_type')) {
            Schema::table('energy_audits', function (Blueprint $table) {
                $table->string('agent_type')->nullable()->after('audit_type_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('energy_audits', function (Blueprint $table) {
            $table->dropColumn('agent_type');
        });
    }
};
