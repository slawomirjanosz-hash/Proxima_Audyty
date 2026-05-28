<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offer_templates', function (Blueprint $table) {
            $table->string('audit_category', 50)->nullable()->after('type_code');
        });
    }

    public function down(): void
    {
        Schema::table('offer_templates', function (Blueprint $table) {
            $table->dropColumn('audit_category');
        });
    }
};
