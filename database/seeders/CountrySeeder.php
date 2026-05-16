<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run()
    {
        $countries = [
            ['country_name' => 'سوريا', 'country_rate' => 85.5],
            ['country_name' => 'مصر', 'country_rate' => 90.0],
            ['country_name' => 'السعودية', 'country_rate' => 92.3],
            ['country_name' => 'الإمارات', 'country_rate' => 94.2],
            ['country_name' => 'الأردن', 'country_rate' => 88.7],
            ['country_name' => 'لبنان', 'country_rate' => 82.1],
            ['country_name' => 'فلسطين', 'country_rate' => 80.0],
            ['country_name' => 'العراق', 'country_rate' => 79.4],
            ['country_name' => 'تونس', 'country_rate' => 86.9],
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