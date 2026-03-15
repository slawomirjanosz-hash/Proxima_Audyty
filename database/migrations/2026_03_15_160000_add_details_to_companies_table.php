<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('street')->nullable()->after('city');
            $table->string('postal_code', 20)->nullable()->after('street');
            $table->text('description')->nullable()->after('postal_code');
            $table->string('phone', 50)->nullable()->after('description');
            $table->string('email')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['street', 'postal_code', 'description', 'phone', 'email']);
        });
    }
};
