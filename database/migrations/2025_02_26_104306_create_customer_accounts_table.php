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
        Schema::create('customer_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); // Customer Reference
            $table->decimal('total_purchases', 10, 2)->default(0); // Total amount spent
            $table->decimal('total_paid', 10, 2)->default(0); // Amount paid by customer
            $table->decimal('pending_balance', 10, 2)->default(0); // Remaining amount to be paid
            $table->timestamp('last_payment_date')->nullable(); // Date of the most recent payment
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_accounts');
    }
};
