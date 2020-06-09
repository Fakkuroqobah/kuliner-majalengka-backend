<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasApiTokens;

    protected $primaryKey = 'id_user';

    protected $guarded = ['id_user'];

    protected $hidden = [
        'user_password', 'user_level', 'updated_at', 'remember_token'
    ];

    // JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthPassword()
    {
        return $this->user_password;
    }

    // OAUTH
    // public function findForPassport($username) {
    //     return $this->where('user_email', $username)->first();
    // }

    // public function validateForPassportPasswordGrant($password)
    // {
    //     return Hash::check($password, $this->user_password);
    // }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'restaurant_user');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'category_user');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'rating_user');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'id_user');
    }
}
