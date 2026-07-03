<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class NotificationController extends Controller
{
    // جلب جميع إشعارات المستخدم الحالي
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        // عدد الإشعارات غير المقروءة
        $unreadCount = $user->notifications()->unread()->count();
        
        // جلب الإشعارات (أحدثها أولاً)
        $notifications = $user->notifications()
                              ->latest()
                              ->paginate(20);
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'notifications' => $notifications->items(),
                'unread_count' => $unreadCount,
                'total' => $user->notifications()->count(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
            ]
        ]);
    }

    // جلب الإشعارات غير المقروءة فقط
    public function unread(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        
        $notifications = $user->notifications()
                              ->unread()
                              ->latest()
                              ->get();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'notifications' => $notifications,
                'count' => $notifications->count()
            ]
        ]);
    }

    // تحديد إشعار كمقروء
    public function markAsRead($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'الإشعار غير موجود'
            ], 404);
        }
        
        $notification->update(['read_at' => now()]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديد الإشعار كمقروء'
        ]);
    }

    // تحديد جميع الإشعارات كمقروءة
    public function markAllAsRead()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $user->notifications()->unread()->update(['read_at' => now()]);
        
        return response()->json([
            'status' => 'success',
            'message' => 'تم تحديد جميع الإشعارات كمقروءة'
        ]);
    }

    // حذف إشعار
    public function destroy($id)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if (!$notification) {
            return response()->json([
                'status' => 'error',
                'message' => 'الإشعار غير موجود'
            ], 404);
        }
        
        $notification->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'تم حذف الإشعار'
        ]);
    }

    // (دالة مساعدة) إنشاء إشعار جديد (سيتم استدعاؤها من أماكن مختلفة)
    public static function create($userId, $type='info', $title, $body, $data = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
        ]);
    }


    
/**
 * إرسال إشعار لجميع المستخدمين (للأدمن فقط)
 */
public function sendToAll(Request $request)
{
    // التحقق من أن المستخدم الحالي هو أدمن
    $admin = Auth::user();
    if ($admin->role !== 'admin') {
        return response()->json([
            'status' => 'error',
            'message' => 'غير مصرح. هذه العملية تتطلب صلاحيات المدير.',
        ], 403);
    }

    // التحقق من البيانات المرسلة
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'body' => 'required|string|max:1000',
        'type' => 'nullable|in:info,success,warning,error',
    ]);

    // جلب جميع المستخدمين (أو المستخدمين العاديين فقط)
    $users = User::where('role', 'user')->get(); // أو User::all() إذا أردت إرسال للأدمن أيضاً

    if ($users->isEmpty()) {
        return response()->json([
            'status' => 'error',
            'message' => 'لا يوجد مستخدمين لإرسال الإشعار لهم.',
        ], 404);
    }

    $count = 0;
    foreach ($users as $user) {
        self::create(
            $user->id,
            $validated['type'] ?? 'info',
            $validated['title'],
            $validated['body'],
            null // يمكن إضافة بيانات إضافية هنا إذا أردت
        );
        $count++;
    }

    return response()->json([
        'status' => 'success',
        'message' => "تم إرسال الإشعار إلى {$count} مستخدم بنجاح.",
        'data' => [
            'sent_to' => $count,
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]
    ], 201);
}
}