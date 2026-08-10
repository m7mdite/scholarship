<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';

    protected $primaryKey = 'id';
    protected $fillable = ['country_name', 'country_rate'];

    public function scholarships()
    {
        return $this->hasMany(Scholarship::class, 'country_id', 'id');
    }
}