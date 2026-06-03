<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'scholarship_id',
        'reviewer_name',
        'review',
        'rating',
    ];

    public function scholarship()
    {
        return $this->belongsTo(Scholarship::class);
    }
}