<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

use Laravel\Passport\HasApiTokens;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $primaryKey = 'id_user';

    protected $guarded = ['id_user'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'user_password', 'remember_token'
    ];

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
