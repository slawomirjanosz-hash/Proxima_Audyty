<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_type_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_type_id')->constrained('audit_types')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('position')->default(1);
            $table->json('tasks')->nullable();
            $table->json('data_fields')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_type_sections');
    }
};
