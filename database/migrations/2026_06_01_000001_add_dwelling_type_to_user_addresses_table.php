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
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->enum('dwelling_type', [
                'casa',
                'hotel',
                'restaurante',
                'escuela',
                'oficina',
                'hospital',
                'teatro',
                'plaza comercial',
                'departamento',
                'otro',
            ])->default('casa')->after('neighborhood');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->dropColumn('dwelling_type');
        });
    }
};
