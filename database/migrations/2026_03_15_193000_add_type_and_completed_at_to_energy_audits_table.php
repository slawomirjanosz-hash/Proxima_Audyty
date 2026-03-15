<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('energy_audits', function (Blueprint $table) {
            $table->string('audit_type')->nullable()->after('title');
            $table->timestamp('completed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('energy_audits', function (Blueprint $table) {
            $table->dropColumn(['audit_type', 'completed_at']);
        });
    }
};
