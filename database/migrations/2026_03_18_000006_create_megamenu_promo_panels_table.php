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
        Schema::create('megamenu_promo_panels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('megamenu_category_id')
                ->unique()
                ->constrained('megamenu_categories')
                ->cascadeOnDelete();
            $table->string('badge')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('emoji', 10)->nullable();
            $table->string('bg_color', 7)->nullable();
            $table->string('link_text')->nullable();
            $table->string('link_url')->nullable();
            $table->string('image_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('megamenu_promo_panels');
    }
};
