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
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_transaction_id')->constrained('company_transactions')->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->integer('previous_stock')->nullable();
            $table->integer('current_stock')->nullable();
            $table->string('transaction_type')->nullable(); // purchase, sale, return, etc.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
