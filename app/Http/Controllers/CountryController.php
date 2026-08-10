<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CountryController extends Controller
{
    /**
     * جلب الدول بناءً على صلاحية المستخدم
     * - Admin: جميع الدول
     * - غير Admin: الدول التي لها منح فقط
     */
    public function index()
    {
        $user = Auth::user();

        // إذا كان المستخدم Admin، يجلب جميع الدول
        if ($user && $user->role === 'admin') {
            $countries = Country::all();
            $message = 'تم جلب جميع الدول بنجاح (صلاحية مدير)';
        } else {
            // جلب الدول التي لها منح دراسية
            $countries = Country::whereHas('scholarships')->get();
            $message = 'تم جلب الدول التي لها منح دراسية بنجاح';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $countries,
            'is_admin' => $user && $user->role === 'admin',
        ], 200);
    }

    /**
     * إضافة دولة جديدة (للمدير فقط)
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
            'country_name' => 'required|string|max:30',
            'country_rate' => 'required|numeric',
        ]);

        $country = Country::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة الدولة بنجاح',
            'data' => $country
        ], 201);
    }

    /**
     * عرض دولة محددة
     */
    public function show($id)
    {
        $country = Country::find($id);
        
        if (!$country) {
            return response()->json([
                'status' => 'error',
                'message' => 'الدولة غير موجودة',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب الدولة بنجاح',
            'data' => $country
        ], 200);
    }

    /**
     * تحديث دولة (للمدير فقط)
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

        $country = Country::find($id);
        
        if (!$country) {
            return response()->json([
                'status' => 'error',
                'message' => 'الدولة غير موجودة',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'country_name' => 'sometimes|string|max:30',
            'country_rate' => 'sometimes|numeric',
        ]);

        $country->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الدولة بنجاح',
            'data' => $country
        ], 200);
    }

    /**
     * حذف دولة (للمدير فقط)
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

        $country = Country::find($id);
        
        if (!$country) {
            return response()->json([
                'status' => 'error',
                'message' => 'الدولة غير موجودة',
                'data' => null
            ], 404);
        }

        $country->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الدولة بنجاح',
            'data' => null
        ], 200);
    }
}