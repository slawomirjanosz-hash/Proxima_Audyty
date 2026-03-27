<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iso50001_templates', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->default('Szablon ISO 50001');
            $table->json('steps');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iso50001_templates');
    }
};
