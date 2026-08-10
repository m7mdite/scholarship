<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['specialization_name', 'category_id'];

    // العلاقة مع Category
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // ====== أضف هذه العلاقة ======
    public function scholarships()
    {
        return $this->hasMany(Scholarship::class, 'specialization_id');
    }
}