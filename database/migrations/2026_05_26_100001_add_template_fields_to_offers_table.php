<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->foreignId('offer_template_id')->nullable()->after('id')
                  ->constrained('offer_templates')->nullOnDelete();
            $table->longText('html_content')->nullable()->after('offer_description');
            $table->decimal('km_rate', 8, 2)->nullable()->after('show_unit_prices');
            $table->decimal('hour_rate', 8, 2)->nullable()->after('km_rate');
            $table->decimal('distance_km', 8, 2)->nullable()->after('hour_rate');
            $table->decimal('travel_hours', 8, 2)->nullable()->after('distance_km');
            $table->decimal('travel_cost', 10, 2)->nullable()->after('travel_hours');
            $table->decimal('auditor_hours', 8, 2)->nullable()->after('travel_cost');
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table) {
            $table->dropForeign(['offer_template_id']);
            $table->dropColumn([
                'offer_template_id', 'html_content',
                'km_rate', 'hour_rate', 'distance_km', 'travel_hours', 'travel_cost', 'auditor_hours',
            ]);
        });
    }
};
