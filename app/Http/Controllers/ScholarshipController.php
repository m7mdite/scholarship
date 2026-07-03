<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\City;
use App\Models\User;
use App\Models\Country;
use App\Models\Scholarship;
use App\Models\Specialization;
use Illuminate\Http\Request;
use App\Http\Requests\StoreScholarshipRequest;
use App\Http\Requests\UpdateScholarshipRequest;
use Illuminate\Support\Facades\DB;
use Psy\Readline\Hoa\Console;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;

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


    // =====================================================================================================
    //    جلب المنح المميزة  مع دعم الفلاتر والاقتراحات الشخصية
    // =====================================================================================================
    public function getTopScholarships(Request $request)
    {

        /** @var \App\Models\User|null $user */
        $user = auth('sanctum')->user();
        $today = Carbon::today();

        // إعدادات pagination من الطلب
        $perPage = (int) $request->input('per_page', 15);  // عدد النتائج في الصفحة (افتراضي 15)
        $page = (int) $request->input('page', 1);

        // قراءة الفلاتر من الطلب
        $filters = [
            'country_id' => $request->input('country'),
            'category_id' => $request->input('category'),
            'degree' => $request->input('degree'),
            'finance' => $request->input('finance'),
        ];
        // إزالة الفلاتر الفارغة
        $filters = array_filter($filters, fn($v) => !is_null($v) && $v !== '' && $v !== 0);

        // إذا كان المستخدم مسجلاً ولديه مفضلات ولم يتم إرسال أي فلتر -> نستخدم المنح المقترحة
        $usePersonalized = ($user && $user->favoriteScholarships()->count() > 0 && empty($filters));

        if ($usePersonalized) {
            // نفس المنطق القديم: اقتراحات بناءً على مفضلات المستخدم
            $favorites = $user->favoriteScholarships;
            $specializationIds = $favorites->pluck('specialization_id')->unique()->toArray();
            $countryIds = $favorites->pluck('country_id')->unique()->toArray();
            $cityIds = $favorites->pluck('city_id')->unique()->toArray();
            $categoryIds = $favorites->pluck('category_id')->unique()->toArray();

            $query = Scholarship::with(['city', 'specialization', 'photos', 'country', 'category'])
                ->where('finished_date', '>=', $today)
                ->whereNotIn('id', $favorites->pluck('id'))
                ->where(function ($q) use ($specializationIds, $countryIds, $cityIds, $categoryIds) {
                    $q->whereIn('specialization_id', $specializationIds)
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
            ");
            $message = 'تم جلب منح مقترحة بناءً على مفضلاتك';
        } else {
            // المنح العادية (مع أو بدون فلاتر)
            $query = Scholarship::with(['city', 'specialization', 'photos', 'country', 'category'])
                ->where('finished_date', '>=', $today)
                ->orderBy('id', 'desc');

            // تطبيق الفلاتر إذا وجدت
            if (!empty($filters)) {
                foreach ($filters as $column => $value) {
                    if ($column === 'degree' || $column === 'finance') {
                        $query->where($column, $value);
                    } elseif ($column === 'country_id') {
                        $query->where('country_id', $value);
                    } elseif ($column === 'category_id') {
                        $query->where('category_id', $value);
                    }
                }
                $message = 'تم جلب المنح حسب الفلتر';
            } else {
                $message = 'تم جلب أحدث المنح';
            }
        }

        // تنفيذ paginate
        $scholarships = $query->paginate($perPage, ['*'], 'page', $page);

        // تنسيق البيانات كما في السابق (map)
        $formatted = $scholarships->getCollection()->map(function ($scholarship) use ($today) {
            // حساب start_status (نفس الكود القديم)
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
        });

        // إعادة بناء paginator بالبيانات المنسقة
        $paginatedData = new LengthAwarePaginator(
            $formatted,
            $scholarships->total(),
            $scholarships->perPage(),
            $scholarships->currentPage(),
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $paginatedData
        ], 200);
    }


    // ===========================================================================================================
    // جلب المنح حسب الدولة 
    // ========================================================================================================
    public function getByCountry(int $countryId)
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


    // ========================================================================================================
    // إضافة منحة جديدة
    // ===================================================================================================
    public function store(StoreScholarshipRequest $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
                'data' => null
            ], 403);
        }
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
            // إضافة review اختياري
            if ($request->filled('reviewer_name') && $request->filled('review')) {
                $scholarship->reviews()->create([
                    'reviewer_name' => $request->reviewer_name,
                    'review' => $request->review,
                    'rating' => $request->rating ?? null,
                ]);
            }
            // إضافة how_to_apply اختياري
            if ($request->filled('how_to_apply_description')) {
                $scholarship->howToApply()->create([
                    'how_to_apply_description' => $request->how_to_apply_description,
                ]);
            }
            // إضافة معايير التقديم (application criteria) إن وُجدت
            if ($request->has('application_criteria') && is_array($request->application_criteria)) {
                foreach ($request->application_criteria as $criteria) {
                    $scholarship->applicationCriteria()->create([
                        'requirment_type' => $criteria['requirment_type'],
                        'application_criteria_value' => $criteria['application_criteria_value'],
                        'application_criteria_description' => $criteria['application_criteria_description'] ?? null,
                    ]);
                }
            }
            $this->sendNotificationsToMatchingUsers($scholarship);

            DB::commit();
            // تحميل العلاقات لعرضها في الاستجابة
            $scholarship->load(['country', 'city', 'specialization', 'category', 'photos', 'reviews', 'howToApply', 'applicationCriteria']);

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

    // ==============================================================================================================================
    // عرض منحة محددة
    // =============================================================================================================================
    public function show(int $id)
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


    // =============================================================================================================================
    //                                                      تحديث منحة
    // =============================================================================================================================
    public function update(UpdateScholarshipRequest $request, int $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
                'data' => null
            ], 403);
        }
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
            $scholarship->load(['country', 'city', 'specialization', 'category', 'photos', 'applicationCriteria']);

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
    // ==============================================================================================================================
    //                                                   حذف منحة
    // =============================================================================================================================
    public function destroy(int $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
                'data' => null
            ], 403);
        }
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


    // ==============================================================================================
    // جلب منح مشابهة لمنحة معينة بناءً على التخصص، ثم المدينة، ثم الدولة، ثم الفئة
    // =====================================================================================
    public function getSimilarScholarships(int $id)
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
                $startStatus = $daysRemaining > 0 ? "تبقى {$daysRemaining} يوم" : 'انتهت الصلاحية';
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




    
    // ==========================================================================================
    // ارسال إشعارات للمستخدمين الذين لديهم تفضيلات مطابقة للمنحة
    // =========================================================================================
    private function sendNotificationsToMatchingUsers($scholarship)
    {
        // جلب جميع المستخدمين الذين لديهم تفضيلات
        $users = User::whereHas('preferences', function ($query) use ($scholarship) {
            $query->where(function ($q) use ($scholarship) {
                // مطابقة الاختصاص
                if ($scholarship->specialization_id) {
                    $q->orWhere('specialization_id', $scholarship->specialization_id);
                }
                // مطابقة الدولة
                if ($scholarship->country_id) {
                    $q->orWhere('country_id', $scholarship->country_id);
                }
                // مطابقة الدرجة
                if ($scholarship->degree) {
                    $q->orWhere('degree', $scholarship->degree);
                }
                // مطابقة الفئة (من خلال التخصص)
                if ($scholarship->category_id) {
                    $q->orWhereHas('specialization', function ($sq) use ($scholarship) {
                        $sq->where('category_id', $scholarship->category_id);
                    });
                }
            });
        })->with('preferences')->get();

        if ($users->isEmpty()) {
            return;
        }

        $sentCount = 0;
        foreach ($users as $user) {
            NotificationController::create(
                $user->id,
                'info',
                '📢 منحة جديدة تناسب اهتماماتك!',
                "تم إضافة منحة جديدة: {$scholarship->scholarship_name} في تخصص {$scholarship->specialization->specialization_name}",
                [
                    'scholarship_id' => $scholarship->id,
                    'scholarship_name' => $scholarship->scholarship_name,
                    'link' => '/scholarships/' . $scholarship->id,
                ]
            );
            $sentCount++;
        }

        // تسجيل عدد الإشعارات المرسلة (اختياري)
        Log::info("تم إرسال {$sentCount} إشعار للمستخدمين المطابقين للمنحة: {$scholarship->id}");
    }
}
