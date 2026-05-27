<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type_code')->unique();
            $table->text('description')->nullable();
            $table->longText('html_content')->nullable();
            $table->decimal('default_km_rate', 8, 2)->default(1.50);
            $table->decimal('default_hour_rate', 8, 2)->default(80.00);
            $table->decimal('default_auditor_hours', 8, 2)->default(8.00);
            $table->json('default_items')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_templates');
    }
};
