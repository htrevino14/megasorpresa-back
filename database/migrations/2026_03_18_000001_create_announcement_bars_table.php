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
        Schema::create('announcement_bars', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->string('link_url')->nullable();
            $table->string('link_label')->nullable();
            $table->string('bg_color', 7)->default('#0072E3');
            $table->string('text_color', 7)->default('#FFFFFF');
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_bars');
    }
};
