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
        Schema::create('company_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('purchase_id')->nullable()->constrained('purchases')->onDelete('set null');
            $table->enum('transaction_type', ['credit', 'debit']); // Use 'credit' for purchases on credit (amount recorded, not paid) and 'debit' when payment is made at the time of purchase
            $table->decimal('amount', 10, 2);
            $table->timestamp('transaction_date')->default(now());
            $table->string('detail')->nullable();// Additional transaction details
            $table->string('payment_method')->nullable();
            $table->string('reference')->nullable(); // Invoice number or bank reference
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_transactions');
    }
};
