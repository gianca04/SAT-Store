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
                'name' => 'ATLANTIC',
                'description' => 'Atlantic es un fabricante francés líder en soluciones de confort térmico desde 1968. Especialista en sistemas de calefacción, producción de agua caliente sanitaria, climatización y ventilación VMC. Con más de 50 años de experiencia, 5000 colaboradores y 13 fábricas en Francia, Atlantic desarrolla radiadores eléctricos, bombas de calor, calderas de gas, calentadores de agua y sistemas de ventilación para mejorar el confort del hogar y la eficiencia energética.',
                'foto_path' => 'brands/atlantic.jpg',
            ],
            [
                'name' => 'KNAUF',
                'description' => 'Knauf es un grupo familiar alemán líder mundial en materiales de construcción con más de 90 años de experiencia. Especialista en soluciones de aislamiento térmico y acústico, placas de yeso, paneles de poliestireno expandido, poliuretano y lana de madera. Con presencia en más de 90 países, 85 plantas de procesamiento y 320 sitios de producción, Knauf ofrece materiales de alta calidad para suelos, techos, paredes, tejados y fachadas, enfocándose en resistencia al fuego, eficiencia energética y sostenibilidad.',
                'foto_path' => 'brands/knauf.jpg',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
