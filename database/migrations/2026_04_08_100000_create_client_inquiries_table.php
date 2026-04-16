<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_inquiries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('audit_type_id')->nullable()->constrained()->nullOnDelete();
            $table->string('audit_type_name')->nullable(); // snapshot of name at time of submission
            $table->text('message')->nullable();
            $table->string('status')->default('new'); // new, in_review, accepted, rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_inquiries');
    }
};
