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

        return response()->json([
            'result' => $like,
            'total' => count($like)
        ], 200);
    }

    public function owner()
    {
        $likes = Like::with('user', 'restaurant')->where('id_user', Auth::user()->id_user)->get();

        if(!$likes) {
            return response()->json([
                'error' => 'Like not found'
            ], 404);
        }

        return response()->json([
            'result' => $likes,
            'total' => count($likes)
        ], 200);
    }

    public function create(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $user = $request->user()->id_user;

        $like = Like::with('restaurant')->where('likes.id_restaurant', '=', "$id")
                                        ->where('likes.id_user', '=', "$user")
                                        ->get();

        if ($like->toArray() !== []) {
            return response()->json([
                'error' => 'you have already liked'
            ], 403);
        }

        $request->user()->likes()->create([
            'id_restaurant' => $restaurant->id_restaurant
        ]);

        return response()->json([
            'success' => 'like'
        ], 200);
    }

    public function delete(Request $request, $id)
    {
        $user = $request->user()->id_user;

        $like = Like::where('id_like', '=', "$id")
                      ->where('id_user', '=', "$user")
                      ->delete();

        if(!$like) return response()->json(['error' => 'Access denied'], 403);

        return response()->json([
            'success' => 'unlike'
        ], 200);
    }
}
