<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getSchemaBuilder()->hasTable('audit_units')) {
            DB::table('audit_units')->where('name', 'st C')->update(['name' => '°C']);
        }
    }

    public function down(): void
    {
        if (DB::getSchemaBuilder()->hasTable('audit_units')) {
            DB::table('audit_units')->where('name', '°C')->update(['name' => 'st C']);
        }
    }
};
