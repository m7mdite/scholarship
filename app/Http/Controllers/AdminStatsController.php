<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\Country;
use App\Models\Category;
use App\Models\User;
use App\Models\FavoriteScholarship;
use App\Models\UserAction;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AdminStatsController extends Controller
{
    public function index()
    {
        // ====== الزوار ======
        $todayKey = 'visitors_today_' . now()->toDateString();
        $visitorsToday = count(Cache::get($todayKey, []));
        $totalVisitors = Cache::get('total_visitors', 0);

        // ====== الإحصائيات الأساسية ======
        $totalScholarships = Scholarship::count();
        $totalCountries = Country::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();

        // ====== المستخدمون ======
        $newUsersToday = User::whereDate('created_at', today())->count();

        // ====== المنح ======
        $activeScholarships = Scholarship::where('finished_date', '>=', now())->count();
        $expiredScholarships = Scholarship::where('finished_date', '<', now())->count();
        $scholarshipsAddedToday = Scholarship::whereDate('created_at', today())->count();

        // ====== المفضلة ======
        $totalFavorites = FavoriteScholarship::count();
        $favoritesToday = FavoriteScholarship::whereDate('created_at', today())->count();

        // ====== أكثر 3 منح تم إضافتها للمفضلة ======
        $topFavorites = FavoriteScholarship::select('scholarship_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('scholarship_id')
            ->orderByDesc('count')
            ->limit(3)
            ->with('scholarship')
            ->get()
            ->map(function ($item) {
                return [
                    'scholarship_id' => $item->scholarship_id,
                    'scholarship_name' => $item->scholarship->scholarship_name ?? 'غير معروف',
                    'count' => $item->count,
                ];
            });

        // ====== أكثر 3 مستخدمين نشطين خلال الأسبوع ======
        // باستخدام updated_at (بدلاً من last_login_at)
        $topActiveUsers = User::where('updated_at', '>=', now()->subDays(7))
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get()
            ->map(function ($user) {
                return [
                    'user_id' => $user->id,
                    'user_name' => $user->name ?? $user->user_name ?? 'مستخدم',
                    'email' => $user->email ?? $user->user_email ?? '',
                    'last_activity' => $user->updated_at ? $user->updated_at->toDateTimeString() : null,
                ];
            });

        // ====== إحصائيات الذكاء الاصطناعي ======
        $aiEnhanceCount = UserAction::where('action', 'ai_enhance_bio')->count();
        $cvGenerateCount = UserAction::where('action', 'generate_cv')->count();
        $motivationGenerateCount = UserAction::where('action', 'generate_motivation')->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                // === الزوار ===
                'visitors_today' => $visitorsToday,
                'total_visitors' => $totalVisitors,

                // === الأساسيات ===
                'total_scholarships' => $totalScholarships,
                'total_countries' => $totalCountries,
                'total_categories' => $totalCategories,
                'total_users' => $totalUsers,

                // === المستخدمون ===
                'new_users_today' => $newUsersToday,

                // === المنح ===
                'active_scholarships' => $activeScholarships,
                'expired_scholarships' => $expiredScholarships,
                'scholarships_added_today' => $scholarshipsAddedToday,

                // === المفضلة ===
                'total_favorites' => $totalFavorites,
                'favorites_today' => $favoritesToday,

                // === أكثر 3 منح تم إضافتها للمفضلة ===
                'top_favorite_scholarships' => $topFavorites,

                // === أكثر 3 مستخدمين نشطين ===
                'top_active_users' => $topActiveUsers,

                // === إحصائيات الذكاء الاصطناعي ===
                'ai_enhance_bio_count' => $aiEnhanceCount,
                'cv_generate_count' => $cvGenerateCount,
                'motivation_generate_count' => $motivationGenerateCount,
            ]
        ]);
    }
}