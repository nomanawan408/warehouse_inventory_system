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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('cascade');  // Link to sale (optional)
            $table->foreignId('account_id')->constrained()->onDelete('cascade');  // Link to customer account
            $table->decimal('amount_paid', 10, 2);  // Payment amount
            $table->enum('payment_method', ['cash', 'online']);  // Only manual payments
            $table->text('description')->nullable();  // Notes (e.g., "Paid by hand")
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
