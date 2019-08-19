<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'id_category';
    protected $guarded = ['id_category'];

    public function user()
    {
        return $this->belongsTo(User::class, 'category_user', 'id_user');
    }

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'category_restaurant', 'id_category', 'id_restaurant');
    }
}
