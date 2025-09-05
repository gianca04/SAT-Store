<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = Brand::all();

        $products = [
            [
                'brand' => 'ATLANTIC',
                'products' => [
                    [
                        'name' => 'ATADOR P/CABLE NEGRO UV 2.5X100MM C/100UND',
                        'description' => 'Atador para cable de color negro con protección UV, dimensiones 2.5x100mm. Incluye 100 unidades por paquete. Ideal para sujeción de cables en instalaciones eléctricas exteriores e interiores.',
                        'active' => true,
                    ],
                    [
                        'name' => 'ATADOR P/CABLE NATURAL 3.5X200MM C/100',
                        'description' => 'Atador para cable de color natural, dimensiones 3.5x200mm. Incluye 100 unidades por paquete. Perfecto para organización de cables en instalaciones eléctricas residenciales y comerciales.',
                        'active' => true,
                    ],
                    [
                        'name' => 'ATADOR P/CABLE NATURAL 4.8X200MM C/100',
                        'description' => 'Atador para cable de color natural, dimensiones 4.8x200mm. Incluye 100 unidades por paquete. Mayor resistencia para cables de mayor diámetro en instalaciones eléctricas.',
                        'active' => true,
                    ],
                    [
                        'name' => 'ATADOR P/CABLE NATURAL 4.8X300MM C/100',
                        'description' => 'Atador para cable de color natural, dimensiones 4.8x300mm. Incluye 100 unidades por paquete. Ideal para agrupación de múltiples cables en instalaciones complejas.',
                        'active' => true,
                    ],
                    [
                        'name' => 'ATADOR P/CABLE NATURAL 4.8X380MM C/100',
                        'description' => 'Atador para cable de color natural, dimensiones 4.8x380mm. Incluye 100 unidades por paquete. Diseñado para sujeción de cables en espacios amplios con alta resistencia.',
                        'active' => true,
                    ],
                    [
                        'name' => 'ATADOR P/CABLE NEGRO UV 7.6X300MM C/100',
                        'description' => 'Atador para cable de color negro con protección UV, dimensiones 7.6x300mm. Incluye 100 unidades por paquete. Resistente a rayos UV para instalaciones exteriores de larga duración.',
                        'active' => true,
                    ],
                    [
                        'name' => 'ATADOR P/CABLE NATURAL 4.8X250MM C/100',
                        'description' => 'Atador para cable de color natural, dimensiones 4.8x250mm. Incluye 100 unidades por paquete. Tamaño intermedio para diversas aplicaciones de organización de cables.',
                        'active' => true,
                    ],
                    [
                        'name' => 'ATADOR P/CABLE NEGRO UV 7.6X380MM C/100',
                        'description' => 'Atador para cable de color negro con protección UV, dimensiones 7.6x380mm. Incluye 100 unidades por paquete. Máxima resistencia y protección para instalaciones exteriores exigentes.',
                        'active' => true,
                    ],
                    [
                        'name' => 'ATADOR P/CABLE NATURAL 4.8X450MM C/100',
                        'description' => 'Atador para cable de color natural, dimensiones 4.8x450mm. Incluye 100 unidades por paquete. Tamaño extra largo para agrupación de cables en instalaciones industriales.',
                        'active' => true,
                    ],
                    [
                        'name' => 'TERMINAL TUBULAR SIMPLE AISLADO 4MM2 GRIS C/100',
                        'description' => 'Terminal tubular simple aislado de 4mm² en color gris. Incluye 100 unidades por paquete. Diseñado para conexiones eléctricas seguras con aislamiento incorporado.',
                        'active' => true,
                    ],
                ]
            ],
            [
                'brand' => 'KNAUF',
                'products' => [
                    [
                        'name' => 'CINTA AISLANTE COLOR NEGRO 3/4" X 20Y',
                        'description' => 'Cinta aislante eléctrica de color negro, dimensiones 3/4 pulgada x 20 yardas. Fabricada con materiales de alta calidad para aislamiento eléctrico confiable en instalaciones residenciales, comerciales e industriales.',
                        'active' => true,
                    ],
                    [
                        'name' => 'CINTA AISLANTE COLOR BLANCO 3/4" X 20Y',
                        'description' => 'Cinta aislante eléctrica de color blanco, dimensiones 3/4 pulgada x 20 yardas. Ideal para identificación y aislamiento de cables en instalaciones donde se requiere diferenciación por colores.',
                        'active' => true,
                    ],
                ]
            ],
        ];

        foreach ($products as $brandProducts) {
            $brand = $brands->where('name', $brandProducts['brand'])->first();
            
            if ($brand) {
                foreach ($brandProducts['products'] as $productData) {
                    Product::create([
                        'brand_id' => $brand->id,
                        'name' => $productData['name'],
                        'description' => $productData['description'],
                        'active' => $productData['active'],
                    ]);
                }
            }
        }
    }
}
