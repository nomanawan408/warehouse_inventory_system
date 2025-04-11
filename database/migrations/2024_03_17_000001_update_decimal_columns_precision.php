<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateDecimalColumnsPrecision extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('total_amount', 15, 2)->change();
            $table->decimal('discount', 15, 2)->change();
            $table->decimal('net_total', 15, 2)->change();
            $table->decimal('amount_paid', 15, 2)->change();
            $table->decimal('pending_amount', 15, 2)->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->change();
            $table->decimal('discount', 15, 2)->change();
            $table->decimal('profit_margin', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });

        Schema::table('customer_accounts', function (Blueprint $table) {
            $table->decimal('total_purchases', 15, 2)->change();
            $table->decimal('total_paid', 15, 2)->change();
            $table->decimal('pending_balance', 15, 2)->change();
        });

        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->change();
            $table->decimal('discount', 10, 2)->change();
            $table->decimal('net_total', 10, 2)->change();
            $table->decimal('amount_paid', 10, 2)->change();
            $table->decimal('pending_amount', 10, 2)->change();
        });

        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('discount', 10, 2)->change();
            $table->decimal('profit_margin', 10, 2)->change();
            $table->decimal('total', 10, 2)->change();
        });

        Schema::table('customer_accounts', function (Blueprint $table) {
            $table->decimal('total_purchases', 10, 2)->change();
            $table->decimal('total_paid', 10, 2)->change();
            $table->decimal('pending_balance', 10, 2)->change();
        });

        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
    }
} 