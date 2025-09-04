<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPhoto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductPhotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();

        $photoDescriptions = [
            'Foto frontal del producto',
            'Vista lateral del producto',
            'Detalle de la pantalla',
            'Accesorios incluidos',
            'Vista posterior del producto',
            'Producto en uso',
            'Detalles técnicos',
            'Comparación de tamaños',
        ];

        foreach ($products as $product) {
            // Crear entre 2 y 4 fotos por producto
            $photoCount = rand(2, 4);
            
            for ($i = 0; $i < $photoCount; $i++) {
                ProductPhoto::create([
                    'product_id' => $product->id,
                    'description' => $photoDescriptions[array_rand($photoDescriptions)] . ' - ' . $product->name,
                ]);
            }
        }
    }
}
