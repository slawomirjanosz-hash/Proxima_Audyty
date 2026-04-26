<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('iso50001_questionnaire_questions', function (Blueprint $table): void {
            $table->id();
            $table->string('block_key', 10)->default('A');
            $table->string('question_code', 10)->default('');
            $table->text('question_text');
            $table->string('answer_hint')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('iso50001_questionnaire_questions');
    }
};
