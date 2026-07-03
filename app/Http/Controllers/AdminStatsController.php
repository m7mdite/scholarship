<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use App\Models\Country;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class AdminStatsController extends Controller
{
    public function index()
    {
        // عدد الزوار اليوميين (من الكاش)
        $todayKey = 'visitors_today_' . now()->toDateString();
        $visitorsToday = count(Cache::get($todayKey, []));

        // عدد الزوار الإجماليين (من الكاش)
        $totalVisitors = Cache::get('total_visitors', 0);

        // الإحصائيات الأخرى
        $totalScholarships = Scholarship::count();
        $totalCountries = Country::count();
        $totalCategories = Category::count();
        $totalUsers = User::count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_scholarships' => $totalScholarships,
                'total_countries' => $totalCountries,
                'total_categories' => $totalCategories,
                'total_users' => $totalUsers,
                'visitors_today' => $visitorsToday,
                'total_visitors' => $totalVisitors,
            ]
        ]);
    }
}