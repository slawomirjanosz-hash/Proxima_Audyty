<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_stages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 20)->default('#3b82f6');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
        });

        DB::table('crm_stages')->insert([
            ['name' => 'Nowy Lead', 'slug' => 'nowy_lead', 'color' => '#64748b', 'order' => 1, 'is_active' => true, 'is_closed' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kontakt', 'slug' => 'kontakt', 'color' => '#2563eb', 'order' => 2, 'is_active' => true, 'is_closed' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wycena', 'slug' => 'wycena', 'color' => '#eab308', 'order' => 3, 'is_active' => true, 'is_closed' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Negocjacje', 'slug' => 'negocjacje', 'color' => '#f97316', 'order' => 4, 'is_active' => true, 'is_closed' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wygrana', 'slug' => 'wygrana', 'color' => '#16a34a', 'order' => 5, 'is_active' => true, 'is_closed' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Przegrana', 'slug' => 'przegrana', 'color' => '#dc2626', 'order' => 6, 'is_active' => true, 'is_closed' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_stages');
    }
};
