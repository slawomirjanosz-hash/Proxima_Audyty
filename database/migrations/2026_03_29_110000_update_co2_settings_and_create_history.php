<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Przelicz wartości z kg/kWh na g/kWh i usuń zbędną kolumnę grid_display
        DB::table('system_settings')->where('key', 'co2_el_comb_factor')->update(['value' => '717']);
        DB::table('system_settings')->where('key', 'co2_el_nat_factor')->update(['value' => '552']);
        DB::table('system_settings')->where('key', 'co2_el_grid_display')->delete();

        // Tabela historii wskaźników CO₂ (informacyjna)
        Schema::create('co2_indicators_history', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('comb_factor')->comment('Wskaźnik – źródła spalania [g CO₂/kWh]');
            $table->unsignedSmallInteger('nat_factor')->comment('Wskaźnik krajowy z OZE [g CO₂/kWh]');
            $table->string('source_url', 500)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->unique('year');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });

        // Dodaj bieżące wartości jako pierwszy rekord historii
        DB::table('co2_indicators_history')->insert([
            'year'        => 2024,
            'comb_factor' => 717,
            'nat_factor'  => 552,
            'source_url'  => 'https://www.kobize.pl/uploads/materialy/materialy_do_pobrania/aktualnosci/2025/142_Wskazniki_emisyjnosci_2025.pdf',
            'created_by'  => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('co2_indicators_history');

        // Przywróć stare wartości
        DB::table('system_settings')->where('key', 'co2_el_comb_factor')->update(['value' => '0.710']);
        DB::table('system_settings')->where('key', 'co2_el_nat_factor')->update(['value' => '0.640']);
        DB::table('system_settings')->insertOrIgnore([
            'key'         => 'co2_el_grid_display',
            'value'       => '553',
            'description' => 'Wartość wyświetlana w stałych energetycznych [g CO₂/kWh].',
            'updated_by'  => null,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
};
