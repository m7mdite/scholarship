<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FavoriteScholarshipController extends Controller
{
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
            ->with(['city', 'specialization', 'photos', 'country', 'category'])
            ->get();

        $formatted = $favorites->map(function ($scholarship) use ($today) {
            $startDate = $scholarship->start_date ? Carbon::parse($scholarship->start_date) : null;

            if ($startDate && $startDate->isFuture()) {
                $startStatus = 'تبدأ في ' . $startDate->toDateString();
            } elseif ($startDate && $startDate->lte($today)) {
                $daysRemaining = $today->diffInDays(Carbon::parse($scholarship->finished_date), false);
                $startStatus = $daysRemaining > 0 ? "تبقت {$daysRemaining} يوم" : 'انتهت الصلاحية';
            } else {
                $startStatus = 'تاريخ البدء غير محدد';
            }

            $photoUrl = $scholarship->photos->isNotEmpty()
                ? url($scholarship->photos->first()->image_path)
                : null;

            return [
                'id' => $scholarship->id,
                'scholarship_name' => $scholarship->scholarship_name,
                'finance' => $scholarship->finance,
                'degree' => $scholarship->degree,
                'city_name' => $scholarship->city->city_name ?? null,
                'specialization_name' => $scholarship->specialization->specialization_name ?? null,
                'start_status' => $startStatus,
                'photo_url' => $photoUrl,
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