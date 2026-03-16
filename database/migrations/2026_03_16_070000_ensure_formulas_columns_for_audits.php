<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('audit_types') && ! Schema::hasColumn('audit_types', 'formulas')) {
            Schema::table('audit_types', function (Blueprint $table) {
                $table->json('formulas')->nullable()->after('name');
            });
        }

        if (Schema::hasTable('audit_type_sections') && ! Schema::hasColumn('audit_type_sections', 'formulas')) {
            Schema::table('audit_type_sections', function (Blueprint $table) {
                $table->json('formulas')->nullable()->after('data_fields');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('audit_type_sections') && Schema::hasColumn('audit_type_sections', 'formulas')) {
            Schema::table('audit_type_sections', function (Blueprint $table) {
                $table->dropColumn('formulas');
            });
        }

        if (Schema::hasTable('audit_types') && Schema::hasColumn('audit_types', 'formulas')) {
            Schema::table('audit_types', function (Blueprint $table) {
                $table->dropColumn('formulas');
            });
        }
    }
};
