<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('audit_type_sections', function (Blueprint $table) {
            $table->json('formulas')->nullable()->after('data_fields');
        });
    }

    public function down(): void
    {
        Schema::table('audit_type_sections', function (Blueprint $table) {
            $table->dropColumn('formulas');
        });
    }
};
