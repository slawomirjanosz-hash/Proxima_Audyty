<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iso50001_audits', function (Blueprint $table): void {
            $table->date('due_date')->nullable()->after('current_step');
        });
    }

    public function down(): void
    {
        Schema::table('iso50001_audits', function (Blueprint $table): void {
            $table->dropColumn('due_date');
        });
    }
};
