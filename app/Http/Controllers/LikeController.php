<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Restaurant;
use App\Like;
use App\User;
use Auth;

class LikeController extends Controller
{
    public function show($restaurant)
    {
        $like = Like::with('user', 'restaurant')->whereHas('restaurant', function($q) use($restaurant) {
            $q->where('restaurants.restaurant_slug', '=', "$restaurant");
        })->get();

        if(!$like) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'result' => $like,
            'total' => count($like)
        ]);
    }

    public function owner()
    {
        $likes = Like::with('user', 'restaurant')->where('id_user', '=', Auth::user()->id_user)->get();

        if(!$like) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'result' => $like,
            'total' => count($like)
        ]);
    }

    public function create(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        
        if(!$restaurant) return $this->sendResponseNotFoundApi();

        $user = $request->user()->id_user;

        $like = Like::with('restaurant')->where('likes.id_restaurant', '=', "$id")
                                        ->where('likes.id_user', '=', "$user")
                                        ->get();

        if(!$like) return $this->sendResponseNotFoundApi();

        if ($like->toArray() !== []) return $this->sendResponseForbiddenApi('you have already liked');

        $create = $request->user()->likes()->create([
            'id_restaurant' => $restaurant->id_restaurant
        ]);

        if(!$create) return $this->sendResponseBadRequestApi();

        return $this->sendResponseCreatedApi();
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user()->id_user;

        $like = Like::where('id_like', '=', "$id")
                      ->where('id_user', '=', "$user")
                      ->delete();

        if(!$like) return $this->sendResponseForbiddenApi();

        return $this->sendResponseDeletedApi();
    }
}
