<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use Illuminate\Http\Request;

class SpecializationController extends Controller
{
    // جلب جميع التخصصات (مع الفئة المرتبطة)
    public function index()
    {
        $specializations = Specialization::with('category')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب التخصصات بنجاح',
            'data' => $specializations
        ], 200);
    }

    // إضافة تخصص جديد
    public function store(Request $request)
    {
        $validated = $request->validate([
            'specialization_name' => 'required|string|max:30',
            'category_id' => 'required|exists:categories,id',
        ]);

        $specialization = Specialization::create($validated);
        // تحميل العلاقة لإرجاعها مع البيانات
        $specialization->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة التخصص بنجاح',
            'data' => $specialization
        ], 201);
    }

    // عرض تخصص محدد
    public function show($id)
    {
        $specialization = Specialization::with('category')->find($id);
        if (!$specialization) {
            return response()->json([
                'status' => 'error',
                'message' => 'التخصص غير موجود',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب التخصص بنجاح',
            'data' => $specialization
        ], 200);
    }

    // تحديث تخصص
    public function update(Request $request, $id)
    {
        $specialization = Specialization::find($id);
        if (!$specialization) {
            return response()->json([
                'status' => 'error',
                'message' => 'التخصص غير موجود',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'specialization_name' => 'sometimes|string|max:30',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $specialization->update($validated);
        $specialization->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث التخصص بنجاح',
            'data' => $specialization
        ], 200);
    }

    // حذف تخصص
    public function destroy($id)
    {
        $specialization = Specialization::find($id);
        if (!$specialization) {
            return response()->json([
                'status' => 'error',
                'message' => 'التخصص غير موجود',
                'data' => null
            ], 404);
        }

        $specialization->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف التخصص بنجاح',
            'data' => null
        ], 200);
    }
}