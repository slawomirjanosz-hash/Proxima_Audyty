<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('audit_units', 'kind')) {
            Schema::table('audit_units', function (Blueprint $table) {
                $table->string('kind', 20)->default('number')->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('audit_units', 'kind')) {
            Schema::table('audit_units', function (Blueprint $table) {
                $table->dropColumn('kind');
            });
        }
    }
};
