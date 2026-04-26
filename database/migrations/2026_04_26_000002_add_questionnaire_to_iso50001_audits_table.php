<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('iso50001_audits', function (Blueprint $table): void {
            $table->json('questionnaire_answers')->nullable()->after('answers');
            $table->boolean('questionnaire_completed')->default(false)->after('questionnaire_answers');
        });
    }

    public function down(): void
    {
        Schema::table('iso50001_audits', function (Blueprint $table): void {
            $table->dropColumn(['questionnaire_answers', 'questionnaire_completed']);
        });
    }
};
