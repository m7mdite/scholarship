<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $table = 'scholarships';
    protected $primaryKey = 'id';
    public $timestamps = false;
    const CREATED_AT = 'scholarship_created_at';

    protected $fillable = [
        'scholarship_name',
        'degree',
        'finance',
        'scholarship_description',
        'donar',
        'finished_date',
        'start_date',
        'scholarship_created_at',
        'scholarship_language',
        'country_id',
        'city_id',
        'specialization_id',
        'category_id',
    ];

    
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }
    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'scholarship_id', 'id');
    }
    public function photos()
    {
        return $this->hasMany(Photo::class, 'scholarship_id', 'id');
    }
    public function howToApply()
    {
        return $this->hasOne(HowToApply::class, 'scholarship_id', 'id');
    }
    public function applicationCriteria()
    {
        return $this->hasMany(ApplicationCriteria::class, 'scholarship_id', 'id');
    }
    public function favoriteByUsers()
    {
        return $this->hasMany(FavoriteScholarship::class, 'scholarship_id', 'id');
    }
    public function personalExperiences()
    {
        return $this->hasMany(PersonalExperience::class, 'scholarship_id', 'id');
    }
}
