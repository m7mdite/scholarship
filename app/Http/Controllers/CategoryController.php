<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * جلب الفئات بناءً على صلاحية المستخدم
     * - Admin: جميع الفئات
     * - غير Admin: الفئات التي لها منح فقط
     */
    public function index()
    {
        $user = Auth::user();

        // إذا كان المستخدم Admin، يجلب جميع الفئات
        if ($user && $user->role === 'admin') {
            $categories = Category::all();
            $message = 'تم جلب جميع الفئات بنجاح (صلاحية مدير)';
        } else {
            // جلب الفئات التي لها منح دراسية
            $categories = Category::whereHas('scholarships')->get();
            $message = 'تم جلب الفئات التي لها منح دراسية بنجاح';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $categories,
            'is_admin' => $user && $user->role === 'admin',
        ], 200);
    }

    /**
     * إنشاء فئة جديدة (للمدير فقط)
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
            'category_name' => 'required|string|max:30',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة الفئة بنجاح',
            'data' => $category
        ], 201);
    }

    /**
     * عرض فئة محددة
     */
    public function show($id)
    {
        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'الفئة غير موجودة',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب الفئة بنجاح',
            'data' => $category
        ], 200);
    }

    /**
     * تحديث فئة (للمدير فقط)
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

        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'الفئة غير موجودة',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'category_name' => 'required|string|max:30',
        ]);

        $category->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الفئة بنجاح',
            'data' => $category
        ], 200);
    }

    /**
     * حذف فئة (للمدير فقط)
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

        $category = Category::find($id);
        
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'الفئة غير موجودة',
                'data' => null
            ], 404);
        }

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الفئة بنجاح',
            'data' => null
        ], 200);
    }
}