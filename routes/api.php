<?php

use Illuminate\Http\Request;

use App\Http\Controllers\ScholarshipController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SpecializationController;
// use App\Models\Category;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello, World!']);
});

Route::middleware('guest')->group(function () {
    // تسجيل المستخدمين الجدد
    Route::post('/register', [AuthController::class, 'register']);
    // تسجيل الدخول
    Route::post('/login', [AuthController::class, 'login']);
    // جلب أول 15 منحة
    Route::get('/top-scholarships', [ScholarshipController::class, 'getTopScholarships']);
});


// Route::middleware('admin')->group(function () {
//     Route::apiResource('cities', CityController::class);
//     Route::apiResource('countries', CountryController::class);
//     Route::apiResource('category', CategoryController::class);
// });

Route::apiResource('cities', CityController::class);
Route::apiResource('countries', CountryController::class);
Route::apiResource('categories', CategoryController::class);
Route::apiResource('specializations', SpecializationController::class);
Route::apiResource('scholarships', ScholarshipController::class);






Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});



