<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\City;
use App\Models\Specialization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PhotoSeeder extends Seeder
{
    public function run()
    {
        Storage::disk('public')->makeDirectory('photos');

        $cities = City::all();
        $specializations = Specialization::all();

        if ($cities->isEmpty() || $specializations->isEmpty()) {
            $this->command->error('لازم تشغّل CitySeeder و SpecializationSeeder قبل PhotoSeeder.');
            return;
        }

        // بنك الصور المصدرية
        $imagesDir = database_path('seeders/images');
        if (!is_dir($imagesDir)) {
            mkdir($imagesDir, 0755, true);
            $this->command->warn("المجلد {$imagesDir} غير موجود، تم إنشاؤه. ضع فيه صور مسمّاة 1.jpg إلى 20.jpg.");
        }

        $availableImages = [];
        for ($i = 1; $i <= 20; $i++) {
            if (file_exists($imagesDir . '/' . $i . '.jpg')) {
                $availableImages[] = $i . '.jpg';
            }
        }

        if (empty($availableImages)) {
            $this->command->error('لا توجد صور مصدرية بمجلد seeders/images، لا يمكن توليد بنك الصور.');
            return;
        }

        $created = 0;
        $skipped = 0;

        foreach ($cities as $city) {
            foreach ($specializations as $specialization) {

                // تفادي التكرار لو الـ Seeder اشتغل أكتر من مرة
                $exists = Photo::where('city_id', $city->id)
                    ->where('specialization_id', $specialization->id)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                $randomImage = $availableImages[array_rand($availableImages)];
                $sourcePath = $imagesDir . '/' . $randomImage;
                $newImageName = 'city' . $city->id . '_spec' . $specialization->id . '.jpg';
                $destPath = storage_path('app/public/photos/' . $newImageName);

                if (!file_exists($sourcePath)) {
                    continue;
                }

                copy($sourcePath, $destPath);

                Photo::create([
                    'image_path' => '/storage/photos/' . $newImageName,
                    'city_id' => $city->id,
                    'specialization_id' => $specialization->id,
                ]);

                $created++;
            }
        }

        $this->command->info("تم إنشاء {$created} صورة لكل توليفات (مدينة × تخصص)، وتخطي {$skipped} موجودة مسبقاً.");
    }
}