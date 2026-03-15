<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_task_changes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('task_id')->nullable()->constrained('crm_tasks')->nullOnDelete();
            $table->string('entity_type', 50);
            $table->unsignedBigInteger('entity_id');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('change_type', 40);
            $table->json('change_details')->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_task_changes');
    }
};
