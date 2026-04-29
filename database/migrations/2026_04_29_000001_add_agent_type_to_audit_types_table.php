<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('audit_types', 'agent_type')) {
            Schema::table('audit_types', function (Blueprint $table) {
                $table->string('agent_type')->nullable()->after('category');
            });
        }

        // ISO 50001 -> iso50001
        DB::table('audit_types')->where('category', 'iso')->update(['agent_type' => 'iso50001']);

        // Energy types -> map by name keyword
        $keywordMap = [
            'sprężarkowni'  => 'compressor_room',
            'kotłowni'      => 'boiler_room',
            'suszarni'      => 'drying_room',
            'budynk'        => 'buildings',
            'technologiczn' => 'technological_processes',
        ];

        $energyTypes = DB::table('audit_types')->where('category', 'energy')->get();
        foreach ($energyTypes as $type) {
            $name      = mb_strtolower($type->name);
            $agentType = 'general';
            foreach ($keywordMap as $keyword => $agent) {
                if (str_contains($name, $keyword)) {
                    $agentType = $agent;
                    break;
                }
            }
            DB::table('audit_types')->where('id', $type->id)->update(['agent_type' => $agentType]);
        }

        // White cert types -> map by name keyword (bc_ prefix)
        $bcKeywordMap = [
            'sprężarkowni'  => 'bc_compressor_room',
            'kotłowni'      => 'bc_boiler_room',
            'suszarni'      => 'bc_drying_room',
            'budynk'        => 'bc_buildings',
            'technologiczn' => 'bc_technological_processes',
        ];

        $bcTypes = DB::table('audit_types')->where('category', 'white_cert')->get();
        foreach ($bcTypes as $type) {
            $name      = mb_strtolower($type->name);
            $agentType = null; // generic white cert = show all bc_* options
            foreach ($bcKeywordMap as $keyword => $agent) {
                if (str_contains($name, $keyword)) {
                    $agentType = $agent;
                    break;
                }
            }
            if ($agentType) {
                DB::table('audit_types')->where('id', $type->id)->update(['agent_type' => $agentType]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('audit_types', 'agent_type')) {
            Schema::table('audit_types', function (Blueprint $table) {
                $table->dropColumn('agent_type');
            });
        }
    }
};
