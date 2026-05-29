<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Scholarship;
use App\Models\Specialization;
use Illuminate\Http\Request;
use App\Http\Requests\StoreScholarshipRequest;
use App\Http\Requests\UpdateScholarshipRequest;
use Illuminate\Support\Facades\DB;
use Psy\Readline\Hoa\Console;

class ScholarshipController extends Controller
{
    // ======================================================================= // جلب جميع المنح (مع الصور والعلاقات)
    public function index()
    {
        $scholarships = Scholarship::with([
            'country',
            'city',
            'specialization',
            'category',
            'photos'
        ])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنح بنجاح',
            'data' => $scholarships
        ], 200);
    }
    // ======================================================================= جلب أول 15 منحة نشطة مع معلومات مختصرة (للصفحة الرئيسية)
    public function getTopScholarships(Request $request)
    {
        // $user = $request->user();
        $user = auth('sanctum')->user(); // بدلاً من $request->user()
        Log::info(get_class($user));
        $today = Carbon::today();
        Log::info('User ID: ' . ($user ? $user->id : 'null'));
        Log::info('User: ', [$user ? $user->id : 'null']);
        // \Log::info('Favorite count: ' . ($user ? $user->favoriteScholarships()->count() : 'no user'));
        // إذا كان المستخدم مسجلاً ولديه منح مفضلة
        if ($user && $user->favoriteScholarships()->count() > 0) {
            // echo "User ID: " . $user->id . "\n";
            // dd("User ID: " . $user->id);
            

            // الحصول على IDs التخصصات والدول والمدن والفئات من مفضلات المستخدم
            $favorites = $user->favoriteScholarships;
            $specializationIds = $favorites->pluck('specialization_id')->unique()->toArray();
            $countryIds = $favorites->pluck('country_id')->unique()->toArray();
            $cityIds = $favorites->pluck('city_id')->unique()->toArray();
            $categoryIds = $favorites->pluck('category_id')->unique()->toArray();

            // استعلام متقدم للمنح المشابهة (الأولوية للتخصص ثم الدولة ثم المدينة ثم الفئة)
            $similarScholarships = Scholarship::with(['city', 'specialization', 'photos'])
                ->where('finished_date', '>=', $today)
                // ->whereNotIn('id', $favorites->pluck('id')) // استبعاد المنح المفضلة نفسها
                ->where(function ($query) use ($specializationIds, $countryIds, $cityIds, $categoryIds) {
                    $query->whereIn('specialization_id', $specializationIds)
                        ->orWhereIn('country_id', $countryIds)
                        ->orWhereIn('city_id', $cityIds)
                        ->orWhereIn('category_id', $categoryIds);
                })
                ->orderByRaw("
                CASE 
                    WHEN specialization_id IN (" . implode(',', $specializationIds) . ") THEN 1
                    WHEN country_id IN (" . implode(',', $countryIds) . ") THEN 2
                    WHEN city_id IN (" . implode(',', $cityIds) . ") THEN 3
                    WHEN category_id IN (" . implode(',', $categoryIds) . ") THEN 4
                    ELSE 5
                END
            ")
                ->take(15)
                ->get();

            $scholarships = $similarScholarships->map(function ($scholarship) use ($today) {
                return $this->formatScholarshipForTop($scholarship, $today);
            });

            $message = 'تم جلب منح مقترحة بناءً على مفضلاتك';
        } else {
            // السلوك الأصلي: أحدث 15 منحة نشطة
            $scholarships = Scholarship::with(['city', 'specialization', 'photos'])
                ->where('finished_date', '>=', $today)
                ->orderBy('id', 'desc')
                ->take(15)
                ->get()
                ->map(function ($scholarship) use ($today) {
                    return $this->formatScholarshipForTop($scholarship, $today);
                });
            $message = 'تم جلب أول 15 منحة بنجاح';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'count' => $scholarships->count(),
            'data' => $scholarships
        ], 200);
    }
    // public function getTopScholarships()
    // {
    //     $today = Carbon::today();

    //     $scholarships = Scholarship::with(['city', 'specialization', 'photos'])
    //         ->where('finished_date', '>=', $today) // فقط المنح التي لم تنتهِ
    //         ->orderBy('id', 'desc') // أحدث المنح أولاً
    //         ->take(15)
    //         ->get()
    //         ->map(function ($scholarship) use ($today) {
    //             // حساب حالة تاريخ البدء
    //             $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;
    //             if ($startDate && $startDate->isFuture()) {
    //                 $startStatus = 'تبدأ في ' . $startDate->toDateString();
    //             } elseif ($startDate && $startDate->lte($today)) {
    //                 $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
    //                 // بما أن finished_date >= اليوم، فالباقي موجب
    //                 $startStatus = 'متبقي ' . $daysRemaining . ' يوم';
    //             } else {
    //                 $startStatus = 'تاريخ البدء غير محدد';
    //             }

    //             // الحصول على أول صورة إن وجدت
    //             $photoUrl = null;
    //             if ($scholarship->photos->isNotEmpty()) {
    //                 $photoPath = $scholarship->photos->first()->image_path;
    //                 // إذا كان المسار يبدأ بـ /storage/ أو كان متوقعاً
    //                 $photoUrl = url($photoPath); // أو asset($photoPath)
    //             }

    //             return [
    //                 'id' => $scholarship->id,
    //                 'scholarship_name' => $scholarship->scholarship_name,
    //                 'finance' => $scholarship->finance,
    //                 'degree' => $scholarship->degree,
    //                 'city_name' => $scholarship->city->city_name ?? null,
    //                 'specialization_name' => $scholarship->specialization->specialization_name ?? null,
    //                 'start_status' => $startStatus,
    //                 'photo_url' => $photoUrl,
    //             ];
    //         });

    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'تم جلب أول 15 منحة بنجاح',
    //         'data' => $scholarships
    //     ], 200);
    // }
    // =======================================================================  جلب المنح حسب الدولة (مع الصور والعلاقات)
    public function getByCountry($countryId)
    {

        $country = Country::find($countryId);
        if (!$country) {
            return response()->json([
                'status' => 'error',
                'message' => 'الدولة غير موجودة',
                'count' => 0,
                'data' => null
            ], 404);
        }
        $today = Carbon::today();
        $scholarships = Scholarship::with(['city', 'specialization', 'photos'])
            ->where('country_id', $countryId)
            ->where('finished_date', '>=', $today) // المنح التي لم تنتهِ
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($scholarship) use ($today) {
                // حساب حالة البدء (مثل getTopScholarships)
                $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;
                if ($startDate && $startDate->isFuture()) {
                    $startStatus = 'تبدأ في ' . $startDate->toDateString();
                } elseif ($startDate && $startDate->lte($today)) {
                    $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
                    $startStatus = $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
                } else {
                    $startStatus = 'تاريخ البدء غير محدد';
                }

                $photoUrl = null;
                if ($scholarship->photos->isNotEmpty()) {
                    $photoUrl = url($scholarship->photos->first()->image_path);
                }

                return [
                    'id' => $scholarship->id,
                    'scholarship_name' => $scholarship->scholarship_name,
                    'finance' => $scholarship->finance,
                    'degree' => $scholarship->degree,
                    'city_name' => $scholarship->city->city_name ?? null,
                    'specialization_name' => $scholarship->specialization->specialization_name ?? null,
                    'start_status' => $startStatus,
                    'photo_url' => $photoUrl,
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنح للدولة بنجاح',
            'count' => $scholarships->count(),
            'data' => $scholarships
        ], 200);
    }



    // ======================================================================= اضافة منحة جديدة (بدون صورة)
    // إضافة منحة جديدة (مع صورة اختيارية)
    public function store(StoreScholarshipRequest $request)
    {
        $validated = $request->validated();


        DB::beginTransaction();
        try {
            // إنشاء المنحة
            $scholarship = Scholarship::create($validated);

            // رفع الصورة إن وُجدت
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('scholarships', $fileName, 'public');
                $scholarship->photos()->create([
                    'image_path' => Storage::url($path),
                    'city_id' => $validated['city_id'] ?? null,
                ]);
            }

            DB::commit();
            // تحميل العلاقات لعرضها في الاستجابة
            $scholarship->load(['country', 'city', 'specialization', 'category', 'photos']);

            return response()->json([
                'status' => 'success',
                'message' => 'تمت إضافة المنحة بنجاح',
                'data' => $scholarship
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'فشل إضافة المنحة: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    // عرض منحة محددة
    public function show($id)
    {
        $scholarship = Scholarship::with([
            'country',
            'city',
            'specialization',
            'category',
            'photos',
            'howToApply',
            'applicationCriteria',
            'personalExperiences'
        ])->find($id);

        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنحة بنجاح',
            'data' => $scholarship
        ], 200);
    }

    // تحديث منحة
    public function update(UpdateScholarshipRequest $request, $id)
    {
        $scholarship = Scholarship::find($id);
        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة',
                'data' => null
            ], 404);
        }

        $validated = $request->validated();


        DB::beginTransaction();
        try {
            $scholarship->update($validated);

            // معالجة الصورة الجديدة (إن وجدت)
            if ($request->hasFile('photo')) {
                // حذف الصورة القديمة إن وجدت
                if ($scholarship->photos->isNotEmpty()) {
                    $oldPhoto = $scholarship->photos->first();
                    // حذف الملف الفعلي من التخزين
                    $oldPath = str_replace('/storage/', '', $oldPhoto->image_path);
                    Storage::disk('public')->delete($oldPath);
                    $oldPhoto->delete();
                }
                // رفع الصورة الجديدة
                $file = $request->file('photo');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('scholarships', $fileName, 'public');
                $scholarship->photos()->create([
                    'image_path' => Storage::url($path)
                ]);
            }

            DB::commit();
            $scholarship->load(['country', 'city', 'specialization', 'category', 'photos']);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث المنحة بنجاح',
                'data' => $scholarship
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'فشل تحديث المنحة: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    // حذف منحة
    public function destroy($id)
    {
        $scholarship = Scholarship::find($id);
        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة',
                'data' => null
            ], 404);
        }

        DB::beginTransaction();
        try {
            // حذف الصور المرتبطة من التخزين
            foreach ($scholarship->photos as $photo) {
                $path = str_replace('/storage/', '', $photo->image_path);
                Storage::disk('public')->delete($path);
            }
            // حذف المنحة (ستحذف الصور تلقائياً عبر cascade أو يدوياً)
            $scholarship->delete();
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'تم حذف المنحة بنجاح',
                'data' => null
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'فشل حذف المنحة: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }


    // ======================================================================== جلب منح مشابهة (حسب التخصص، ثم المدينة، ثم الدولة، ثم الفئة)
    public function getSimilarScholarships($id)
    {
        $scholarship = Scholarship::find($id);
        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة',
                'data' => null
            ], 404);
        }

        // 1. نفس التخصص
        $similar = Scholarship::where('specialization_id', $scholarship->specialization_id)
            ->where('id', '!=', $id)
            ->where('finished_date', '>=', now()) // لم تنتهِ
            ->limit(3)
            ->get();

        // 2. إذا كان العدد أقل من 3، أضف من نفس المدينة
        if ($similar->count() < 3) {
            $needed = 3 - $similar->count();
            $more = Scholarship::where('city_id', $scholarship->city_id)
                ->where('id', '!=', $id)
                ->whereNotIn('id', $similar->pluck('id'))
                ->where('finished_date', '>=', now())
                ->limit($needed)
                ->get();
            $similar = $similar->merge($more);
        }

        // 3. إذا كان العدد أقل من 3، أضف من نفس الدولة
        if ($similar->count() < 3) {
            $needed = 3 - $similar->count();
            $more = Scholarship::where('country_id', $scholarship->country_id)
                ->where('id', '!=', $id)
                ->whereNotIn('id', $similar->pluck('id'))
                ->where('finished_date', '>=', now())
                ->limit($needed)
                ->get();
            $similar = $similar->merge($more);
        }

        // 4. إذا كان العدد أقل من 3، أضف من نفس الفئة
        if ($similar->count() < 3) {
            $needed = 3 - $similar->count();
            $more = Scholarship::where('category_id', $scholarship->category_id)
                ->where('id', '!=', $id)
                ->whereNotIn('id', $similar->pluck('id'))
                ->where('finished_date', '>=', now())
                ->limit($needed)
                ->get();
            $similar = $similar->merge($more);
        }

        // تنسيق البيانات مثل getTopScholarships
        $today = \Carbon\Carbon::today();
        $data = $similar->map(function ($item) use ($today) {
            $startDate = $item->start_date ? \Carbon\Carbon::parse($item->start_date) : null;
            if ($startDate && $startDate->isFuture()) {
                $startStatus = 'تبدأ في ' . $startDate->toDateString();
            } elseif ($startDate && $startDate->lte($today)) {
                $daysRemaining = $today->diffInDays(\Carbon\Carbon::parse($item->finished_date), false);
                $startStatus = $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
            } else {
                $startStatus = 'تاريخ البدء غير محدد';
            }

            $photoUrl = $item->photos->isNotEmpty() ? url($item->photos->first()->image_path) : null;

            return [
                'id' => $item->id,
                'scholarship_name' => $item->scholarship_name,
                'finance' => $item->finance,
                'degree' => $item->degree,
                'city_name' => $item->city->city_name ?? null,
                'specialization_name' => $item->specialization->specialization_name ?? null,
                'start_status' => $startStatus,
                'photo_url' => $photoUrl,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنح المشابهة',
            'count' => $data->count(),
            'data' => $data
        ], 200);
    }

    // =====================================

    // (إضافي) جلب أول 15 منحة نشطة مع معلومات مختصرة (للصفحة الرئيسية)
    public function topScholarships()
    {
        $today = now()->toDateString();

        $scholarships = Scholarship::with(['city', 'specialization', 'photos'])
            ->where('finished_date', '>=', $today)
            ->orderBy('id', 'desc')
            ->take(15)
            ->get()
            ->map(function ($scholarship) {
                $photoUrl = $scholarship->photos->isNotEmpty() ? $scholarship->photos->first()->image_path : null;
                $startStatus = $this->getStartStatus($scholarship);
                return [
                    'id' => $scholarship->id,
                    'scholarship_name' => $scholarship->scholarship_name,
                    'finance' => $scholarship->finance,
                    'degree' => $scholarship->degree,
                    'city_name' => $scholarship->city->city_name ?? null,
                    'specialization_name' => $scholarship->specialization->specialization_name ?? null,
                    'start_status' => $startStatus,
                    'photo_url' => $photoUrl,
                ];
            });

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب أول 15 منحة بنجاح',
            'data' => $scholarships
        ], 200);
    }

    // دالة مساعدة لحساب حالة البدء
    private function getStartStatus($scholarship)
    {
        if (!$scholarship->start_date) return 'تاريخ البدء غير محدد';
        $startDate = \Carbon\Carbon::parse($scholarship->start_date);
        $today = \Carbon\Carbon::today();
        if ($startDate->isFuture()) {
            return 'تبدأ في ' . $startDate->toDateString();
        } elseif ($startDate->lte($today)) {
            $daysRemaining = $today->diffInDays(\Carbon\Carbon::parse($scholarship->finished_date), false);
            return $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
        }
        return 'تاريخ البدء غير محدد';
    }




    // دالة مساعدة لتنسيق المنحة بنفس شكل getTopScholarships الأصلي
    private function formatScholarshipForTop($scholarship, $today)
    {
        $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;
        if ($startDate && $startDate->isFuture()) {
            $startStatus = 'تبدأ في ' . $startDate->toDateString();
        } elseif ($startDate && $startDate->lte($today)) {
            $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
            $startStatus = $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
        } else {
            $startStatus = 'تاريخ البدء غير محدد';
        }

        $photoUrl = $scholarship->photos->isNotEmpty() ? url($scholarship->photos->first()->image_path) : null;

        return [
            'id' => $scholarship->id,
            'scholarship_name' => $scholarship->scholarship_name,
            'finance' => $scholarship->finance,
            'degree' => $scholarship->degree,
            'city_name' => $scholarship->city->city_name ?? null,
            'specialization_name' => $scholarship->specialization->specialization_name ?? null,
            'start_status' => $startStatus,
            'photo_url' => $photoUrl,
        ];
    }
}
