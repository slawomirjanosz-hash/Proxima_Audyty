<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table): void {
            $table->string('key')->primary();
            $table->string('value');
            $table->string('description')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Wartości domyślne – wskaźniki emisji CO₂ dla energii elektrycznej KSE
        // Źródło: KOBiZE, rok sprawozdawczy 2024, publikacja grudzień 2025
        DB::table('system_settings')->insert([
            [
                'key'         => 'co2_el_comb_factor',
                'value'       => '0.710',
                'description' => 'Wskaźnik emisji CO₂ dla energii elektrycznej – źródła spalania paliw [kg CO₂/kWh]. KOBiZE rok 2024.',
                'updated_by'  => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'co2_el_nat_factor',
                'value'       => '0.640',
                'description' => 'Wskaźnik emisji CO₂ dla energii elektrycznej – krajowy z OZE i stratami sieciowymi [kg CO₂/kWh]. KOBiZE rok 2024.',
                'updated_by'  => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'co2_el_grid_display',
                'value'       => '553',
                'description' => 'Wartość wskaźnika emisji wyświetlana w stałych energetycznych [g CO₂/kWh]. KOBiZE rok 2024.',
                'updated_by'  => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'co2_el_year',
                'value'       => '2024',
                'description' => 'Rok sprawozdawczy wskaźników emisji CO₂ (publikacja KOBiZE).',
                'updated_by'  => null,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
