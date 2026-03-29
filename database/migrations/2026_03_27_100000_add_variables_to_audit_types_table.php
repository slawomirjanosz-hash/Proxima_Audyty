<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('audit_types', 'variables')) {
            Schema::table('audit_types', function (Blueprint $table): void {
                $table->json('variables')->nullable()->after('formulas');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('audit_types', 'variables')) {
            Schema::table('audit_types', function (Blueprint $table): void {
                $table->dropColumn('variables');
            });
        }
    }
};
