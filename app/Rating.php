<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $primaryKey = 'id_rating';
    protected $guarded = ['id_rating'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'rating_restaurant', 'id_restaurant');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'rating_user', 'id_user');
    }
}
