<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CategoryRestaurant;

class CategoryRestaurantController extends Controller
{
    public function index($id)
    {
        $categoryRestaurant = CategoryRestaurant::where('id_restaurant', $id)->get();

        return response()->json($categoryRestaurant[0]);
    }
}
