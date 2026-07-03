<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VisitorTracker
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $todayKey = 'visitors_today_' . now()->toDateString();
        $allKey = 'all_visitors';      // لتخزين جميع عناوين IP الفريدة (عبر كل الأيام)
        $totalKey = 'total_visitors';   // العداد الإجمالي

        // ----- تحديث زوار اليوم -----
        $todayIps = Cache::get($todayKey, []);
        if (!in_array($ip, $todayIps)) {
            $todayIps[] = $ip;
            // تنتهي صلاحية الكاش في نهاية اليوم
            Cache::put($todayKey, $todayIps, now()->endOfDay());
        }

        // ----- تحديث الزوار الإجماليين (مرة واحدة لكل IP فريد) -----
        $allIps = Cache::get($allKey, []);
        if (!in_array($ip, $allIps)) {
            $allIps[] = $ip;
            // تخزين دائم (أو لمدة سنة مثلاً)
            Cache::forever($allKey, $allIps);
            // زيادة العداد الإجمالي
            $total = Cache::get($totalKey, 0);
            Cache::forever($totalKey, $total + 1);
        }

        return $next($request);
    }
}