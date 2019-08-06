<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $primaryKey = 'id_gallery';
    protected $guarded = ['id_gallery'];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class, 'gallery_restaurant', 'id_restaurant');
    }
}
