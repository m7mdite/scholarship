<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;


class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // افترض أن المستخدم لديه عمود 'role' في جدول users
        if (auth()->check() && auth()->user()->role === 'admin') {
            return $next($request);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات الأدمن.',
            'data' => null
        ], 403);
    }
}
