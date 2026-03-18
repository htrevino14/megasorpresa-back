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
        Schema::create('megamenu_subcategory_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('megamenu_category_id')
                ->constrained('megamenu_categories')
                ->cascadeOnDelete();
            $table->string('title');
            $table->foreignId('category_id_destination')
                ->nullable()
                ->constrained('categories')
                ->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['megamenu_category_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('megamenu_subcategory_groups');
    }
};
