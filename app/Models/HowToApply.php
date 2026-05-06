<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HowToApply extends Model
{
    protected $table = 'how_to_applies';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['how_to_apply_description', 'id'];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class, 'scholarship_id', 'id');
    }
}
