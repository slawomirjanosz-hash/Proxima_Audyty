<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_tasks', function (Blueprint $table): void {
            $table->boolean('notify_on_complete')->default(false)->after('completed_at');
            $table->string('notify_frequency')->default('codziennie')->after('notify_on_complete');
            // notify_frequency values: codziennie | co_2_dni | co_tydzien | wylaczone
        });
    }

    public function down(): void
    {
        Schema::table('crm_tasks', function (Blueprint $table): void {
            $table->dropColumn(['notify_on_complete', 'notify_frequency']);
        });
    }
};
