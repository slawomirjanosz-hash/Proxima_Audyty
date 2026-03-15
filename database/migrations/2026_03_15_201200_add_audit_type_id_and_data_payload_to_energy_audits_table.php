<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('energy_audits', function (Blueprint $table) {
            $table->foreignId('audit_type_id')->nullable()->after('audit_type')->constrained('audit_types')->nullOnDelete();
            $table->json('data_payload')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('energy_audits', function (Blueprint $table) {
            $table->dropConstrainedForeignId('audit_type_id');
            $table->dropColumn('data_payload');
        });
    }
};
