<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationCriteria extends Model
{
    protected $table = 'application_criterias';
protected $primaryKey = 'id';
public $timestamps = false;
protected $fillable = ['requirment_type', 'application_criteria_value', 'application_criteria_description', 'scholarship_id'];

public function scholarship()
{
    return $this->belongsTo(Scholarship::class, 'scholarship_id', 'id');
}
}
