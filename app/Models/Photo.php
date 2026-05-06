<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = 'photos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['image_path', 'city_id', 'id'];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, 'scholarship_id', 'id');
    }
}
