<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('heat_recovery_calculations', function (Blueprint $table) {
            $table->float('boiler_load_pct')->nullable()->after('boiler_efficiency');
        });
    }

    public function down(): void
    {
        Schema::table('heat_recovery_calculations', function (Blueprint $table) {
            $table->dropColumn('boiler_load_pct');
        });
    }
};
