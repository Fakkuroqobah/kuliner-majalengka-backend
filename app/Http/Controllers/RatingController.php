<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Restaurant;
use App\Rating;
use App\User;
use Validator;
use Auth;

class RatingController extends Controller
{
    public function show ($restaurant)
    {
        $ratings = Rating::with('user', 'restaurant')->whereHas('restaurant', function($q) use($restaurant) {
            $q->where('restaurants.restaurant_slug', '=', "$restaurant");
        })->get();

        if (count($ratings) > 0) {
            foreach ($ratings as $value) {
                $result  = ( count($ratings) / $value->rating_value ) * 100;
                $value = substr($result, 0,4);
            }
        }else{
            $value = "0";
        }

        return response()->json([
            'result' => $ratings,
            'total' => count($ratings),
            'value' => $value
        ], 200);
    }

    public function owner()
    {
        $ratings = Rating::with('user', 'restaurant')->where('rating_user', Auth::user()->id_user)->get();

        if(!$ratings) {
            return response()->json([
                'error' => 'Rating not found'
            ], 404);
        }

        return response()->json([
            'result' => $ratings,
            'total' => count($ratings)
        ], 200);
    }

    public function create(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating_value' => 'required',
            'rating_comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $restaurant = Restaurant::findOrFail($id);
        $user = $request->user()->id_user;

        $rating = Rating::with('restaurant')->where('ratings.rating_restaurant', '=', "$id")
                      ->where('ratings.rating_user', '=', "$user")
                      ->get();

        if ($rating->toArray() !== []) {
            return response()->json([
                'error' => 'you have already rating'
            ], 401);
        }

        $request->user()->ratings()->create([
            'rating_value' => $request->input('rating_value'),
            'rating_comment' => $request->input('rating_comment'),
            'rating_restaurant' => $restaurant->id_restaurant,
        ]);

        return response()->json([
            'success' => 'Data successfully created',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating_value' => 'required',
            'rating_comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $rating = Rating::findOrFail($id);

        // check user
        if(Auth::user()->id_user !== $rating->rating_user) {
            return response()->json(['error' => "Access denied"], 403);
        }

        $rating->update([
            'rating_value' => $request->input('rating_value'),
            'rating_comment' => $request->input('rating_comment'),
        ]);

        return response()->json([
            'success' => 'Data successfully updated',
        ], 200);
    }

    public function delete($id)
    {
        $rating = Rating::findOrFail($id);

        // check user
        if(Auth::user()->id_user !== $rating->rating_user) {
            return response()->json(['error' => "Access denied"], 403);
        }
        
        $rating->delete();

        return response()->json([
            'success' => 'Data successfully delete'
        ]);
    }
}
