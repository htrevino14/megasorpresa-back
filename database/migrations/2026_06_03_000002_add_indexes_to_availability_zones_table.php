<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The pivot table already has a unique composite index on
     * (product_id, city_id) which optimizes lookups that lead with
     * product_id. The catalog, however, filters products by city_id,
     * so we add a reverse composite index (city_id, product_id) to
     * cover those queries efficiently.
     */
    public function up(): void
    {
        Schema::table('availability_zones', function (Blueprint $table) {
            $table->index(['city_id', 'product_id'], 'availability_zones_city_product_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('availability_zones', function (Blueprint $table) {
            $table->dropIndex('availability_zones_city_product_index');
        });
    }
};
