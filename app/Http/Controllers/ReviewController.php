<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    // جلب جميع الريفيوز لمنحة معينة (عام)
    public function index($scholarshipId)
    {
        $scholarship = Scholarship::find($scholarshipId);
        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة',
            ], 404);
        }
        $reviews = $scholarship->reviews()->latest()->get();
        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب التقييمات',
            'count' => $reviews->count(),
            'data' => $reviews
        ], 200);
    }

    // إضافة ريفيو جديد (للأدمن فقط)
    public function store(Request $request, $scholarshipId)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
            ], 403);
        }

        $scholarship = Scholarship::find($scholarshipId);
        if (!$scholarship) {
            return response()->json([
                'status' => 'error',
                'message' => 'المنحة غير موجودة',
            ], 404);
        }

        $validated = $request->validate([
            'reviewer_name' => 'required|string|max:100',
            'review' => 'required|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $review = $scholarship->reviews()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إضافة التقييم بنجاح',
            'data' => $review
        ], 201);
    }

    // تعديل ريفيو (للأدمن فقط)
    public function update(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
            ], 403);
        }

        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'التقييم غير موجود',
            ], 404);
        }

        $validated = $request->validate([
            'reviewer_name' => 'sometimes|string|max:100',
            'review' => 'sometimes|string',
            'rating' => 'nullable|integer|min:1|max:5',
        ]);

        $review->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث التقييم بنجاح',
            'data' => $review
        ], 200);
    }

    // حذف ريفيو (للأدمن فقط)
    public function destroy($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
            ], 403);
        }

        $review = Review::find($id);
        if (!$review) {
            return response()->json([
                'status' => 'error',
                'message' => 'التقييم غير موجود',
            ], 404);
        }

        $review->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف التقييم بنجاح',
        ], 200);
    }
}