<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use App\Models\Scholarship;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CityController extends Controller
{
    /**
     * جلب المدن بناءً على صلاحية المستخدم
     * - Admin: جميع المدن
     * - غير Admin: المدن التي لها منح فقط
     */
    public function index()
    {
        $user = Auth::user();

        // إذا كان المستخدم Admin، يجلب جميع المدن
        if ($user && $user->role === 'admin') {
            $cities = City::with('country')->get();
            $message = 'تم جلب جميع المدن بنجاح (صلاحية مدير)';
        } else {
            // جلب المدن التي لها منح دراسية (منح نشطة أو غير منتهية)
            $cities = City::whereHas('scholarships', function ($query) {
                // اختياري: يمكنك إضافة شرط لجلب المدن التي لها منح نشطة فقط
                // $query->where('finished_date', '>=', now());
            })->with('country')->get();

            $message = 'تم جلب المدن التي لها منح دراسية بنجاح';
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $cities,
            'is_admin' => $user && $user->role === 'admin', // اختياري: لتوضيح نوع البيانات
        ], 200);
    }

    // ====== باقي الدوال (store, show, update, destroy) بدون تغيير ======

    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
                'data' => null
            ], 403);
        }

        $validated = $request->validate([
            'city_name' => 'required|string|max:25',
            'country_id' => 'required|exists:countries,id',
        ]);

        $city = City::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة المدينة بنجاح',
            'data' => $city->load('country')
        ], 201);
    }

    public function show($id)
    {
        $city = City::with('country')->find($id);
        if (!$city) {
            return response()->json([
                'status' => 'error',
                'message' => 'المدينة غير موجودة',
                'data' => null
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المدينة بنجاح',
            'data' => $city
        ], 200);
    }

    public function update(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
                'data' => null
            ], 403);
        }

        $city = City::find($id);
        if (!$city) {
            return response()->json([
                'status' => 'error',
                'message' => 'المدينة غير موجودة',
                'data' => null
            ], 404);
        }

        $validated = $request->validate([
            'city_name' => 'sometimes|string|max:25',
            'country_id' => 'sometimes|exists:countries,id',
        ]);

        $city->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث المدينة بنجاح',
            'data' => $city->load('country')
        ], 200);
    }

    public function destroy($id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
                'data' => null
            ], 403);
        }

        $city = City::find($id);
        if (!$city) {
            return response()->json([
                'status' => 'error',
                'message' => 'المدينة غير موجودة',
                'data' => null
            ], 404);
        }

        $city->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف المدينة بنجاح',
            'data' => null
        ], 200);
    }
}