<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\City;
use App\Models\User;
use App\Models\Country;
use App\Models\Photo;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use App\Http\Requests\StoreScholarshipRequest;
use App\Http\Requests\UpdateScholarshipRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ScholarshipController extends Controller
{
    /**
     * صورة افتراضية تُستخدم إذا ما لقينا صورة مطابقة بنفس المدينة والتخصص
     */
    private const DEFAULT_PHOTO_PATH = '/storage/scholarships/default.jpg';

    // =======================================================================
    // Helper بسيط: يجيب كل صور "بنك الصور" مرة وحدة، ويبنيلها Map
    // بمفتاح city_id-specialization_id عشان نتفادى استعلام لكل منحة (N+1)
    // =======================================================================
    private function photosMap(): Collection
    {
        return Photo::all()->keyBy(fn($p) => $p->city_id . '-' . $p->specialization_id);
    }
// =====================================================================================
    // يرجع رابط الصورة لمنحة وحدة اعتماداً على Map جاهزة (أو يبنيها إذا ما انبعتت)
    private function photoUrlFor($scholarship, ?Collection $map = null): string
    {
        $map = $map ?? $this->photosMap();
        $key = $scholarship->city_id . '-' . $scholarship->specialization_id;
        $photo = $map->get($key);

        return $photo ? url($photo->image_path) : url(self::DEFAULT_PHOTO_PATH);
    }
// ================================================================================
    private function calculateStartStatus($scholarship, Carbon $today): string
    {
        $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;

        if ($startDate && $startDate->isFuture()) {
            return 'تبدأ في ' . $startDate->toDateString();
        } elseif ($startDate && $startDate->lte($today)) {
            $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
            return $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
        }

        return 'تاريخ البدء غير محدد';
    }

    // ======================================================================= // جلب جميع المنح (مع الصور والعلاقات)
    public function index()
    {
        $scholarships = Scholarship::with([
            'country',
            'city',
            'specialization',
            'category',
        ])->get();

        $map = $this->photosMap();
        $scholarships->each(function ($s) use ($map) {
            $s->photo_url = $this->photoUrlFor($s, $map);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنح بنجاح',
            'data' => $scholarships
        ], 200);
    }

    public function getTopScholarships(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = auth('sanctum')->user();
        $today = Carbon::today();
        $perPage = (int) $request->input('per_page', 15);
        $page = (int) $request->input('page', 1);
        $filters = [
            'country_id' => $request->input('country'),
            'category_id' => $request->input('category'),
            'degree' => $request->input('degree'),
            'specialization_id' => $request->input('specialization'),
            'finance' => $request->input('finance'),
        ];
        $filters = array_filter($filters, fn($v) => !is_null($v) && $v !== '' && $v !== 0);
        $usePersonalized = ($user && $user->favoriteScholarships()->count() > 0 && empty($filters));

        $favoriteIds = $user ? $user->favoriteScholarships()->pluck('scholarships.id')->toArray() : [];

        if ($usePersonalized) {
            $favorites = $user->favoriteScholarships;
            $specializationIds = $favorites->pluck('specialization_id')->filter()->unique()->values()->toArray();
            $countryIds = $favorites->pluck('country_id')->filter()->unique()->values()->toArray();
            $cityIds = $favorites->pluck('city_id')->filter()->unique()->values()->toArray();
            $categoryIds = $favorites->pluck('category_id')->filter()->unique()->values()->toArray();

            $buildIn = function (array $ids, array &$bindings) {
                if (empty($ids)) {
                    return '-1';
                }
                $bindings = array_merge($bindings, $ids);
                return implode(',', array_fill(0, count($ids), '?'));
            };

            $bindings = [];
            $specSql = $buildIn($specializationIds, $bindings);
            $countrySql = $buildIn($countryIds, $bindings);
            $citySql = $buildIn($cityIds, $bindings);
            $categorySql = $buildIn($categoryIds, $bindings);

            $query = Scholarship::with(['city', 'specialization', 'country', 'category'])
                ->where('finished_date', '>=', $today)
                ->orderByRaw("
                CASE
                    WHEN specialization_id IN ($specSql) THEN 1
                    WHEN country_id IN ($countrySql) THEN 2
                    WHEN city_id IN ($citySql) THEN 3
                    WHEN category_id IN ($categorySql) THEN 4
                    ELSE 5
                END
            ", $bindings)
                ->orderBy('id', 'desc');

            $message = 'تم جلب منح مقترحة بناءً على مفضلاتك';
        } else {
            $query = Scholarship::with(['city', 'specialization', 'country', 'category'])
                ->where('finished_date', '>=', $today)
                ->orderBy('id', 'desc');
            if (!empty($filters)) {
                foreach ($filters as $column => $value) {
                    if ($column === 'degree' || $column === 'finance') {
                        $query->where($column, $value);
                    } elseif ($column === 'country_id') {
                        $query->where('country_id', $value);
                    } elseif ($column === 'category_id') {
                        $query->where('category_id', $value);
                    } elseif ($column === 'specialization_id') {
                        $query->where('specialization_id', $value);
                    }
                }
                $message = 'تم جلب المنح حسب الفلتر';
            } else {
                $message = 'تم جلب أحدث المنح';
            }
        }

        $scholarships = $query->paginate($perPage, ['*'], 'page', $page);

        $map = $this->photosMap();

        $formatted = $scholarships->getCollection()->map(function ($scholarship) use ($today, $user, $favoriteIds, $map) {
            $startStatus = $this->calculateStartStatus($scholarship, $today);

            $result = [
                'id' => $scholarship->id,
                'scholarship_name' => $scholarship->scholarship_name,
                'finance' => $scholarship->finance,
                'degree' => $scholarship->degree,
                'city_name' => $scholarship->city->city_name ?? null,
                'specialization_name' => $scholarship->specialization->specialization_name ?? null,
                'start_status' => $startStatus,
                'photo_url' => $this->photoUrlFor($scholarship, $map),
            ];

            if ($user) {
                $result['is_favorite'] = in_array($scholarship->id, $favoriteIds);
            }

            return $result;
        });

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
        $scholarships = Scholarship::with(['city', 'specialization'])
            ->where('country_id', $countryId)
            ->where('finished_date', '>=', $today)
            ->orderBy('id', 'desc')
            ->get();

        $map = $this->photosMap();

        $formatted = $scholarships->map(function ($scholarship) use ($today, $map) {
            $startStatus = $this->calculateStartStatus($scholarship, $today);

            return [
                'id' => $scholarship->id,
                'scholarship_name' => $scholarship->scholarship_name,
                'finance' => $scholarship->finance,
                'degree' => $scholarship->degree,
                'city_name' => $scholarship->city->city_name ?? null,
                'specialization_name' => $scholarship->specialization->specialization_name ?? null,
                'start_status' => $startStatus,
                'photo_url' => $this->photoUrlFor($scholarship, $map),
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنح للدولة بنجاح',
            'count' => $formatted->count(),
            'data' => $formatted
        ], 200);
    }

    // ========================================================================================================
    // إضافة منحة جديدة
    // ملاحظة: ما في رفع صورة هون، الصورة بتنجاب تلقائياً وقت العرض من بنك الصور
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
            $scholarship = Scholarship::create($validated);

            if ($request->filled('reviewer_name') && $request->filled('review')) {
                $scholarship->reviews()->create([
                    'reviewer_name' => $request->reviewer_name,
                    'review' => $request->review,
                    'rating' => $request->rating ?? null,
                ]);
            }
            if ($request->filled('how_to_apply_description')) {
                $scholarship->howToApply()->create([
                    'how_to_apply_description' => $request->how_to_apply_description,
                ]);
            }
            if ($request->filled('application_criteria')) {
                $criteria = $request->input('application_criteria');
                $scholarship->applicationCriteria()->create([
                    'age' => $criteria['age'] ?? null,
                    'gender' => $criteria['gender'] ?? null,
                    'nationalities' => $criteria['nationalities'] ?? null,
                ]);
            }

            $notificationsSent = true;
            try {
                $this->sendNotificationsToMatchingUsers($scholarship);
            } catch (\Throwable $notifyException) {
                $notificationsSent = false;
                Log::error('فشل إرسال إشعارات المنحة الجديدة (id: ' . $scholarship->id . '): ' . $notifyException->getMessage());
            }

            DB::commit();
            $scholarship->load(['country', 'city', 'specialization', 'category', 'reviews', 'howToApply', 'applicationCriteria']);
            $scholarship->photo_url = $this->photoUrlFor($scholarship);

            return response()->json([
                'status' => 'success',
                'message' => $notificationsSent
                    ? 'تمت إضافة المنحة بنجاح'
                    : 'تمت إضافة المنحة بنجاح، لكن تعذّر إرسال إشعارات للمستخدمين',
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
            'reviews',
            'specialization',
            'category',
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

        $scholarship->photo_url = $this->photoUrlFor($scholarship);
        /** @var \App\Models\User|null $user */
    $user = auth('sanctum')->user();
    if ($user) {
        $scholarship->is_favorite = $user->favoriteScholarships()
            ->where('scholarships.id', $scholarship->id)
            ->exists();
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
            if ($request->filled('application_criteria')) {
                $criteria = $request->input('application_criteria');
                $scholarship->applicationCriteria()->updateOrCreate(
                    ['scholarship_id' => $scholarship->id],
                    [
                        'age' => $criteria['age'] ?? null,
                        'gender' => $criteria['gender'] ?? null,
                        'nationalities' => $criteria['nationalities'] ?? null,
                    ]
                );
            }

            DB::commit();
            $scholarship->load(['country', 'city', 'specialization', 'category', 'applicationCriteria']);
            $scholarship->photo_url = $this->photoUrlFor($scholarship);

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

    /** @var \App\Models\User|null $user */
    $user = auth('sanctum')->user();
    $favoriteIds = $user ? $user->favoriteScholarships()->pluck('scholarships.id')->toArray() : [];

    $similar = Scholarship::where('specialization_id', $scholarship->specialization_id)
        ->where('id', '!=', $id)
        ->where('finished_date', '>=', now())
        ->limit(3)
        ->get();

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

    $similar->load(['city', 'specialization']);

    $today = Carbon::today();
    $map = $this->photosMap();

    $data = $similar->map(function ($item) use ($today, $map, $user, $favoriteIds) {
        $startStatus = $this->calculateStartStatus($item, $today);

        $result = [
            'id' => $item->id,
            'scholarship_name' => $item->scholarship_name,
            'finance' => $item->finance,
            'degree' => $item->degree,
            'city_name' => $item->city->city_name ?? null,
            'specialization_name' => $item->specialization->specialization_name ?? null,
            'start_status' => $startStatus,
            'photo_url' => $this->photoUrlFor($item, $map),
        ];

        if ($user) {
            $result['is_favorite'] = in_array($item->id, $favoriteIds);
        }

        return $result;
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
        $users = User::all();

        if ($users->isEmpty()) {
            return;
        }

        $sentCount = 0;
        foreach ($users as $user) {
            NotificationController::create(
                $user->id,
                'info',
                '📢 منحة جديدة متاحة!',
                // "",
                "تم إضافة منحة جديدة: {$scholarship->scholarship_name} في تخصص {$scholarship->specialization->specialization_name}",
                [
                    'scholarship_id' => $scholarship->id,
                    'country_name' => $scholarship->country->country_name ?? null,
                    'specialization_name' => $scholarship->specialization->specialization_name ?? null,
                    'scholarship_name' => $scholarship->scholarship_name,
                    'link' => '/scholarships/' . $scholarship->id,
                ]
            );
            $sentCount++;
        }

        Log::info("تم إرسال {$sentCount} إشعار لجميع المستخدمين عن المنحة الجديدة: {$scholarship->id}");
    }
}
// namespace App\Http\Controllers;

// use Carbon\Carbon;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Log;
// use App\Models\Category;
// use App\Models\City;
// use App\Models\User;
// use App\Models\Country;
// use App\Models\Photo;
// use App\Models\Scholarship;
// use App\Models\Specialization;
// use Illuminate\Http\Request;
// use App\Http\Requests\StoreScholarshipRequest;
// use App\Http\Requests\UpdateScholarshipRequest;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Pagination\LengthAwarePaginator;
// use Illuminate\Support\Collection;

// class ScholarshipController extends Controller
// {
//     /**
//      * مسار الصورة الافتراضية (placeholder) تستخدم لما ما نلاقي صورة مطابقة
//      * لا للتخصص ولا للمدينة. عدّل المسار حسب مكان تخزينك الفعلي.
//      */
//     private const DEFAULT_PHOTO_PATH = '/storage/scholarships/default.jpg';

//     // =======================================================================
//     // Helper: يجيب صور لمجموعة منح دفعة وحدة (بدون N+1)
//     // الأولوية: تطابق (city_id + specialization_id) بالضبط
//     // ثم fallback: نفس specialization_id بس (أي مدينة)
//     // ثم fallback أخير: صورة افتراضية
//     // بيرجع Collection: [scholarship_id => photo_url]
//     // =======================================================================
//     private function resolvePhotosForScholarships(Collection $scholarships): Collection
//     {
//         if ($scholarships->isEmpty()) {
//             return collect();
//         }

//         $uniquePairs = $scholarships
//             ->map(fn($s) => ['city_id' => $s->city_id, 'specialization_id' => $s->specialization_id])
//             ->unique(fn($p) => $p['city_id'] . '-' . $p['specialization_id'])
//             ->values();

//         // 1) تطابق دقيق (مدينة + تخصص) بـ query واحد
//         $exactPhotos = Photo::where(function ($query) use ($uniquePairs) {
//             foreach ($uniquePairs as $pair) {
//                 $query->orWhere(function ($q) use ($pair) {
//                     $q->where('city_id', $pair['city_id'])
//                       ->where('specialization_id', $pair['specialization_id']);
//                 });
//             }
//         })->get()->groupBy(fn($p) => $p->city_id . '-' . $p->specialization_id);

//         // 2) fallback: نفس التخصص بس (لأي منحة ما لقيناها بالخطوة الأولى)
//         $specializationIds = $scholarships->pluck('specialization_id')->filter()->unique()->values();
//         $specPhotos = $specializationIds->isNotEmpty()
//             ? Photo::whereIn('specialization_id', $specializationIds)->get()->groupBy('specialization_id')
//             : collect();

//         return $scholarships->mapWithKeys(function ($s) use ($exactPhotos, $specPhotos) {
//             $exactKey = $s->city_id . '-' . $s->specialization_id;

//             $photo = $exactPhotos->get($exactKey)?->first()
//                 ?? $specPhotos->get($s->specialization_id)?->first();

//             $url = $photo ? url($photo->image_path) : url(self::DEFAULT_PHOTO_PATH);

//             return [$s->id => $url];
//         });
//     }
// // ==============================================================================
//     // Helper لمنحة وحدة (بيستخدم نفس منطق الـ batch عشان نضمن نفس الأولوية بكل مكان)
//     private function resolvePhotoForScholarship($scholarship): string
//     {
//         return $this->resolvePhotosForScholarships(collect([$scholarship]))->first();
//     }

//     private function calculateStartStatus($scholarship, Carbon $today): string
//     {
//         $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;

//         if ($startDate && $startDate->isFuture()) {
//             return 'تبدأ في ' . $startDate->toDateString();
//         } elseif ($startDate && $startDate->lte($today)) {
//             $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
//             return $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
//         }

//         return 'تاريخ البدء غير محدد';
//     }

//     // ======================================================================= // جلب جميع المنح (مع الصور والعلاقات)
//     public function index()
//     {
//         $scholarships = Scholarship::with([
//             'country',
//             'city',
//             'specialization',
//             'category',
//         ])->get();

//         $photoMap = $this->resolvePhotosForScholarships($scholarships);

//         $scholarships->each(function ($s) use ($photoMap) {
//             $s->photo_url = $photoMap->get($s->id);
//         });

//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم جلب المنح بنجاح',
//             'data' => $scholarships
//         ], 200);
//     }



//     public function getTopScholarships(Request $request)
//     {
//         /** @var \App\Models\User|null $user */
//         $user = auth('sanctum')->user();
//         $today = Carbon::today();
//         $perPage = (int) $request->input('per_page', 15);
//         $page = (int) $request->input('page', 1);
//         $filters = [
//             'country_id' => $request->input('country'),
//             'category_id' => $request->input('category'),
//             'degree' => $request->input('degree'),
//             'specialization_id' => $request->input('specialization'),
//             'finance' => $request->input('finance'),
//         ];
//         $filters = array_filter($filters, fn($v) => !is_null($v) && $v !== '' && $v !== 0);
//         $usePersonalized = ($user && $user->favoriteScholarships()->count() > 0 && empty($filters));

//         $favoriteIds = $user ? $user->favoriteScholarships()->pluck('scholarships.id')->toArray() : [];

//         if ($usePersonalized) {
//             $favorites = $user->favoriteScholarships;
//             $specializationIds = $favorites->pluck('specialization_id')->filter()->unique()->values()->toArray();
//             $countryIds = $favorites->pluck('country_id')->filter()->unique()->values()->toArray();
//             $cityIds = $favorites->pluck('city_id')->filter()->unique()->values()->toArray();
//             $categoryIds = $favorites->pluck('category_id')->filter()->unique()->values()->toArray();

//             // دالة صغيرة تبني "?,?,?" وتضيف الـ bindings، وإذا الـ array فاضي بترجع شرط دايماً خاطئ بأمان
//             $buildIn = function (array $ids, array &$bindings) {
//                 if (empty($ids)) {
//                     return '-1'; // ما في قيم -> ما رح يطابق أي id حقيقي
//                 }
//                 $bindings = array_merge($bindings, $ids);
//                 return implode(',', array_fill(0, count($ids), '?'));
//             };

//             $bindings = [];
//             $specSql = $buildIn($specializationIds, $bindings);
//             $countrySql = $buildIn($countryIds, $bindings);
//             $citySql = $buildIn($cityIds, $bindings);
//             $categorySql = $buildIn($categoryIds, $bindings);

//             $query = Scholarship::with(['city', 'specialization', 'country', 'category'])
//                 ->where('finished_date', '>=', $today)
//                 // ملاحظة: ما في شرط where يقيّد النتائج -> رح ترجع كل المنح
//                 // وبس الترتيب هو يلي بيحدد إيمتى تطلع المفضلة/المشابهة بالأول
//                 ->orderByRaw("
//                 CASE
//                     WHEN specialization_id IN ($specSql) THEN 1
//                     WHEN country_id IN ($countrySql) THEN 2
//                     WHEN city_id IN ($citySql) THEN 3
//                     WHEN category_id IN ($categorySql) THEN 4
//                     ELSE 5
//                 END
//             ", $bindings)
//                 ->orderBy('id', 'desc'); // ترتيب ثانوي داخل نفس المجموعة

//             $message = 'تم جلب منح مقترحة بناءً على مفضلاتك';
//         } else {
//             $query = Scholarship::with(['city', 'specialization', 'country', 'category'])
//                 ->where('finished_date', '>=', $today)
//                 ->orderBy('id', 'desc');
//             if (!empty($filters)) {
//                 foreach ($filters as $column => $value) {
//                     if ($column === 'degree' || $column === 'finance') {
//                         $query->where($column, $value);
//                     } elseif ($column === 'country_id') {
//                         $query->where('country_id', $value);
//                     } elseif ($column === 'category_id') {
//                         $query->where('category_id', $value);
//                     } elseif ($column === 'specialization_id') {
//                         $query->where('specialization_id', $value);
//                     }
//                 }
//                 $message = 'تم جلب المنح حسب الفلتر';
//             } else {
//                 $message = 'تم جلب أحدث المنح';
//             }
//         }

//         $scholarships = $query->paginate($perPage, ['*'], 'page', $page);

//         // جلب الصور دفعة وحدة لكل عناصر الصفحة الحالية (بدون N+1)
//         $photoMap = $this->resolvePhotosForScholarships($scholarships->getCollection());

//         $formatted = $scholarships->getCollection()->map(function ($scholarship) use ($today, $user, $favoriteIds, $photoMap) {
//             $startStatus = $this->calculateStartStatus($scholarship, $today);

//             $result = [
//                 'id' => $scholarship->id,
//                 'scholarship_name' => $scholarship->scholarship_name,
//                 'finance' => $scholarship->finance,
//                 'degree' => $scholarship->degree,
//                 'city_name' => $scholarship->city->city_name ?? null,
//                 'specialization_name' => $scholarship->specialization->specialization_name ?? null,
//                 'start_status' => $startStatus,
//                 'photo_url' => $photoMap->get($scholarship->id),
//             ];

//             if ($user) {
//                 $result['is_favorite'] = in_array($scholarship->id, $favoriteIds);
//             }

//             return $result;
//         });

//         $paginatedData = new LengthAwarePaginator(
//             $formatted,
//             $scholarships->total(),
//             $scholarships->perPage(),
//             $scholarships->currentPage(),
//             ['path' => LengthAwarePaginator::resolveCurrentPath()]
//         );

//         return response()->json([
//             'status' => 'success',
//             'message' => $message,
//             'data' => $paginatedData
//         ], 200);
//     }

//     // ===========================================================================================================
//     // جلب المنح حسب الدولة
//     // ========================================================================================================
//     public function getByCountry(int $countryId)
//     {

//         $country = Country::find($countryId);
//         if (!$country) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'الدولة غير موجودة',
//                 'count' => 0,
//                 'data' => null
//             ], 404);
//         }
//         $today = Carbon::today();
//         $scholarships = Scholarship::with(['city', 'specialization'])
//             ->where('country_id', $countryId)
//             ->where('finished_date', '>=', $today) // المنح التي لم تنتهِ
//             ->orderBy('id', 'desc')
//             ->get();

//         $photoMap = $this->resolvePhotosForScholarships($scholarships);

//         $formatted = $scholarships->map(function ($scholarship) use ($today, $photoMap) {
//             $startStatus = $this->calculateStartStatus($scholarship, $today);

//             return [
//                 'id' => $scholarship->id,
//                 'scholarship_name' => $scholarship->scholarship_name,
//                 'finance' => $scholarship->finance,
//                 'degree' => $scholarship->degree,
//                 'city_name' => $scholarship->city->city_name ?? null,
//                 'specialization_name' => $scholarship->specialization->specialization_name ?? null,
//                 'start_status' => $startStatus,
//                 'photo_url' => $photoMap->get($scholarship->id),
//             ];
//         });

//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم جلب المنح للدولة بنجاح',
//             'count' => $formatted->count(),
//             'data' => $formatted
//         ], 200);
//     }



//     // ========================================================================================================
//     // إضافة منحة جديدة
//     // ملاحظة: ما عاد في رفع صورة هون. الصورة بتنجاب تلقائياً وقت العرض
//     // من "بنك الصور" (جدول photos) حسب تطابق المدينة والتخصص.
//     // رفع صور جديدة لبنك الصور صار من مسؤولية PhotoController منفصل.
//     // ===================================================================================================
//     public function store(StoreScholarshipRequest $request)
//     {
//         if (!Auth::check() || Auth::user()->role !== 'admin') {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
//                 'data' => null
//             ], 403);
//         }
//         $validated = $request->validated();


//         DB::beginTransaction();
//         try {
//             // إنشاء المنحة
//             $scholarship = Scholarship::create($validated);

//             // إضافة review اختياري
//             if ($request->filled('reviewer_name') && $request->filled('review')) {
//                 $scholarship->reviews()->create([
//                     'reviewer_name' => $request->reviewer_name,
//                     'review' => $request->review,
//                     'rating' => $request->rating ?? null,
//                 ]);
//             }
//             // إضافة how_to_apply اختياري
//             if ($request->filled('how_to_apply_description')) {
//                 $scholarship->howToApply()->create([
//                     'how_to_apply_description' => $request->how_to_apply_description,
//                 ]);
//             }
//             if ($request->filled('application_criteria')) {
//                 $criteria = $request->input('application_criteria');
//                 $scholarship->applicationCriteria()->create([
//                     'age' => $criteria['age'] ?? null,
//                     'gender' => $criteria['gender'] ?? null,
//                     'nationalities' => $criteria['nationalities'] ?? null,
//                 ]);
//             }

//             $notificationsSent = true;
//             try {
//                 $this->sendNotificationsToMatchingUsers($scholarship);
//             } catch (\Throwable $notifyException) {
//                 // فشل إرسال الإشعارات لا يجب أن يوقف إنشاء المنحة أو يعمل rollback لها
//                 $notificationsSent = false;
//                 Log::error('فشل إرسال إشعارات المنحة الجديدة (id: ' . $scholarship->id . '): ' . $notifyException->getMessage());
//             }

//             DB::commit();
//             // تحميل العلاقات لعرضها في الاستجابة
//             $scholarship->load(['country', 'city', 'specialization', 'category', 'reviews', 'howToApply', 'applicationCriteria']);
//             $scholarship->photo_url = $this->resolvePhotoForScholarship($scholarship);

//             return response()->json([
//                 'status' => 'success',
//                 'message' => $notificationsSent
//                     ? 'تمت إضافة المنحة بنجاح'
//                     : 'تمت إضافة المنحة بنجاح، لكن تعذّر إرسال إشعارات للمستخدمين',
//                 'data' => $scholarship
//             ], 201);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'فشل إضافة المنحة: ' . $e->getMessage(),
//                 'data' => null
//             ], 500);
//         }
//     }

//     // ==============================================================================================================================
//     // عرض منحة محددة
//     // =============================================================================================================================
//     public function show(int $id)
//     {
//         $scholarship = Scholarship::with([
//             'country',
//             'city',
//             'reviews',
//             'specialization',
//             'category',
//             'howToApply',
//             'applicationCriteria',
//             'personalExperiences'
//         ])->find($id);

//         if (!$scholarship) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'المنحة غير موجودة',
//                 'data' => null
//             ], 404);
//         }

//         $scholarship->photo_url = $this->resolvePhotoForScholarship($scholarship);

//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم جلب المنحة بنجاح',
//             'data' => $scholarship
//         ], 200);
//     }


//     // =============================================================================================================================
//     //                                                      تحديث منحة
//     // ملاحظة: ما عاد في رفع/حذف صورة هون لنفس السبب يلي بـ store()
//     // =============================================================================================================================
//     public function update(UpdateScholarshipRequest $request, int $id)
//     {
//         if (!Auth::check() || Auth::user()->role !== 'admin') {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
//                 'data' => null
//             ], 403);
//         }
//         $scholarship = Scholarship::find($id);
//         if (!$scholarship) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'المنحة غير موجودة',
//                 'data' => null
//             ], 404);
//         }

//         $validated = $request->validated();


//         DB::beginTransaction();
//         try {
//             $scholarship->update($validated);
//             if ($request->filled('application_criteria')) {
//                 $criteria = $request->input('application_criteria');
//                 $scholarship->applicationCriteria()->updateOrCreate(
//                     ['scholarship_id' => $scholarship->id],
//                     [
//                         'age' => $criteria['age'] ?? null,
//                         'gender' => $criteria['gender'] ?? null,
//                         'nationalities' => $criteria['nationalities'] ?? null,
//                     ]
//                 );
//             }

//             DB::commit();
//             $scholarship->load(['country', 'city', 'specialization', 'category', 'applicationCriteria']);
//             // إعادة حساب الصورة لأنه ممكن الـ city_id أو specialization_id يكونوا تغيّروا بالتحديث
//             $scholarship->photo_url = $this->resolvePhotoForScholarship($scholarship);

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'تم تحديث المنحة بنجاح',
//                 'data' => $scholarship
//             ], 200);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'فشل تحديث المنحة: ' . $e->getMessage(),
//                 'data' => null
//             ], 500);
//         }
//     }
//     // ==============================================================================================================================
//     //                                                   حذف منحة
//     // ملاحظة: ما عاد في حذف صور هون، لأنه الصور صارت "بنك" مستقل
//     // مش ملك للمنحة، فحذف المنحة ما لازم يمسح صور ممكن تكون مستخدمة
//     // لمنح تانية بنفس المدينة والتخصص.
//     // =============================================================================================================================
//     public function destroy(int $id)
//     {
//         if (!Auth::check() || Auth::user()->role !== 'admin') {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
//                 'data' => null
//             ], 403);
//         }
//         $scholarship = Scholarship::find($id);
//         if (!$scholarship) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'المنحة غير موجودة',
//                 'data' => null
//             ], 404);
//         }

//         DB::beginTransaction();
//         try {
//             $scholarship->delete();
//             DB::commit();

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'تم حذف المنحة بنجاح',
//                 'data' => null
//             ], 200);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'فشل حذف المنحة: ' . $e->getMessage(),
//                 'data' => null
//             ], 500);
//         }
//     }


//     // ==============================================================================================
//     // جلب منح مشابهة لمنحة معينة بناءً على التخصص، ثم المدينة، ثم الدولة، ثم الفئة
//     // =====================================================================================
//     public function getSimilarScholarships(int $id)
//     {
//         $scholarship = Scholarship::find($id);
//         if (!$scholarship) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'المنحة غير موجودة',
//                 'data' => null
//             ], 404);
//         }

//         // 1. نفس التخصص
//         $similar = Scholarship::where('specialization_id', $scholarship->specialization_id)
//             ->where('id', '!=', $id)
//             ->where('finished_date', '>=', now()) // لم تنتهِ
//             ->limit(3)
//             ->get();

//         // 2. إذا كان العدد أقل من 3، أضف من نفس المدينة
//         if ($similar->count() < 3) {
//             $needed = 3 - $similar->count();
//             $more = Scholarship::where('city_id', $scholarship->city_id)
//                 ->where('id', '!=', $id)
//                 ->whereNotIn('id', $similar->pluck('id'))
//                 ->where('finished_date', '>=', now())
//                 ->limit($needed)
//                 ->get();
//             $similar = $similar->merge($more);
//         }

//         // 3. إذا كان العدد أقل من 3، أضف من نفس الدولة
//         if ($similar->count() < 3) {
//             $needed = 3 - $similar->count();
//             $more = Scholarship::where('country_id', $scholarship->country_id)
//                 ->where('id', '!=', $id)
//                 ->whereNotIn('id', $similar->pluck('id'))
//                 ->where('finished_date', '>=', now())
//                 ->limit($needed)
//                 ->get();
//             $similar = $similar->merge($more);
//         }

//         // 4. إذا كان العدد أقل من 3، أضف من نفس الفئة
//         if ($similar->count() < 3) {
//             $needed = 3 - $similar->count();
//             $more = Scholarship::where('category_id', $scholarship->category_id)
//                 ->where('id', '!=', $id)
//                 ->whereNotIn('id', $similar->pluck('id'))
//                 ->where('finished_date', '>=', now())
//                 ->limit($needed)
//                 ->get();
//             $similar = $similar->merge($more);
//         }

//         // تحميل العلاقات المطلوبة للعرض (مش موجودة كانت بالأصل محمّلة بالـ query فوق)
//         $similar->load(['city', 'specialization']);

//         $photoMap = $this->resolvePhotosForScholarships($similar);

//         // تنسيق البيانات مثل getTopScholarships
//         $today = Carbon::today();
//         $data = $similar->map(function ($item) use ($today, $photoMap) {
//             $startStatus = $this->calculateStartStatus($item, $today);

//             return [
//                 'id' => $item->id,
//                 'scholarship_name' => $item->scholarship_name,
//                 'finance' => $item->finance,
//                 'degree' => $item->degree,
//                 'city_name' => $item->city->city_name ?? null,
//                 'specialization_name' => $item->specialization->specialization_name ?? null,
//                 'start_status' => $startStatus,
//                 'photo_url' => $photoMap->get($item->id),
//             ];
//         });

//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم جلب المنح المشابهة',
//             'count' => $data->count(),
//             'data' => $data
//         ], 200);
//     }





//     // ==========================================================================================
//     // ارسال إشعارات للمستخدمين الذين لديهم تفضيلات مطابقة للمنحة
//     // =========================================================================================
//     private function sendNotificationsToMatchingUsers($scholarship)
//     {
//         // جلب جميع المستخدمين المسجلين في النظام
//         $users = User::all();

//         if ($users->isEmpty()) {
//             return;
//         }

//         $sentCount = 0;
//         foreach ($users as $user) {
//             NotificationController::create(
//                 $user->id,
//                 'info',
//                 '📢 منحة جديدة متاحة!',
//                 "تم إضافة منحة جديدة: {$scholarship->scholarship_name} في تخصص {$scholarship->specialization->specialization_name}",
//                 [
//                     'scholarship_id' => $scholarship->id,
//                     'scholarship_name' => $scholarship->scholarship_name,
//                     'link' => '/scholarships/' . $scholarship->id,
//                 ]
//             );
//             $sentCount++;
//         }

//         // تسجيل عدد الإشعارات المرسلة
//         Log::info("تم إرسال {$sentCount} إشعار لجميع المستخدمين عن المنحة الجديدة: {$scholarship->id}");
//     }
// }

// namespace App\Http\Controllers;

// use Carbon\Carbon;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Log;
// use App\Models\Category;
// use App\Models\City;
// use App\Models\User;
// use App\Models\Country;
// use App\Models\Scholarship;
// use App\Models\Specialization;
// use Illuminate\Http\Request;
// use App\Http\Requests\StoreScholarshipRequest;
// use App\Http\Requests\UpdateScholarshipRequest;
// use Illuminate\Support\Facades\DB;
// use Psy\Readline\Hoa\Console;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Pagination\LengthAwarePaginator;

// class ScholarshipController extends Controller
// {
//     // ======================================================================= // جلب جميع المنح (مع الصور والعلاقات)
//     public function index()
//     {
//         $scholarships = Scholarship::with([
//             'country',
//             'city',
//             'specialization',
//             'category',
//             'photos'
//         ])->get();

//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم جلب المنح بنجاح',
//             'data' => $scholarships
//         ], 200);
//     }



//     public function getTopScholarships(Request $request)
//     {
//         /** @var \App\Models\User|null $user */
//         $user = auth('sanctum')->user();
//         $today = Carbon::today();
//         $perPage = (int) $request->input('per_page', 15);
//         $page = (int) $request->input('page', 1);
//         $filters = [
//             'country_id' => $request->input('country'),
//             'category_id' => $request->input('category'),
//             'degree' => $request->input('degree'),
//             'specialization_id' => $request->input('specialization'),
//             'finance' => $request->input('finance'),
//         ];
//         $filters = array_filter($filters, fn($v) => !is_null($v) && $v !== '' && $v !== 0);
//         $usePersonalized = ($user && $user->favoriteScholarships()->count() > 0 && empty($filters));

//         $favoriteIds = $user ? $user->favoriteScholarships()->pluck('scholarships.id')->toArray() : [];

//         if ($usePersonalized) {
//             $favorites = $user->favoriteScholarships;
//             $specializationIds = $favorites->pluck('specialization_id')->filter()->unique()->values()->toArray();
//             $countryIds = $favorites->pluck('country_id')->filter()->unique()->values()->toArray();
//             $cityIds = $favorites->pluck('city_id')->filter()->unique()->values()->toArray();
//             $categoryIds = $favorites->pluck('category_id')->filter()->unique()->values()->toArray();

//             // دالة صغيرة تبني "?,?,?" وتضيف الـ bindings، وإذا الـ array فاضي بترجع شرط دايماً خاطئ بأمان
//             $buildIn = function (array $ids, array &$bindings) {
//                 if (empty($ids)) {
//                     return '-1'; // ما في قيم -> ما رح يطابق أي id حقيقي
//                 }
//                 $bindings = array_merge($bindings, $ids);
//                 return implode(',', array_fill(0, count($ids), '?'));
//             };

//             $bindings = [];
//             $specSql = $buildIn($specializationIds, $bindings);
//             $countrySql = $buildIn($countryIds, $bindings);
//             $citySql = $buildIn($cityIds, $bindings);
//             $categorySql = $buildIn($categoryIds, $bindings);

//             $query = Scholarship::with(['city', 'specialization', 'photos', 'country', 'category'])
//                 ->where('finished_date', '>=', $today)
//                 // ملاحظة: ما في شرط where يقيّد النتائج -> رح ترجع كل المنح
//                 // وبس الترتيب هو يلي بيحدد إيمتى تطلع المفضلة/المشابهة بالأول
//                 ->orderByRaw("
//                 CASE
//                     WHEN specialization_id IN ($specSql) THEN 1
//                     WHEN country_id IN ($countrySql) THEN 2
//                     WHEN city_id IN ($citySql) THEN 3
//                     WHEN category_id IN ($categorySql) THEN 4
//                     ELSE 5
//                 END
//             ", $bindings)
//                 ->orderBy('id', 'desc'); // ترتيب ثانوي داخل نفس المجموعة

//             $message = 'تم جلب منح مقترحة بناءً على مفضلاتك';
//         } else {
//             $query = Scholarship::with(['city', 'specialization', 'photos', 'country', 'category'])
//                 ->where('finished_date', '>=', $today)
//                 ->orderBy('id', 'desc');
//             if (!empty($filters)) {
//                 foreach ($filters as $column => $value) {
//                     if ($column === 'degree' || $column === 'finance') {
//                         $query->where($column, $value);
//                     } elseif ($column === 'country_id') {
//                         $query->where('country_id', $value);
//                     } elseif ($column === 'category_id') {
//                         $query->where('category_id', $value);
//                     } elseif ($column === 'specialization_id') {
//                         $query->where('specialization_id', $value);
//                     }
//                 }
//                 $message = 'تم جلب المنح حسب الفلتر';
//             } else {
//                 $message = 'تم جلب أحدث المنح';
//             }
//         }

//         $scholarships = $query->paginate($perPage, ['*'], 'page', $page);

//         $formatted = $scholarships->getCollection()->map(function ($scholarship) use ($today, $user, $favoriteIds) {
//             $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;
//             if ($startDate && $startDate->isFuture()) {
//                 $startStatus = 'تبدأ في ' . $startDate->toDateString();
//             } elseif ($startDate && $startDate->lte($today)) {
//                 $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
//                 $startStatus = $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
//             } else {
//                 $startStatus = 'تاريخ البدء غير محدد';
//             }

//             $photoUrl = $scholarship->photos->isNotEmpty() ? url($scholarship->photos->first()->image_path) : null;

//             $result = [
//                 'id' => $scholarship->id,
//                 'scholarship_name' => $scholarship->scholarship_name,
//                 'finance' => $scholarship->finance,
//                 'degree' => $scholarship->degree,
//                 'city_name' => $scholarship->city->city_name ?? null,
//                 'specialization_name' => $scholarship->specialization->specialization_name ?? null,
//                 'start_status' => $startStatus,
//                 'photo_url' => $photoUrl,
//             ];

//             if ($user) {
//                 $result['is_favorite'] = in_array($scholarship->id, $favoriteIds);
//             }

//             return $result;
//         });

//         $paginatedData = new LengthAwarePaginator(
//             $formatted,
//             $scholarships->total(),
//             $scholarships->perPage(),
//             $scholarships->currentPage(),
//             ['path' => LengthAwarePaginator::resolveCurrentPath()]
//         );

//         return response()->json([
//             'status' => 'success',
//             'message' => $message,
//             'data' => $paginatedData
//         ], 200);
//     }

//     // ===========================================================================================================
//     // جلب المنح حسب الدولة 
//     // ========================================================================================================
//     public function getByCountry(int $countryId)
//     {

//         $country = Country::find($countryId);
//         if (!$country) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'الدولة غير موجودة',
//                 'count' => 0,
//                 'data' => null
//             ], 404);
//         }
//         $today = Carbon::today();
//         $scholarships = Scholarship::with(['city', 'specialization', 'photos'])
//             ->where('country_id', $countryId)
//             ->where('finished_date', '>=', $today) // المنح التي لم تنتهِ
//             ->orderBy('id', 'desc')
//             ->get()
//             ->map(function ($scholarship) use ($today) {
//                 // حساب حالة البدء (مثل getTopScholarships)
//                 $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;
//                 if ($startDate && $startDate->isFuture()) {
//                     $startStatus = 'تبدأ في ' . $startDate->toDateString();
//                 } elseif ($startDate && $startDate->lte($today)) {
//                     $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
//                     $startStatus = $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
//                 } else {
//                     $startStatus = 'تاريخ البدء غير محدد';
//                 }

//                 $photoUrl = null;
//                 if ($scholarship->photos->isNotEmpty()) {
//                     $photoUrl = url($scholarship->photos->first()->image_path);
//                 }

//                 return [
//                     'id' => $scholarship->id,
//                     'scholarship_name' => $scholarship->scholarship_name,
//                     'finance' => $scholarship->finance,
//                     'degree' => $scholarship->degree,
//                     'city_name' => $scholarship->city->city_name ?? null,
//                     'specialization_name' => $scholarship->specialization->specialization_name ?? null,
//                     'start_status' => $startStatus,
//                     'photo_url' => $photoUrl,
//                 ];
//             });

//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم جلب المنح للدولة بنجاح',
//             'count' => $scholarships->count(),
//             'data' => $scholarships
//         ], 200);
//     }



//     // ========================================================================================================
//     // إضافة منحة جديدة
//     // ===================================================================================================
//     public function store(StoreScholarshipRequest $request)
//     {
//         if (!Auth::check() || Auth::user()->role !== 'admin') {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
//                 'data' => null
//             ], 403);
//         }
//         $validated = $request->validated();


//         DB::beginTransaction();
//         try {
//             // إنشاء المنحة
//             $scholarship = Scholarship::create($validated);

//             // رفع الصورة إن وُجدت
//             if ($request->hasFile('photo')) {
//                 $file = $request->file('photo');
//                 $fileName = time() . '_' . $file->getClientOriginalName();
//                 $path = $file->storeAs('scholarships', $fileName, 'public');
//                 $scholarship->photos()->create([
//                     'image_path' => Storage::url($path),
//                     'city_id' => $validated['city_id'] ?? null,
//                 ]);
//             }
//             // إضافة review اختياري
//             if ($request->filled('reviewer_name') && $request->filled('review')) {
//                 $scholarship->reviews()->create([
//                     'reviewer_name' => $request->reviewer_name,
//                     'review' => $request->review,
//                     'rating' => $request->rating ?? null,
//                 ]);
//             }
//             // إضافة how_to_apply اختياري
//             if ($request->filled('how_to_apply_description')) {
//                 $scholarship->howToApply()->create([
//                     'how_to_apply_description' => $request->how_to_apply_description,
//                 ]);
//             }
//             if ($request->filled('application_criteria')) {
//                 $criteria = $request->input('application_criteria');
//                 $scholarship->applicationCriteria()->create([
//                     'age' => $criteria['age'] ?? null,
//                     'gender' => $criteria['gender'] ?? null,
//                     'nationalities' => $criteria['nationalities'] ?? null,
//                 ]);
//             }
//             // $this->sendNotificationsToMatchingUsers($scholarship);
//             $notificationsSent = true;
//             try {
//                 $this->sendNotificationsToMatchingUsers($scholarship);
//             } catch (\Throwable $notifyException) {
//                 // فشل إرسال الإشعارات لا يجب أن يوقف إنشاء المنحة أو يعمل rollback لها
//                 $notificationsSent = false;
//                 Log::error('فشل إرسال إشعارات المنحة الجديدة (id: ' . $scholarship->id . '): ' . $notifyException->getMessage());
//             }

//             DB::commit();
//             // تحميل العلاقات لعرضها في الاستجابة
//             $scholarship->load(['country', 'city', 'specialization', 'category', 'photos', 'reviews', 'howToApply', 'applicationCriteria']);

//             return response()->json([
//                 'status' => 'success',
//                 'message' => $notificationsSent
//                     ? 'تمت إضافة المنحة بنجاح'
//                     : 'تمت إضافة المنحة بنجاح، لكن تعذّر إرسال إشعارات للمستخدمين',
//                 'data' => $scholarship
//             ], 201);

//             // DB::commit();
//             // // تحميل العلاقات لعرضها في الاستجابة
//             // $scholarship->load(['country', 'city', 'specialization', 'category', 'photos', 'reviews', 'howToApply', 'applicationCriteria']);

//             // return response()->json([
//             //     'status' => 'success',
//             //     'message' => 'تمت إضافة المنحة بنجاح',
//             //     'data' => $scholarship
//             // ], 201);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'فشل إضافة المنحة: ' . $e->getMessage(),
//                 'data' => null
//             ], 500);
//         }
//     }

//     // ==============================================================================================================================
//     // عرض منحة محددة
//     // =============================================================================================================================
//     public function show(int $id)
//     {
//         $scholarship = Scholarship::with([
//             'country',
//             'city',
//             'reviews',
//             'specialization',
//             'category',
//             'photos',
//             'howToApply',
//             'applicationCriteria',
//             'personalExperiences'
//         ])->find($id);

//         if (!$scholarship) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'المنحة غير موجودة',
//                 'data' => null
//             ], 404);
//         }

//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم جلب المنحة بنجاح',
//             'data' => $scholarship
//         ], 200);
//     }


//     // =============================================================================================================================
//     //                                                      تحديث منحة
//     // =============================================================================================================================
//     public function update(UpdateScholarshipRequest $request, int $id)
//     {
//         if (!Auth::check() || Auth::user()->role !== 'admin') {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
//                 'data' => null
//             ], 403);
//         }
//         $scholarship = Scholarship::find($id);
//         if (!$scholarship) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'المنحة غير موجودة',
//                 'data' => null
//             ], 404);
//         }

//         $validated = $request->validated();


//         DB::beginTransaction();
//         try {
//             $scholarship->update($validated);
//             if ($request->filled('application_criteria')) {
//                 $criteria = $request->input('application_criteria');
//                 $scholarship->applicationCriteria()->updateOrCreate(
//                     ['scholarship_id' => $scholarship->id],
//                     [
//                         'age' => $criteria['age'] ?? null,
//                         'gender' => $criteria['gender'] ?? null,
//                         'nationalities' => $criteria['nationalities'] ?? null,
//                     ]
//                 );
//             }

//             // معالجة الصورة الجديدة (إن وجدت)
//             if ($request->hasFile('photo')) {
//                 // حذف الصورة القديمة إن وجدت
//                 if ($scholarship->photos->isNotEmpty()) {
//                     $oldPhoto = $scholarship->photos->first();
//                     // حذف الملف الفعلي من التخزين
//                     $oldPath = str_replace('/storage/', '', $oldPhoto->image_path);
//                     Storage::disk('public')->delete($oldPath);
//                     $oldPhoto->delete();
//                 }
//                 // رفع الصورة الجديدة
//                 $file = $request->file('photo');
//                 $fileName = time() . '_' . $file->getClientOriginalName();
//                 $path = $file->storeAs('scholarships', $fileName, 'public');
//                 $scholarship->photos()->create([
//                     'image_path' => Storage::url($path)
//                 ]);
//             }

//             DB::commit();
//             $scholarship->load(['country', 'city', 'specialization', 'category', 'photos', 'applicationCriteria']);

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'تم تحديث المنحة بنجاح',
//                 'data' => $scholarship
//             ], 200);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'فشل تحديث المنحة: ' . $e->getMessage(),
//                 'data' => null
//             ], 500);
//         }
//     }
//     // ==============================================================================================================================
//     //                                                   حذف منحة
//     // =============================================================================================================================
//     public function destroy(int $id)
//     {
//         if (!Auth::check() || Auth::user()->role !== 'admin') {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
//                 'data' => null
//             ], 403);
//         }
//         $scholarship = Scholarship::find($id);
//         if (!$scholarship) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'المنحة غير موجودة',
//                 'data' => null
//             ], 404);
//         }

//         DB::beginTransaction();
//         try {
//             // حذف الصور المرتبطة من التخزين
//             foreach ($scholarship->photos as $photo) {
//                 $path = str_replace('/storage/', '', $photo->image_path);
//                 Storage::disk('public')->delete($path);
//             }
//             // حذف المنحة (ستحذف الصور تلقائياً عبر cascade أو يدوياً)
//             $scholarship->delete();
//             DB::commit();

//             return response()->json([
//                 'status' => 'success',
//                 'message' => 'تم حذف المنحة بنجاح',
//                 'data' => null
//             ], 200);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'فشل حذف المنحة: ' . $e->getMessage(),
//                 'data' => null
//             ], 500);
//         }
//     }


//     // ==============================================================================================
//     // جلب منح مشابهة لمنحة معينة بناءً على التخصص، ثم المدينة، ثم الدولة، ثم الفئة
//     // =====================================================================================
//     public function getSimilarScholarships(int $id)
//     {
//         $scholarship = Scholarship::find($id);
//         if (!$scholarship) {
//             return response()->json([
//                 'status' => 'error',
//                 'message' => 'المنحة غير موجودة',
//                 'data' => null
//             ], 404);
//         }

//         // 1. نفس التخصص
//         $similar = Scholarship::where('specialization_id', $scholarship->specialization_id)
//             ->where('id', '!=', $id)
//             ->where('finished_date', '>=', now()) // لم تنتهِ
//             ->limit(3)
//             ->get();

//         // 2. إذا كان العدد أقل من 3، أضف من نفس المدينة
//         if ($similar->count() < 3) {
//             $needed = 3 - $similar->count();
//             $more = Scholarship::where('city_id', $scholarship->city_id)
//                 ->where('id', '!=', $id)
//                 ->whereNotIn('id', $similar->pluck('id'))
//                 ->where('finished_date', '>=', now())
//                 ->limit($needed)
//                 ->get();
//             $similar = $similar->merge($more);
//         }

//         // 3. إذا كان العدد أقل من 3، أضف من نفس الدولة
//         if ($similar->count() < 3) {
//             $needed = 3 - $similar->count();
//             $more = Scholarship::where('country_id', $scholarship->country_id)
//                 ->where('id', '!=', $id)
//                 ->whereNotIn('id', $similar->pluck('id'))
//                 ->where('finished_date', '>=', now())
//                 ->limit($needed)
//                 ->get();
//             $similar = $similar->merge($more);
//         }

//         // 4. إذا كان العدد أقل من 3، أضف من نفس الفئة
//         if ($similar->count() < 3) {
//             $needed = 3 - $similar->count();
//             $more = Scholarship::where('category_id', $scholarship->category_id)
//                 ->where('id', '!=', $id)
//                 ->whereNotIn('id', $similar->pluck('id'))
//                 ->where('finished_date', '>=', now())
//                 ->limit($needed)
//                 ->get();
//             $similar = $similar->merge($more);
//         }

//         // تنسيق البيانات مثل getTopScholarships
//         $today = \Carbon\Carbon::today();
//         $data = $similar->map(function ($item) use ($today) {
//             $startDate = $item->start_date ? \Carbon\Carbon::parse($item->start_date) : null;
//             if ($startDate && $startDate->isFuture()) {
//                 $startStatus = 'تبدأ في ' . $startDate->toDateString();
//             } elseif ($startDate && $startDate->lte($today)) {
//                 $daysRemaining = $today->diffInDays(\Carbon\Carbon::parse($item->finished_date), false);
//                 $startStatus = $daysRemaining > 0 ? "تبقى {$daysRemaining} يوم" : 'انتهت الصلاحية';
//             } else {
//                 $startStatus = 'تاريخ البدء غير محدد';
//             }

//             $photoUrl = $item->photos->isNotEmpty() ? url($item->photos->first()->image_path) : null;

//             return [
//                 'id' => $item->id,
//                 'scholarship_name' => $item->scholarship_name,
//                 'finance' => $item->finance,
//                 'degree' => $item->degree,
//                 'city_name' => $item->city->city_name ?? null,
//                 'specialization_name' => $item->specialization->specialization_name ?? null,
//                 'start_status' => $startStatus,
//                 'photo_url' => $photoUrl,
//             ];
//         });

//         return response()->json([
//             'status' => 'success',
//             'message' => 'تم جلب المنح المشابهة',
//             'count' => $data->count(),
//             'data' => $data
//         ], 200);
//     }





//     // ==========================================================================================
//     // ارسال إشعارات للمستخدمين الذين لديهم تفضيلات مطابقة للمنحة
//     // =========================================================================================
//     private function sendNotificationsToMatchingUsers($scholarship)
//     {
//         // جلب جميع المستخدمين المسجلين في النظام
//         $users = User::all();

//         if ($users->isEmpty()) {
//             return;
//         }

//         $sentCount = 0;
//         foreach ($users as $user) {
//             NotificationController::create(
//                 $user->id,
//                 'info',
//                 '📢 منحة جديدة متاحة!',
//                 "تم إضافة منحة جديدة: {$scholarship->scholarship_name} في تخصص {$scholarship->specialization->specialization_name}",
//                 [
//                     'scholarship_id' => $scholarship->id,
//                     'scholarship_name' => $scholarship->scholarship_name,
//                     'link' => '/scholarships/' . $scholarship->id,
//                 ]
//             );
//             $sentCount++;
//         }

//         // تسجيل عدد الإشعارات المرسلة
//         Log::info("تم إرسال {$sentCount} إشعار لجميع المستخدمين عن المنحة الجديدة: {$scholarship->id}");
//     }
// }
