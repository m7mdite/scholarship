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
            ['country_name' => 'رمانيا', 'country_rate' => 92],
            ['country_name' => 'هنغاريا', 'country_rate' => 94],
            ['country_name' => 'روسيا', 'country_rate' => 88],
            ['country_name' => 'تركيا', 'country_rate' => 91],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['country_name' => $country['country_name']],
                ['country_rate' => $country['country_rate']]
            );
        }
    }
}
