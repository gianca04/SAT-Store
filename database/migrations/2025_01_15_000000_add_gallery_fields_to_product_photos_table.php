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
        Schema::table('product_photos', function (Blueprint $table) {
            // Verificar si las columnas ya existen antes de agregarlas
            if (!Schema::hasColumn('product_photos', 'is_primary')) {
                $table->boolean('is_primary')->default(false)->after('description');
            }
            
            if (!Schema::hasColumn('product_photos', 'position')) {
                $table->integer('position')->default(1)->after('is_primary');
            }
            
            // Agregar índices para mejor performance
            $table->index(['product_id', 'is_primary']);
            $table->index(['product_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_photos', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'is_primary']);
            $table->dropIndex(['product_id', 'position']);
            $table->dropColumn(['is_primary', 'position']);
        });
    }
};
