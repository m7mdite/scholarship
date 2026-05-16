<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    // جلب جميع الدول
    public function index()
    {
        $countries = Country::all();
        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب الدول بنجاح',
            'data' => $countries
        ], 200);
    }

    // إضافة دولة جديدة
    public function store(Request $request)
    {
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

    // عرض دولة محددة
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

    // تحديث دولة
    public function update(Request $request, $id)
    {
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

    // حذف دولة
    public function destroy($id)
    {
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