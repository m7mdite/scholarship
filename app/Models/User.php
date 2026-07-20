<?php

namespace App\Models;

/** 
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Scholarship[] $favoriteScholarships
 */
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Notification;

use Database\Factories\UserFactory;
use Illuminate\Auth\Authenticatable as AuthAuthenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Notification[] $notifications
 */

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, AuthAuthenticatable, HasApiTokens;




    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password;
    }
    public function getEmailForPasswordReset()
    {
        return $this->email;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // public function favoriteScholarships()
    // {
    //     return $this->belongsToMany(Scholarship::class, 'favorite_scholarships', 'user_id', 'scholarship_id');
    // }
    // public function favoriteScholarships()
    // {
    //     return $this->belongsToMany(Scholarship::class, 'favorite_scholarships', 'user_id', 'scholarship_id');
    //         // ->withTimestamps();
    // }

    public function favoriteScholarships()
    {
        return $this->belongsToMany(Scholarship::class, 'favorite_scholarships', 'user_id', 'scholarship_id')
            ->withPivot('created_at', 'updated_at')
            ->withTimestamps('created_at', 'updated_at');
    }
    
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
