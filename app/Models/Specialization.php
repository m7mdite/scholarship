<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specialization extends Model
{
    protected $table = 'specializations';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['specialization_name', 'category_id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
}
