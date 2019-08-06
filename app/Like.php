<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $primaryKey = 'id_like';
    protected $guarded = ['id_like'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'id_restaurant', 'id_restaurant');
    }
}
