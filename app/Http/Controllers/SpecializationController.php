<?php

namespace App\Http\Controllers;

use App\Models\Specialization;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpecializationController extends Controller
{
    /**
     * جلب التخصصات بناءً على صلاحية المستخدم
     * - Admin: جميع التخصصات
     * - غير Admin: التخصصات التي لها منح فقط
     */
    public function index()
    {
        $user = Auth::user();

        // إذا كان المستخدم Admin، يجلب جميع التخصصات
        if ($user && $user->role === 'admin') {
            $specializations = Specialization::with('category')->get();
            $message = 'تم جلب جميع التخصصات بنجاح (صلاحية مدير)';
        } else {
            // جلب التخصصات التي لها منح دراسية
            $specializations = Specialization::whereHas('scholarships')
                ->with('category')
                ->get();
            $message = 'تم جلب التخصصات التي لها منح دراسية بنجاح';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $specializations,
            'is_admin' => $user && $user->role === 'admin',
        ], 200);
    }

    /**
     * إضافة تخصص جديد (للمدير فقط)
     */
    public function store(Request $request)
    {
        // التحقق من صلاحيات المدير
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'specialization_name' => 'required|string|max:30',
            'category_id' => 'required|exists:categories,id',
        ]);

        $specialization = Specialization::create($validated);
        $specialization->load('category');

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة التخصص بنجاح',
            'data' => $specialization
        ], 201);
    }

    /**
     * عرض تخصص محدد
     */
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

    /**
     * تحديث تخصص (للمدير فقط)
     */
    public function update(Request $request, $id)
    {
        // التحقق من صلاحيات المدير
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
                'data' => null
            ], 403);
        }

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

    /**
     * حذف تخصص (للمدير فقط)
     */
    public function destroy($id)
    {
        // التحقق من صلاحيات المدير
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
                'data' => null
            ], 403);
        }

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