<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [


            ['category_name' => 'IT'],
            ['category_name' => 'إقتصاد'],
            ['category_name' => 'العمارة'],
            ['category_name' => 'اللغات الأجنبية'],
            ['category_name' => 'العلوم'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['category_name' => $category['category_name']]
            );
        }
    }
}