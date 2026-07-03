<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserPreferenceController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth:sanctum');
    // }

    // جلب تفضيلات المستخدم الحالي
    public function show()
    {
        $preferences = Auth::user()->preferences->with(['country', 'specialization'])->first();
        if (!$preferences) {
            return response()->json([
                'status' => 'success',
                'message' => 'لا توجد تفضيلات محفوظة',
                'data' => null
            ], 200);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب التفضيلات',
            'data' => $preferences
        ], 200);
    }

    // إنشاء تفضيلات جديدة (لأول مرة)
    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // التأكد من عدم وجود تفضيلات مسبقاً
        if ($user->preferences) {
            return response()->json([
                'status' => 'error',
                'message' => " لديك تفضيلات بالفعل يمكنك تعديلها عن طريق put",
            ], 409);
        }

        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'specialization_id' => 'nullable|exists:specializations,id',
            'degree' => 'nullable|string|max:30',
        ]);

        $preferences = $user->preferences()->create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم إنشاء التفضيلات بنجاح',
            'data' => $preferences
        ], 201);
    }

    // تحديث التفضيلات الحالية
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $preferences = $user->preferences;

        if (!$preferences) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا توجد تفضيلات لتحديثها، استخدم POST لإنشائها',
            ], 404);
        }

        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'specialization_id' => 'nullable|exists:specializations,id',
            'degree' => 'nullable|string|max:30',
        ]);

        $preferences->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث التفضيلات بنجاح',
            'data' => $preferences->fresh()
        ], 200);
    }

    // حذف التفضيلات
    public function destroy()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $preferences = $user->preferences;

        if (!$preferences) {
            return response()->json([
                'status' => 'error',
                'message' => 'لا توجد تفضيلات لحذفها',
            ], 404);
        }

        $preferences->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف التفضيلات بنجاح',
        ], 200);
    }
}