<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heat_recovery_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('fuel_type', 20)->default('gas');
            $table->float('boiler_power')->nullable();
            $table->float('boiler_efficiency')->nullable();
            $table->float('flue_temp_in')->nullable();
            $table->float('mass_flow')->nullable();
            $table->float('xh2o')->nullable();
            $table->string('medium_type', 20)->default('water');
            $table->float('medium_temp_supply')->nullable();
            $table->float('medium_temp_return')->nullable();
            $table->float('medium_pressure')->nullable();
            $table->float('medium_flow')->nullable();
            $table->json('exchangers')->nullable();
            $table->float('result_dry_kw')->nullable();
            $table->float('result_wet_kw')->nullable();
            $table->float('result_total_kw')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heat_recovery_calculations');
    }
};
