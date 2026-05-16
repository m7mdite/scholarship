<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use App\Models\Photo;
use App\Models\Country;
use App\Models\City;
use App\Models\Specialization;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ScholarshipSeeder extends Seeder
{
    public function run()
    {
        // التأكد من وجود مجلد الصور الأساسي للتخزين
        Storage::disk('public')->makeDirectory('scholarships');

        // إنشاء بيانات أساسية (يمكنك تعديلها حسب الحاجة)
        $country = Country::firstOrCreate(['country_name' => 'سوريا'], ['country_rate' => 85]);
        $city = City::firstOrCreate(['city_name' => 'دمشق', 'country_id' => $country->id]);
        $specialization = Specialization::firstOrCreate(['specialization_name' => 'علوم الحاسب'], ['category_id' => 1]);
        $category = Category::firstOrCreate(['category_name' => 'تكنولوجيا']);

        // مصفوفة بأسماء الصور المتاحة
        $sampleImages = [];
        for ($i = 1; $i <= 10; $i++) {
            $sampleImages[] = $i . '.jpg';
        }

        for ($i = 1; $i <= 10; $i++) {
            // إنشاء منحة جديدة
            $scholarship = Scholarship::create([
                'scholarship_name' => "منحة تجريبية $i",
                'degree' => collect(['بكالوريوس', 'ماجستير', 'دكتوراه'])->random(),
                'finance' => collect(['ممولة بالكامل', 'جزئية', 'بدون'])->random(),
                'scholarship_description' => "وصف المنحة التجريبية رقم $i.",
                'donar' => "جامعة $i",
                'start_date' => now()->addDays(rand(10, 100)),
                'finished_date' => now()->addDays(rand(200, 400)),
                'scholarship_language' => 'عربي/إنجليزي',
                'scholarship_link' => 'https://example.com',
                'country_id' => $country->id,
                'city_id' => $city->id,
                'specialization_id' => $specialization->id,
                'category_id' => $category->id,
            ]);

            // ربط صورة وهمية بالمنحة (استخدم صورة من المجلد sample-images)
            // سنختار صورة عشوائية من بين الـ 10 صور
            $randomImage = $sampleImages[array_rand($sampleImages)];
            $sourcePath = storage_path('app/public/sample-images/' . $randomImage);
            $newImageName = 'scholarship_' . $scholarship->id . '.jpg';
            $destPath = storage_path('app/public/scholarships/' . $newImageName);

            // نسخ الصورة فقط إذا كان الملف المصدر موجوداً
            if (file_exists($sourcePath)) {
                copy($sourcePath, $destPath);
                Photo::create([
                    'scholarship_id' => $scholarship->id,
                    'image_path' => '/storage/scholarships/' . $newImageName,
                    'city_id' => $city->id,
                ]);
            } else {
                $this->command->warn("الصورة {$randomImage} غير موجودة، لم يتم إضافة صورة للمنحة {$scholarship->id}");
            }
        }
    }
}