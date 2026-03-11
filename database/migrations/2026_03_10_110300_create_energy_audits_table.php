<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('energy_audits', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('status')->default('new');
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('auditor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('energy_audits');
    }
};
