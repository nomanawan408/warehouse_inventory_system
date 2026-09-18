<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The sales list is ordered/filtered by updated_at on every page load.
     * Without an index this degrades to a full table scan as sales grow.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->index('updated_at', 'sales_updated_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex('sales_updated_at_index');
        });
    }
};
