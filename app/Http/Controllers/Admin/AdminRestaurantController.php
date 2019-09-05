<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\User;
use App\Restaurant;
use Validator;
use Auth;

class AdminRestaurantController extends Controller
{
    public function all()
    {
        $restaurants = Restaurant::with('user')->orderBy('created_at', 'DESC')->paginate(30);

        return $this->sendResponseOkApi($restaurants);
    }

    public function total(Request $request)
    {
        $resActive   = Restaurant::with('user')->where('restaurant_active', '=', 1)->count();
        $resNoActive = Restaurant::with('user')->where('restaurant_active', '=', 0)->count();

        return $this->sendResponseOkApi([
            'resActive' => $resActive,
            'resNoActive' => $resNoActive
        ]);
    }

    public function active($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        if(Auth::user()->id_user === 1) {
            if($restaurant->restaurant_active === 0) {
                $restaurant->update([
                    'restaurant_active' => 1
                ]);
            }else if($restaurant->restaurant_active === 1) {
                $restaurant->update([
                    'restaurant_active' => 0
                ]);
            }else{
                return $this->sendResponseUnproccessApi();
            }
        }else{
            return $this->sendResponseUnauthorizedApi();
        }

        return $this->sendResponseOkApi($restaurant);
    }
}
