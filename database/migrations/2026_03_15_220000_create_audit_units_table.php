<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 64)->unique();
            $table->string('kind', 20)->default('number');
            $table->timestamps();
        });

        DB::table('audit_units')->insert([
            ['name' => 'kW', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'm3/h', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'GJ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => '°C', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bary', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'kPascale', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'mBary', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_units');
    }
};
