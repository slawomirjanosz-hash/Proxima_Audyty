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
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->json('protocol_data')->nullable()->after('status'); // wygenerowany protokół
            $table->timestamp('protocol_generated_at')->nullable()->after('protocol_data');
        });
    }

    public function down(): void
    {
        Schema::table('ai_conversations', function (Blueprint $table) {
            $table->dropColumn(['protocol_data', 'protocol_generated_at']);
        });
    }
};
