<?php

use Illuminate\Http\Request;

use App\Http\Controllers\ScholarshipController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\FavoriteScholarshipController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CvController;
use App\Http\Controllers\NotificationController;
use App\Events\TestNotification;

use Barryvdh\DomPDF\Facade\Pdf;
// use App\Models\Category;

use App\Http\Controllers\AdminStatsController;

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/admin/stats', [AdminStatsController::class, 'index']);
});


/*
|--------------------------------------------------------------------------
| مسارات عامة (لا تحتاج توثيق)
|--------------------------------------------------------------------------
*/
Route::get('/hello', function () {
    return response()->json(['message' => 'Hello, World!']);
});

// تسجيل الدخول وإنشاء حساب
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// عرض المنح للجميع (ضيوف ومستخدمين)
Route::get('/scholarships', [ScholarshipController::class, 'index']);
Route::get('/scholarships/{id}', [ScholarshipController::class, 'show']);
Route::get('/top-scholarships', [ScholarshipController::class, 'getTopScholarships']);
Route::get('/scholarships/{id}/similar', [ScholarshipController::class, 'getSimilarScholarships']);
Route::get('/scholarships/country/{countryId}', [ScholarshipController::class, 'getByCountry']);


Route::get('/test-broadcast', function () {
    event(new TestNotification('هذه رسالة اختبار من Reverb!'));
    return 'تم بث الإشعار!';
});
/*
|--------------------------------------------------------------------------
| مسارات محمية للمستخدمين المسجلين (auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    

    // المستخدم الحالي
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // المفضلة
    Route::post('/favorites/{scholarship}', [FavoriteScholarshipController::class, 'add']);
    Route::delete('/favorites/{scholarship}', [FavoriteScholarshipController::class, 'remove']);
    Route::get('/favorites', [FavoriteScholarshipController::class, 'index']);

    

    // الإشعارات
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::put('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| مسارات خاصة بالأدمن فقط (إدارة البيانات)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // إدارة المنح (إضافة، تعديل، حذف)
    Route::post('/scholarships', [ScholarshipController::class, 'store']);
    Route::put('/scholarships/{id}', [ScholarshipController::class, 'update']);
    Route::delete('/scholarships/{id}', [ScholarshipController::class, 'destroy']);

    // إدارة الدول
    Route::apiResource('countries', CountryController::class)->except(['index', 'show']);
    // إدارة المدن
    Route::apiResource('cities', CityController::class)->except(['index', 'show']);
    // إدارة الفئات
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    // إدارة التخصصات
    Route::apiResource('specializations', SpecializationController::class)->except(['index', 'show']);

    Route::post('/notifications/send-to-all', [NotificationController::class, 'sendToAll']);
});

// ملاحظة: دوال index و show للموارد المذكورة أعلاه قد تم تعريفها ضمن المسارات العامة (أو يمكنك تعريفها بشكل صريح)
// حتى يتمكن الجميع من عرض القوائم والتفاصيل، أضف هذه المسارات خارج مجموعة الأدمن:
Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{id}', [CountryController::class, 'show']);
Route::get('/cities', [CityController::class, 'index']);
Route::get('/cities/{id}', [CityController::class, 'show']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);
Route::get('/specializations', [SpecializationController::class, 'index']);
Route::get('/specializations/{id}', [SpecializationController::class, 'show']);
Route::post('/chat', [ChatController::class, 'handleChat']);
Route::post('/generate-cv', [CvController::class, 'generateCV']);
Route::post('/generate-motivation-letter', [CvController::class, 'generateMotivationLetter']);
Route::post('/generate-recommendation', [CvController::class, 'generateRecommendationLetter']);
Route::get('/test-pdf', function () {
    $pdf = Pdf::loadHTML('<h1>Hello</h1>');
    return $pdf->download('test.pdf');
});
// Route::get('/top-scholarships', [ScholarshipController::class, 'getTopScholarships']);