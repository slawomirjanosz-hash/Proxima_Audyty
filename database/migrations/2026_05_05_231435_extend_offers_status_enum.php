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
        Schema::table('offers', function (Blueprint $table) {
            // Change enum to string to support: portfolio, inprogress, sent, accepted, archived
            $table->string('status', 20)->default('portfolio')->change();
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->enum('status', ['portfolio', 'inprogress', 'archived'])->default('portfolio')->change();
        });
    }
};
