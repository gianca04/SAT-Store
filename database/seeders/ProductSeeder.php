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
            // Apple Products
            [
                'brand' => 'Apple',
                'products' => [
                    [
                        'name' => 'iPhone 15 Pro',
                        'description' => 'El iPhone más avanzado de Apple con chip A17 Pro y cámara principal de 48MP.',
                        'active' => true,
                    ],
                    [
                        'name' => 'MacBook Pro 16"',
                        'description' => 'Portátil profesional con chip M3 Pro y pantalla Liquid Retina XDR.',
                        'active' => true,
                    ],
                    [
                        'name' => 'iPad Air',
                        'description' => 'Tablet ligera y poderosa con chip M1 y pantalla de 10.9 pulgadas.',
                        'active' => false,
                    ],
                ]
            ],
            // Samsung Products
            [
                'brand' => 'Samsung',
                'products' => [
                    [
                        'name' => 'Galaxy S24 Ultra',
                        'description' => 'Smartphone premium con S Pen integrado y cámara de 200MP.',
                        'active' => true,
                    ],
                    [
                        'name' => 'Galaxy Tab S9',
                        'description' => 'Tablet premium con pantalla AMOLED de 11 pulgadas y S Pen incluido.',
                        'active' => true,
                    ],
                ]
            ],
            // Sony Products
            [
                'brand' => 'Sony',
                'products' => [
                    [
                        'name' => 'PlayStation 5',
                        'description' => 'Consola de videojuegos de última generación con SSD ultra rápido.',
                        'active' => true,
                    ],
                    [
                        'name' => 'WH-1000XM5',
                        'description' => 'Auriculares inalámbricos con cancelación de ruido líder en la industria.',
                        'active' => true,
                    ],
                ]
            ],
            // LG Products
            [
                'brand' => 'LG',
                'products' => [
                    [
                        'name' => 'OLED TV 55"',
                        'description' => 'Televisor OLED 4K con tecnología AI ThinQ y webOS.',
                        'active' => true,
                    ],
                ]
            ],
            // Microsoft Products
            [
                'brand' => 'Microsoft',
                'products' => [
                    [
                        'name' => 'Surface Pro 9',
                        'description' => 'Laptop 2 en 1 con pantalla táctil de 13 pulgadas y procesador Intel de 12ª generación.',
                        'active' => true,
                    ],
                    [
                        'name' => 'Xbox Series X',
                        'description' => 'Consola de videojuegos más potente de Microsoft con resolución 4K nativa.',
                        'active' => false,
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
