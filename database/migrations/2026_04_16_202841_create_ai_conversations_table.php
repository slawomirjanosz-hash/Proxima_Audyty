<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('context_type')->nullable(); // np. 'energy_audit', 'iso50001', 'offer'
            $table->unsignedBigInteger('context_id')->nullable(); // id rekordu którego dotyczy
            $table->string('title')->nullable();
            $table->string('status')->default('active'); // active, archived
            $table->timestamps();

            $table->index(['context_type', 'context_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_conversations');
    }
};
