<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationCriteria extends Model
{
    protected $table = 'application_criterias';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['age', 'gender', 'nationalities', 'scholarship_id'];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, 'scholarship_id', 'id');
    }
}