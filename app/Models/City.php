<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ['city_name', 'country_id'];

    // العلاقة مع Country
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    // ====== أضف هذه العلاقة ======
    public function scholarships()
    {
        return $this->hasMany(Scholarship::class, 'city_id');
    }
}