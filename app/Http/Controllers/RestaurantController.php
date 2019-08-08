<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Restaurant;
use App\Like;
use Validator;
use Auth;

class RestaurantController extends Controller
{
    public function all ()
    {
        $restaurants = Restaurant::with('user')->paginate(10);
        
        return response()->json($restaurants, 200);
    }

    public function show($restaurant)
    {
        $restaurant = Restaurant::with('user')->where('restaurant_slug', $restaurant)->first();

        $seen = $restaurant->restaurant_seen;
        $restaurant->update([
            'restaurant_seen' => ++$seen
        ]);

        if(!$restaurant) {
            return response()->json([
                'error' => 'Restaurant not found'
            ], 404);
        }

        return response()->json($restaurant, 200);
    }

    public function owner()
    {
        $restaurants = Restaurant::with('user')->where('restaurant_user', Auth::user()->id_user)->get();

        if(!$restaurants) {
            return response()->json([
                'error' => 'Restaurant not found'
            ], 404);
        }

        return response()->json([
            'result' => $restaurants,
            'total' => count($restaurants)
        ], 200);
    }

    public function popular()
    {
        $restaurant = Like::with('restaurant')
                        ->selectRaw("id_restaurant, COUNT(likes.id_restaurant) AS total")
                        ->groupBy("likes.id_restaurant")
                        ->orderBy('total', 'DESC')
                        ->paginate(10);

        if (count($restaurant) == 0) {
            $restaurant = Restaurant::selectRaw("*, COUNT(restaurants.restaurant_seen) AS total")
                        ->groupBy("restaurants.id_restaurant")
                        ->orderBy('total', 'DESC')
                        ->paginate(10);
        }

        return response()->json($restaurant, 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_name' => 'required|max:30',
            'restaurant_owner' => 'required|max:30',
            'restaurant_address' => 'required',
            'restaurant_image' => 'required|max:2000|mimes:jpeg,jpg,png,bmp',
            'restaurant_latitude' => 'required',
            'restaurant_longitude' => 'required',
            'restaurant_description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $restaurant = Restaurant::where('restaurant_name', $request->input('restaurant_name'))->first();

        if ($restaurant !== null) {
            $restaurantSlug = str_slug($restaurant->restaurant_name . ' ' . time(), '-');
        }else{
            $restaurantSlug = str_slug($request->input('restaurant_name'), '-');
        }

        // UPLOAD IMAGE
        $img = $request->file('restaurant_image')->getClientOriginalExtension();
        $img = str_random(30) . '.' . $img;
        $path = "images/resto/";
        $request->file('restaurant_image')->move($path, $img);

        $restaurant = $request->user()->restaurants()->create([
            'restaurant_name' => $request->input('restaurant_name'),
            'restaurant_slug' => $restaurantSlug,
            'restaurant_owner' => $request->input('restaurant_owner'),
            'restaurant_address' => $request->input('restaurant_address'),
            'restaurant_image' => $img,
            'restaurant_latitude' => $request->input('restaurant_latitude'),
            'restaurant_longitude' => $request->input('restaurant_longitude'),
            'restaurant_description' => $request->input('restaurant_description'),
        ]);

        $restaurant->categories()->attach($request->input('restaurant_category'));

        return response()->json([
            'success' => 'Data successfully created',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_name' => 'required',
            'restaurant_owner' => 'required',
            'restaurant_address' => 'required',
            'restaurant_latitude' => 'required',
            'restaurant_longitude' => 'required',
            'restaurant_description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $restaurant = Restaurant::findOrFail($id);

        // check user
        if(Auth::user()->id_user !== $restaurant->restaurant_user) {
            return response()->json(['error' => "Access denied"], 403);
        }

        if(empty($request->file('restaurant_image'))) {
            $img = $restaurant->restaurant_image;
        }else{
            // Save new image
            $img = $request->file('restaurant_image')->getClientOriginalExtension();
            $img = str_random(30) . '.' . $img;
            $path = 'images/resto/';
            $request->file('restaurant_image')->move($path, $img);

            // and delete old image
            $imgDB = explode('/', $restaurant->restaurant_image);
            $imgDB = end($imgDB);

            $path = base_path("public/images/resto/$imgDB");

            if(file_exists($path)) {
                unlink($path);
            }
        }

        $restaurant->update([
            'restaurant_name' => $request->input('restaurant_name'),
            'restaurant_slug' => str_slug($request->input('restaurant_name'), '-'),
            'restaurant_owner' => $request->input('restaurant_owner'),
            'restaurant_address' => $request->input('restaurant_address'),
            'restaurant_image' => $img,
            'restaurant_latitude' => $request->input('restaurant_latitude'),
            'restaurant_longitude' => $request->input('restaurant_longitude'),
            'restaurant_description' => $request->input('restaurant_description'),
        ]);

        return response()->json([
            'success' => 'Data successfully updated',
        ], 200);
    }

    public function delete($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        // check user
        if(Auth::user()->id_user !== $restaurant->restaurant_user) {
            return response()->json(['error' => "Access denied"], 403);
        }
        
        // fetch image name
        $imgDB = explode('/', $restaurant->restaurant_image);
        $imgDB = end($imgDB);

        $path = base_path("public/images/resto/$imgDB");

        if(file_exists($path)) {
            unlink($path);
        }

        $restaurant->categories()->detach();
        $restaurant->delete();

        return response()->json([
            'message' => 'Data successfully delete'
        ]);
    }
}
