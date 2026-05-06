<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteScholarship extends Model
{
    protected $table = 'favorite_scholarships';
protected $primaryKey = 'id';
public $timestamps = false;
protected $fillable = ['scholarship_id', 'user_id'];

public function scholarship()
{
    return $this->belongsTo(Scholarship::class, 'scholarship_id', 'id');
}

public function user()
{
    return $this->belongsTo(User::class, 'user_id', 'id');
}
}
