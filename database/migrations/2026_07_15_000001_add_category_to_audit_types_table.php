<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('audit_types', 'category')) {
            Schema::table('audit_types', function (Blueprint $table) {
                $table->string('category')->default('energy')->after('name');
            });
        }

        // Ensure ISO 50001 exists with correct category
        $iso = DB::table('audit_types')->where('name', 'ISO 50001')->first();
        if ($iso) {
            DB::table('audit_types')->where('id', $iso->id)->update(['category' => 'iso']);
        } else {
            DB::table('audit_types')->insert([
                'name'       => 'ISO 50001',
                'category'   => 'iso',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Ensure Białe certyfikaty exists with correct category
        $bc = DB::table('audit_types')->where('name', 'Białe certyfikaty')->first();
        if ($bc) {
            DB::table('audit_types')->where('id', $bc->id)->update(['category' => 'white_cert']);
        } else {
            DB::table('audit_types')->insert([
                'name'       => 'Białe certyfikaty',
                'category'   => 'white_cert',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('audit_types', 'category')) {
            Schema::table('audit_types', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
