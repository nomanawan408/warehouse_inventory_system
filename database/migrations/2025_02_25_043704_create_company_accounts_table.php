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
        Schema::create('company_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade'); // Manufacturer Reference
            $table->decimal('total_purchases', 10, 2)->default(0); // Purchases from supplier
            $table->decimal('total_paid', 10, 2)->default(0); // Amount paid to supplier
            $table->decimal('pending_balance', 10, 2)->default(0); // Remaining balance
            $table->timestamp('last_payment_date')->nullable(); // Date of the most recent payment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies_accounts');
    }
};
