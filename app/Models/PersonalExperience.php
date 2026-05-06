<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonalExperience extends Model
{
    protected $table = 'personal_experiences';
    protected $primaryKey = 'id'; 
    public $timestamps = false;
    protected $fillable = ['personal_experiences_description', 'id'];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, 'scholarship_id', 'id');
    }
}
