<?php

use App\Http\Controllers\ScholarshipController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\AuthController; 


Route::get('/', function () {
    return view('welcome');
});

Route::resource('scholarships', ScholarshipController::class);
Route::resource('countries', CountryController::class);
Route::resource('cities', CityController::class);
Route::resource('categories', CategoryController::class);
Route::resource('specializations', SpecializationController::class);

// Route::resource('scholarships', ScholarshipController::class);



Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
});
