<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Country;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run()
    {
        $citiesByCountry = [
            'إيطاليا' => ['روما', 'ميلانو', 'فلورنسا'],
            'ألمانيا' => ['برلين', 'ميونخ', 'فرانكفورت'],
            'رومانيا' => ['بوخارست', 'كلوج نابوكا'],
            'هنغاريا' => ['بودابست', 'ديبرتسن'],
            'روسيا' => ['موسكو', 'سان بطرسبرغ'],
            'تركيا' => ['إسطنبول', 'أنقرة', 'إزمير'],
        ];

        foreach ($citiesByCountry as $countryName => $cityNames) {
            $country = Country::firstOrCreate(
                ['country_name' => $countryName],
                ['country_rate' => 80.0]
            );

            foreach ($cityNames as $cityName) {
                City::firstOrCreate([
                    'city_name' => $cityName,
                    'country_id' => $country->id,
                ]);
            }
        }
    }
}