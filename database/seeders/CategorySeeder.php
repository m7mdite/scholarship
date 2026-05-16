<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['category_name' => 'تكنولوجيا المعلومات'],
            ['category_name' => 'الهندسة'],
            ['category_name' => 'الطب والعلوم الصحية'],
            ['category_name' => 'العلوم الطبيعية'],
            ['category_name' => 'العلوم الإنسانية'],
            ['category_name' => 'الاقتصاد والعلوم الإدارية'],
            ['category_name' => 'القانون'],
            ['category_name' => 'الفنون والتصميم'],
            ['category_name' => 'الزراعة'],
            ['category_name' => 'التربية والتعليم'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['category_name' => $category['category_name']]
            );
        }
    }
}