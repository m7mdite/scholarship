<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CityController extends Controller
{
    // جلب جميع المدن
    public function index()
    {
        $cities = City::with('country')->get();
        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب المدن بنجاح',
            'data' => $cities
        ], 200);
    }

    // إنشاء مدينة جديدة
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

    // عرض مدينة محددة
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

    // تحديث مدينة
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

    // حذف مدينة
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
