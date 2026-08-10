<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['category_name'];

    // العلاقة مع Specialization
    public function specializations()
    {
        return $this->hasMany(Specialization::class, 'category_id');
    }

    // ====== أضف هذه العلاقة ======
    public function scholarships()
    {
        return $this->hasMany(Scholarship::class, 'category_id');
    }
}