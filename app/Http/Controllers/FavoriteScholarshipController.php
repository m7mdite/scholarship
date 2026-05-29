<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\Http\Request;

class FavoriteScholarshipController extends Controller
{
    // إضافة منحة إلى المفضلة
    public function add(Request $request, $scholarshipId)
    {
        $user = $request->user();
        $scholarship = Scholarship::find($scholarshipId);

        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة'
            ], 404);
        }

        // التحقق إذا كانت المنحة موجودة بالفعل في المفضلة
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
    public function remove(Request $request, $scholarshipId)
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
        $favorites = $request->user()->favoriteScholarships()->with(['country', 'city'])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المنح المفضلة',
            'data' => $favorites
        ], 200);
    }
}