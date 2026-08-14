<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\Photo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FavoriteScholarshipController extends Controller
{
    private const DEFAULT_PHOTO_PATH = '/storage/scholarships/default.jpg';

    // نفس الـ helper المستخدم بـ ScholarshipController — يجيب بنك الصور كامل مرة وحدة
    private function photosMap(): Collection
    {
        return Photo::all()->keyBy(fn($p) => $p->city_id . '-' . $p->specialization_id);
    }

    private function photoUrlFor($scholarship, ?Collection $map = null): string
    {
        $map = $map ?? $this->photosMap();
        $key = $scholarship->city_id . '-' . $scholarship->specialization_id;
        $photo = $map->get($key);

        return $photo ? url($photo->image_path) : url(self::DEFAULT_PHOTO_PATH);
    }

    // إضافة منحة إلى المفضلة
    public function add(Request $request,int $scholarshipId)
    {
        $user = $request->user();
        $scholarship = Scholarship::find($scholarshipId);

        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة'
            ], 404);
        }

        if ($user->favoriteScholarships()->where('scholarship_id', $scholarshipId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة موجودة بالفعل في المفضلة'
            ], 409);
        }

        $user->favoriteScholarships()->attach($scholarshipId);

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة المنحة إلى المفضلة',
            'data' => null
        ], 201);
    }

    // حذف منحة من المفضلة
    public function remove(Request $request,int $scholarshipId)
    {
        $user = $request->user();

        if (!$user->favoriteScholarships()->where('scholarship_id', $scholarshipId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة في المفضلة'
            ], 404);
        }

        $user->favoriteScholarships()->detach($scholarshipId);

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إزالة المنحة من المفضلة',
            'data' => null
        ], 200);
    }

    // عرض قائمة المنح المفضلة للمستخدم الحالي
    public function index(Request $request)
    {
        $today = Carbon::today();

        $favorites = $request->user()->favoriteScholarships()
            ->with(['city', 'specialization', 'country', 'category'])
            ->get();

        $map = $this->photosMap();

        $formatted = $favorites->map(function ($scholarship) use ($today, $map) {
            $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;

            if ($startDate && $startDate->isFuture()) {
                $startStatus = 'تبدأ في ' . $startDate->toDateString();
            } elseif ($startDate && $startDate->lte($today)) {
                $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
                $startStatus = $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
            } else {
                $startStatus = 'تاريخ البدء غير محدد';
            }

            return [
                'id' => $scholarship->id,
                'scholarship_name' => $scholarship->scholarship_name,
                'finance' => $scholarship->finance,
                'degree' => $scholarship->degree,
                'city_name' => $scholarship->city->city_name ?? null,
                'specialization_name' => $scholarship->specialization->specialization_name ?? null,
                'start_status' => $startStatus,
                'photo_url' => $this->photoUrlFor($scholarship, $map),
                'is_favorite' => true,
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنح المفضلة',
            'data' => $formatted
        ], 200);
    }

}