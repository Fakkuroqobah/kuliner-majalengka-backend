<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $primaryKey = 'id_menu';
    protected $guarded = ['id_menu'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'menu_restaurant', 'id_restaurant');
    }
}
