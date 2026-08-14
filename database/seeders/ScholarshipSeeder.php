<?php

namespace Database\Seeders;

use App\Models\Scholarship;
use App\Models\Review;
use App\Models\HowToApply;
use App\Models\Country;
use App\Models\City;
use App\Models\Specialization;
use Illuminate\Database\Seeder;
use App\Models\ApplicationCriteria;
use Faker\Factory as Faker;

class ScholarshipSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('ar_SA');

        $countries = Country::all();
        $cities = City::all();
        $specializations = Specialization::all();

        if ($countries->isEmpty() || $cities->isEmpty() || $specializations->isEmpty()) {
            $this->command->error('البيانات الأساسية غير موجودة. شغّل: php artisan db:seed --class=CountrySeeder ثم CitySeeder وCategorySeeder وSpecializationSeeder قبل هذا الـ Seeder.');
            return;
        }

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

        // كل درجة إلها مدة دراسة واقعية بالسنين (min, max)
        $degreeDurations = [
            'ثانوية عامة' => [1, 2],
            'بكالوريوس' => [3, 4],
            'ماجستير' => [1, 2],
            'دكتوراه' => [3, 4],
        ];

        for ($i = 0; $i < 20; $i++) {
            $country = $countries->random();

            // مدينة عشوائية ضمن نفس الدولة فعلياً (مش دايماً أول وحدة)
            $countryCities = $cities->where('country_id', $country->id);
            $city = $countryCities->isNotEmpty() ? $countryCities->random() : $cities->random();

            // التخصص أولاً، والفئة مشتقة منه مباشرة عشان تبقى متطابقة منطقياً
            $specialization = $specializations->random();
            $categoryId = $specialization->category_id;

            $degree = $faker->randomElement(array_keys($degreeDurations));
            $finance = $faker->randomElement(['ممولة بالكامل', 'ممولة جزئياً', 'غير ممولة']);

            $startDate = $faker->dateTimeBetween('now', '+6 months');

            [$minYears, $maxYears] = $degreeDurations[$degree];
            $durationMonths = $faker->numberBetween($minYears * 12, $maxYears * 12);
            $endDate = (clone $startDate)->modify("+{$durationMonths} months");

            $scholarship = Scholarship::create([
                'scholarship_name' => $scholarshipNames[$i],
                'degree' => $degree,
                'finance' => $finance,
                'scholarship_description' => $faker->paragraph(5),
                'donar' => $faker->company,
                'start_date' => $startDate->format('Y-m-d'),
                'finished_date' => $endDate->format('Y-m-d'),
                'scholarship_language' => $faker->randomElement(['عربي', 'إنجليزي', 'فرنسي', 'عربي/إنجليزي']),
                'scholarship_link' => $faker->url,
                'country_id' => $country->id,
                'city_id' => $city->id,
                'specialization_id' => $specialization->id,
                'category_id' => $categoryId,
            ]);

            if ($i % 3 != 0) {
                Review::create([
                    'scholarship_id' => $scholarship->id,
                    'reviewer_name' => $faker->name,
                    'review' => $faker->paragraph(2),
                    'rating' => $faker->numberBetween(3, 5),
                ]);
                if ($i % 2 == 0) {
                    Review::create([
                        'scholarship_id' => $scholarship->id,
                        'reviewer_name' => $faker->name,
                        'review' => $faker->paragraph(2),
                        'rating' => $faker->numberBetween(2, 5),
                    ]);
                }
            }

            if ($i % 2 == 0) {
                HowToApply::create([
                    'scholarship_id' => $scholarship->id,
                    'how_to_apply_description' => $faker->paragraph(3),
                ]);
            }

            if ($i % 3 != 0) {
                ApplicationCriteria::create([
                    'scholarship_id' => $scholarship->id,
                    'age' => $faker->optional(0.85)->randomElement([
                        'أقل من 25 سنة',
                        'من 18 إلى 30 سنة',
                        'أقل من 35 سنة',
                        'لا يوجد حد أقصى للعمر',
                    ]),
                    'gender' => $faker->optional(0.7)->randomElement([
                        'ذكر',
                        'أنثى',
                        'ذكر وأنثى',
                    ]),
                    'nationalities' => $faker->optional(0.8)->randomElement([
                        'جميع الجنسيات',
                        'الجنسيات العربية فقط',
                        'دول الاتحاد الأوروبي',
                        'جميع الجنسيات باستثناء جنسية الدولة المضيفة',
                    ]),
                ]);
            }
        }

        $this->command->info('تم إنشاء 20 منحة.');
    }
}