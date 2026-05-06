<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['city_name', 'country_id'];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
