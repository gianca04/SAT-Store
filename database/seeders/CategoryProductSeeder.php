<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir las categorías y los productos que se asociarán
        $categories = [
            'Cables' => [
                'ATADOR P/CABLE NEGRO UV 2.5X100MM C/100UND',
                'ATADOR P/CABLE NATURAL 3.5X200MM C/100',
                'ATADOR P/CABLE NATURAL 4.8X200MM C/100',
                'ATADOR P/CABLE NATURAL 4.8X300MM C/100',
                'ATADOR P/CABLE NATURAL 4.8X380MM C/100',
                'ATADOR P/CABLE NATURAL 4.8X450MM C/100',
            ],
            'Accesorios Eléctricos' => [
                'CINTA AISLANTE COLOR NEGRO 3/4" X 20Y',
                'CINTA AISLANTE COLOR BLANCO 3/4" X 20Y',
                'TERMINAL TUBULAR SIMPLE AISLADO 4MM2 GRIS C/100',
            ],
            'Atadores UV' => [
                'ATADOR P/CABLE NEGRO UV 2.5X100MM C/100UND',
                'ATADOR P/CABLE NEGRO UV 7.6X300MM C/100',
                'ATADOR P/CABLE NEGRO UV 7.6X380MM C/100',
            ],
            'Atadores Naturales' => [
                'ATADOR P/CABLE NATURAL 3.5X200MM C/100',
                'ATADOR P/CABLE NATURAL 4.8X200MM C/100',
                'ATADOR P/CABLE NATURAL 4.8X300MM C/100',
                'ATADOR P/CABLE NATURAL 4.8X380MM C/100',
                'ATADOR P/CABLE NATURAL 4.8X450MM C/100',
            ],
            'Terminales Eléctricos' => [
                'TERMINAL TUBULAR SIMPLE AISLADO 4MM2 GRIS C/100',
            ],
        ];

        foreach ($categories as $categoryName => $productNames) {
            // Buscar o crear la categoría
            $category = Category::firstOrCreate(['name' => $categoryName], [
                'description' => "$categoryName relacionados con productos eléctricos."
            ]);

            foreach ($productNames as $productName) {
                // Buscar el producto por nombre
                $product = Product::where('name', $productName)->first();

                if ($product) {
                    // Asociar el producto a la categoría si no está ya asociado
                    $product->categories()->syncWithoutDetaching([$category->id]);
                }
            }
        }
    }
}
