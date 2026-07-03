<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use App\Models\Photo;
use App\Models\Review;
use App\Models\HowToApply;
use App\Models\Country;
use App\Models\City;
use App\Models\Specialization;
use App\Models\Category;
use Illuminate\Database\Seeder;
use App\Models\ApplicationCriteria;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;

class ScholarshipSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA'); // لاستخدام بيانات عربية واقعية

        // التأكد من وجود مجلد الصور
        Storage::disk('public')->makeDirectory('scholarships');

        // التأكد من وجود بيانات كافية للدول والمدن والتخصصات والفئات
        // إذا كانت seeders أخرى قد أنشأتها، فهي موجودة. وإلا ننشئ بعضها هنا.
        $this->ensureBaseData();

        // الحصول على جميع الدول والمدن والتخصصات والفئات من قاعدة البيانات
        $countries = Country::all();
        $cities = City::all();
        $specializations = Specialization::all();
        $categories = Category::all();

        if ($countries->isEmpty() || $cities->isEmpty() || $specializations->isEmpty() || $categories->isEmpty()) {
            $this->command->error('البيانات الأساسية (دول، مدن، تخصصات، فئات) غير موجودة. قم بتشغيل Seeders الخاصة بها أولاً.');
            return;
        }

        // قائمة بأسماء منح حقيقية (20 منحة)
        $scholarshipNames = [
            'منحة الحكومة التركية للدراسة في تركيا',
            'منحة تشيفنينغ البريطانية',
            'منحة DAAD الألمانية',
            'منحة إيراسموس موندوس الأوروبية',
            'منحة جامعة الملك عبد العزيز',
            'منحة جامعة الشارقة',
            'منحة معهد مصطفى (MBZUAI)',
            'منحة حكومة اليابان (MEXT)',
            'منحة جامعة سنغافورة الوطنية (NUS)',
            'منحة جامعة هارفارد للطلاب الدوليين',
            'منحة جامعة أوكسفورد (Clarendon)',
            'منحة جامعة كامبريدج (Gates)',
            'منحة جامعة ستانفورد (Knight-Hennessy)',
            'منحة معهد ماساتشوستس للتكنولوجيا (MIT)',
            'منحة جامعة طوكيو للدراسات العليا',
            'منحة جامعة ملبورن الدولية',
            'منحة جامعة بريتش كولومبيا (UBC)',
            'منحة جامعة زيورخ السويسرية',
            'منحة جامعة هلسنكي (Finland Scholarships)',
            'منحة جامعة أوبسالا (Sweden)'
        ];

        // إذا كان لديك صور من 1.jpg إلى 20.jpg، تأكد من وجودها في database/seeders/images/
        $imagesDir = database_path('seeders/images');
        if (!is_dir($imagesDir)) {
            mkdir($imagesDir, 0755, true);
            $this->command->warn("المجلد {$imagesDir} غير موجود، تم إنشاؤه. الرجاء وضع الصور (1.jpg إلى 20.jpg) فيه.");
        }

        $availableImages = [];
        for ($i = 1; $i <= 20; $i++) {
            if (file_exists($imagesDir . '/' . $i . '.jpg')) {
                $availableImages[] = $i . '.jpg';
            }
        }

        if (count($availableImages) < 20) {
            $this->command->warn("يوجد فقط " . count($availableImages) . " صورة من أصل 20. سيتم استخدام المتاح فقط.");
        }

        for ($i = 0; $i < 20; $i++) {
            // اختيار عناصر عشوائية من الجداول الأساسية
            $country = $countries->random();
            // اختيار مدينة تابعة للدولة المختارة (إن وجدت) وإلا أي مدينة
            $city = $cities->where('country_id', $country->id)->first() ?? $cities->random();
            $specialization = $specializations->random();
            $category = $categories->random();

            $degree = $faker->randomElement(['بكالوريوس', 'ثانوية عامة', 'ماجستير', 'دكتوراه']);
            $finance = $faker->randomElement(['ممولة بالكامل', 'ممولة جزئياً', 'غير ممولة']);
            $startDate = $faker->dateTimeBetween('now', '+6 months')->format('Y-m-d');
            $endDate = $faker->dateTimeBetween('+6 months', '+2 years')->format('Y-m-d');

            $scholarship = Scholarship::create([
                'scholarship_name' => $scholarshipNames[$i],
                'degree' => $degree,
                'finance' => $finance,
                'scholarship_description' => $faker->paragraph(5),
                'donar' => $faker->company,
                'start_date' => $startDate,
                'finished_date' => $endDate,
                'scholarship_language' => $faker->randomElement(['عربي', 'إنجليزي', 'فرنسي', 'عربي/إنجليزي']),
                'scholarship_link' => $faker->url,
                'country_id' => $country->id,
                'city_id' => $city->id,
                'specialization_id' => $specialization->id,
                'category_id' => $category->id,
            ]);

            // إضافة صورة (اختيار عشوائي من الصور المتاحة)
            if (!empty($availableImages)) {
                $randomImage = $availableImages[array_rand($availableImages)];
                $sourcePath = $imagesDir . '/' . $randomImage;
                $newImageName = 'scholarship_' . $scholarship->id . '.jpg';
                $destPath = storage_path('app/public/scholarships/' . $newImageName);

                if (file_exists($sourcePath)) {
                    copy($sourcePath, $destPath);
                    Photo::create([
                        'scholarship_id' => $scholarship->id,
                        'image_path' => '/storage/scholarships/' . $newImageName,
                        'city_id' => $city->id,
                    ]);
                }
            }

            // إضافة مراجعة (review) لـ 60% من المنح
            if ($i % 3 != 0) { // عشوائياً: 2 من كل 3 منح تقريباً
                Review::create([
                    'scholarship_id' => $scholarship->id,
                    'reviewer_name' => $faker->name,
                    'review' => $faker->paragraph(2),
                    'rating' => $faker->numberBetween(3, 5),
                ]);
                // يمكن إضافة مراجعة ثانية أحياناً
                if ($i % 2 == 0) {
                    Review::create([
                        'scholarship_id' => $scholarship->id,
                        'reviewer_name' => $faker->name,
                        'review' => $faker->paragraph(2),
                        'rating' => $faker->numberBetween(2, 5),
                    ]);
                }
            }

            // إضافة how_to_apply لـ 50% من المنح
            if ($i % 2 == 0) {
                HowToApply::create([
                    'scholarship_id' => $scholarship->id,
                    'how_to_apply_description' => $faker->paragraph(3),
                ]);
            }
            if ($i % 3 != 0) {
                $numCriteria = rand(1, 3);
                for ($c = 0; $c < $numCriteria; $c++) {
                    ApplicationCriteria::create([
                        'scholarship_id' => $scholarship->id,
                        'requirment_type' => $faker->randomElement(['المعدل التراكمي', 'الخبرة', 'شهادة لغة', 'العمر', 'خطابات توصية']),
                        'application_criteria_value' => $faker->randomElement(['80%', 'سنتان', 'IELTS 6.5', 'أقل من 30 سنة', '3 رسائل']),
                        'application_criteria_description' => $faker->optional(0.8)->sentence??'',
                    ]);
                }
            }
        }

        $this->command->info('تم إنشاء 20 منحة حقيقية مع صور ومراجعات وكيفية تقديم ومعايير تقديم.');
    }

    private function ensureBaseData()
    {
        // إذا كانت الجداول فارغة، نقوم بإنشاء بيانات أساسية بسيطة
        if (Country::count() == 0) {
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
            ];
            foreach ($countries as $c) {
                Country::create($c);
            }
        }

        if (City::count() == 0) {
            $cities = [
                ['city_name' => 'دمشق', 'country_id' => 1],
                ['city_name' => 'حلب', 'country_id' => 1],
                ['city_name' => 'القاهرة', 'country_id' => 2],
                ['city_name' => 'الإسكندرية', 'country_id' => 2],
                ['city_name' => 'الرياض', 'country_id' => 3],
                ['city_name' => 'جدة', 'country_id' => 3],
                ['city_name' => 'دبي', 'country_id' => 4],
                ['city_name' => 'أبو ظبي', 'country_id' => 4],
                ['city_name' => 'عمان', 'country_id' => 5],
                ['city_name' => 'إسطنبول', 'country_id' => 6],
                ['city_name' => 'برلين', 'country_id' => 7],
                ['city_name' => 'لندن', 'country_id' => 8],
                ['city_name' => 'نيويورك', 'country_id' => 9],
                ['city_name' => 'تورونتو', 'country_id' => 10],
            ];
            foreach ($cities as $ci) {
                City::create($ci);
            }
        }

        if (Category::count() == 0) {
            $categories = [
                'تكنولوجيا المعلومات',
                'الهندسة',
                'الطب والعلوم الصحية',
                'العلوم الطبيعية',
                'العلوم الإنسانية',
                'الاقتصاد والعلوم الإدارية',
                'القانون',
                'الفنون والتصميم',
                'الزراعة',
                'التربية والتعليم',
            ];
            foreach ($categories as $cat) {
                Category::create(['category_name' => $cat]);
            }
        }

        if (Specialization::count() == 0) {
            // افتراض أن أول فئة هي "تكنولوجيا المعلومات" (id=1)
            $techCat = Category::where('category_name', 'تكنولوجيا المعلومات')->first();
            if ($techCat) {
                $specs = [
                    'علوم الحاسب',
                    'هندسة البرمجيات',
                    'أمن المعلومات',
                    'الذكاء الاصطناعي',
                    'علوم البيانات',
                ];
                foreach ($specs as $spec) {
                    Specialization::create(['specialization_name' => $spec, 'category_id' => $techCat->id]);
                }
            }
            // أضف تخصصات أخرى لفئات أخرى حسب الحاجة
            $engCat = Category::where('category_name', 'الهندسة')->first();
            if ($engCat) {
                Specialization::create(['specialization_name' => 'الهندسة المدنية', 'category_id' => $engCat->id]);
                Specialization::create(['specialization_name' => 'الهندسة المعمارية', 'category_id' => $engCat->id]);
            }
        }
    }
}
