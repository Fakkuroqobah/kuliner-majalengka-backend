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
    public function all()
    {
        $ratings = Rating::with('user', 'restaurant')->whereHas('restaurant', function($q) {
            $q->where('restaurants.restaurant_user', '=', Auth::user()->id_user);
        })->paginate(5);

        if(!$ratings) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi($ratings);
    }

    public function show($restaurant)
    {
        $ratings = Rating::with('user', 'restaurant')->whereHas('restaurant', function($q) use($restaurant) {
            $q->where('restaurants.restaurant_slug', '=', "$restaurant");
        })->get();

        if(!$ratings) return $this->sendResponseNotFoundApi();

        if (count($ratings) > 0) {
            foreach ($ratings as $value) {
                $result  = ( count($ratings) / $value->rating_value ) * 100;
                $value = doubleval(substr($result, 0,3));
            }
        }else{
            $value = "0";
        }

        return $this->sendResponseOkApi([
            'result' => $ratings,
            'total' => count($ratings),
            'value' => $value
        ]);
    }

    public function owner()
    {
        $ratings = Rating::with('user', 'restaurant')->where('rating_user', '=', Auth::user()->id_user)->get();

        if(!$ratings) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'result' => $ratings,
            'total' => count($ratings)
        ]);
    }

    public function create(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating_value' => 'required',
            'rating_comment' => 'required',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        $restaurant = Restaurant::findOrFail($id);

        if(!$restaurant) return $this->sendResponseNotFoundApi();

        $user = $request->user()->id_user;

        $rating = Rating::with('restaurant')->where('ratings.rating_restaurant', '=', "$id")
                      ->where('ratings.rating_user', '=', "$user")
                      ->get();

        if(!$rating) return $this->sendResponseNotFoundApi();

        if ($rating->toArray() !== []) return $this->sendResponseForbiddenApi('you have already rating');

        $create = $request->user()->ratings()->create([
            'rating_value' => $request->input('rating_value'),
            'rating_comment' => $request->input('rating_comment'),
            'rating_restaurant' => $restaurant->id_restaurant,
        ]);

        if(!$create) return $this->sendResponseBadRequestApi();

        return $this->sendResponseCreatedApi();
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating_value' => 'required',
            'rating_comment' => 'required',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        $rating = Rating::findOrFail($id);

        if(!$rating) return $this->sendResponseNotFoundApi();

        // check user
        if(Auth::user()->id_user !== $rating->rating_user) return $this->sendResponseForbiddenApi();

        $update = $rating->update([
            'rating_value' => $request->input('rating_value'),
            'rating_comment' => $request->input('rating_comment'),
        ]);

        if(!$update) return $this->sendResponseBadRequestApi();

        return $this->sendResponseUpdatedApi();
    }

    public function delete($id)
    {
        $rating = Rating::findOrFail($id);

        if(!$rating) return $this->sendResponseNotFoundApi();

        // check user
        if(Auth::user()->id_user !== $rating->rating_user) return $this->sendResponseForbiddenApi();
        
        $delete = $rating->delete();

        if(!$delete) return $this->sendResponseBadRequestApi();

        return $this->sendResponseDeletedApi();
    }
}
