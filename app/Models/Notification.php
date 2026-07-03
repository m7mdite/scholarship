<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // العلاقة مع المستخدم
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // تحديد إذا كان الإشعار مقروءاً
    public function isRead()
    {
        return !is_null($this->read_at);
    }

    // تحديد إذا كان الإشعار غير مقروء
    public function isUnread()
    {
        return is_null($this->read_at);
    }

    // نطاق لجلب الإشعارات غير المقروءة
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    // نطاق لجلب الإشعارات المقروءة
    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }
}