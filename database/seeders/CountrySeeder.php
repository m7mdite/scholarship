<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['country_name' => 'إيطاليا', 'country_rate' => 85],
            ['country_name' => 'ألمانيا', 'country_rate' => 90],
            ['country_name' => 'روسيا', 'country_rate' => 92],
            ['country_name' => 'رومانيا', 'country_rate' => 94],
            ['country_name' => 'هنغاريا', 'country_rate' => 88],
            ['country_name' => 'تركيا', 'country_rate' => 91],
            ['country_name' => 'ألمانيا', 'country_rate' => 98],
            ['country_name' => 'بريطانيا', 'country_rate' => 97],
            ['country_name' => 'الولايات المتحدة', 'country_rate' => 99],
            ['country_name' => 'كندا', 'country_rate' => 96],
            ['country_name' => 'المغرب', 'country_rate' => 87.3],
            ['country_name' => 'الجزائر', 'country_rate' => 85.0],
            ['country_name' => 'تركيا', 'country_rate' => 91.5],
            ['country_name' => 'ألمانيا', 'country_rate' => 98.2],
            ['country_name' => 'بريطانيا', 'country_rate' => 97.8],
            ['country_name' => 'الولايات المتحدة', 'country_rate' => 99.1],
            ['country_name' => 'فرنسا', 'country_rate' => 96.5],
            ['country_name' => 'كندا', 'country_rate' => 97.2],
            ['country_name' => 'أستراليا', 'country_rate' => 95.8],
            ['country_name' => 'ماليزيا', 'country_rate' => 89.4],
            ['country_name' => 'الصين', 'country_rate' => 88.9],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['country_name' => $country['country_name']],
                ['country_rate' => $country['country_rate']]
            );
        }
    }
}
