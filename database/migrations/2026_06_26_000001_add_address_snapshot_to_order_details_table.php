<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Snapshot de la dirección de envío en order_details.
 *
 * Congela la dirección utilizada al momento de la compra para que el detalle
 * del pedido (GET /api/orders/{id}) siga siendo históricamente preciso aunque
 * el usuario edite o elimine posteriormente su dirección en user_addresses.
 *
 * Todas las columnas son nullable porque los pedidos existentes no cuentan con
 * este snapshot; se poblarán para los pedidos creados a partir de ahora.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->string('ext_number', 20)->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('dwelling_type')->nullable();
            $table->string('zip_code', 10)->nullable();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->text('references')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropConstrainedForeignId('state_id');
            $table->dropColumn([
                'street',
                'ext_number',
                'neighborhood',
                'dwelling_type',
                'zip_code',
                'references',
            ]);
        });
    }
};
