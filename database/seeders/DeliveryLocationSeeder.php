<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\DeliveryArea;
use Illuminate\Database\Seeder;

class DeliveryLocationSeeder extends Seeder
{
    public function run(): void
    {
        $cairo = City::firstOrCreate(
            ['name' => 'Cairo'],
            ['is_active' => true]
        );

        foreach (['Nasr City', 'Sheraton'] as $areaName) {
            DeliveryArea::firstOrCreate(
                ['city_id' => $cairo->id, 'name' => $areaName],
                ['is_active' => true]
            );
        }
    }
}
