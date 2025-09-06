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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('code', 3)->unique(); // Código de la moneda, e.g., 'USD', 'EUR'
            $table->string('name'); // Nombre de la moneda, e.g., 'Dólar Estadounidense'
            $table->string('symbol')->nullable(); // Símbolo de la moneda, e
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
