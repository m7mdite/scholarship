<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['user_id', 'id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, 'scholarship_id', 'id');
    }
}
