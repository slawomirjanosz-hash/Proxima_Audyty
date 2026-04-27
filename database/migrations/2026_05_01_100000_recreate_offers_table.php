<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK from client_inquiries so PostgreSQL allows dropping the offers table
        if (Schema::hasTable('client_inquiries') && Schema::hasColumn('client_inquiries', 'offer_id')) {
            Schema::table('client_inquiries', function (Blueprint $table) {
                $table->dropForeign(['offer_id']);
            });
        }

        Schema::dropIfExists('offers');

        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->string('offer_number')->nullable();
            $table->string('offer_title');
            $table->date('offer_date')->nullable();
            $table->text('offer_description')->nullable();
            $table->json('services')->nullable();
            $table->json('works')->nullable();
            $table->json('materials')->nullable();
            $table->json('custom_sections')->nullable();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('status', ['portfolio', 'inprogress', 'archived'])->default('portfolio');
            $table->foreignId('crm_deal_id')->nullable()->constrained('crm_deals')->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('customer_name')->nullable();
            $table->string('customer_nip')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_city')->nullable();
            $table->string('customer_postal_code')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('profit_percent', 8, 2)->default(0);
            $table->decimal('profit_amount', 10, 2)->default(0);
            $table->boolean('schedule_enabled')->default(false);
            $table->json('schedule')->nullable();
            $table->json('payment_terms')->nullable();
            $table->boolean('show_unit_prices')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offers');
    }
};
