<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $table = 'user_prefrences';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['user_id', 'country_id', 'specialization_id', 'degree', 'faviorate_scholarship_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id', 'id');
    }

    public function favoriteScholarship()
    {
        return $this->belongsTo(FavoriteScholarship::class, 'faviorate_scholarship_id', 'id');
    }
}
