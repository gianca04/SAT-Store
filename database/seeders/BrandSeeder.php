<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Apple',
                'description' => 'Empresa multinacional estadounidense que diseña y produce equipos electrónicos, software y servicios en línea.',
                'foto_path' => 'brands/apple.jpg',
            ],
            [
                'name' => 'Samsung',
                'description' => 'Conglomerado surcoreano de empresas multinacionales con sede en Samsung Town, Seúl.',
                'foto_path' => 'brands/samsung.jpg',
            ],
            [
                'name' => 'Sony',
                'description' => 'Corporación multinacional japonesa con sede en Minato, Tokio, conocida por sus productos electrónicos.',
                'foto_path' => 'brands/sony.jpg',
            ],
            [
                'name' => 'LG',
                'description' => 'Conglomerado surcoreano de empresas multinacionales que opera en todo el mundo.',
                'foto_path' => 'brands/lg.jpg',
            ],
            [
                'name' => 'Microsoft',
                'description' => 'Corporación multinacional de tecnología estadounidense con sede en Redmond, Washington.',
                'foto_path' => 'brands/microsoft.jpg',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
