<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Specialization;
use Illuminate\Database\Seeder;

class SpecializationSeeder extends Seeder
{
    public function run()
    {
        // تعريف التخصصات لكل فئة (اسم الفئة => مصفوفة أسماء التخصصات)
        $specializationsByCategory = [
            'IT' => [
                'علوم الحاسب',
                'هندسة البرمجيات',
                'أمن المعلومات',
                'الذكاء الاصطناعي',
                'علوم البيانات',
                'الشبكات',
            ],
            'إقتصاد' => [
                'إدارة الأعمال',
                'المحاسبة',
                'الاقتصاد',
                'التسويق',
            ],
            'العمارة' => [
                'الهندسة المعمارية',
                'التصميم الداخلي',
            ],
            'اللغات الأجنبية' => [
                'اللغة الإنجليزية',
                'اللغة الفرنسية',
                'اللغة الألمانية',
            ],
            'العلوم' => [
                'الفيزياء',
                'الكيمياء',
                'الأحياء',
                'الرياضيات',
            ],
        ];

        foreach ($specializationsByCategory as $categoryName => $specializationNames) {
            // العثور على الفئة باستخدام اسمها
            $category = Category::where('category_name', $categoryName)->first();
            
            if (!$category) {
                $this->command->warn("الفئة {$categoryName} غير موجودة، يتم تخطي تخصصاتها.");
                continue;
            }

            foreach ($specializationNames as $specName) {
                Specialization::firstOrCreate([
                    'specialization_name' => $specName,
                    'category_id' => $category->id,
                ]);
            }
        }

        $this->command->info('تم إضافة التخصصات بنجاح.');
    }
}