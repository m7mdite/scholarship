<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return response()->json([
            'status' => 'success',
            'message' => 'تم جلب الفئات بنجاح',
            'data' => $categories
        ], 200);
    }



    public function store(Request $request)
    {
      $validated =  $request->validate([
            'category_name' => 'required|string|max:30',
        ]);

        $category= Category::create($validated);
        return response()->json([
            'status' => 'success',
            'message' => 'تمت إضافة الفئة بنجاح',
            'data' => $category
        ], 201);
    }

    public function show($id)
    {
        $category = Category::with('category')->find($id);
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

    

    public function update(Request $request, $id)
    {
        $category =Category::find($id);
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'الفئة غير موجودة',
                'data' => null
            ], 404);
        }
        $request->validate([
            'category_name' => 'required|string|max:30',
        ]);

        $category->update($request->all());
        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديث الفئة بنجاح',
            'data' => $category
        ], 200);
    }

    public function destroy($id)
    {
        $city = Category::find($id);
        if (!$city) {
            return response()->json([
                'status' => 'error',
                'message' => 'الفئة غير موجودة',
                'data' => null
            ], 404);
        }
         return response()->json([
            'status' => 'success',
            'message' => 'تم حذف  بنجاح',
            'data' => null
        ], 200);
    }
}