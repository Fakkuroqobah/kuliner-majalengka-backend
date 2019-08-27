<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Restaurant;
use App\Rating;
use App\Like;
use Validator;
use Auth;

class RestaurantController extends Controller
{
    public function all ()
    {
        $restaurants = Restaurant::with('user')->paginate(10);
        
        return $this->sendResponseOkApi($restaurants);
    }

    public function show($restaurant_name)
    {
        $restaurant = Restaurant::with('user')
                                    ->where('restaurant_slug', $restaurant_name)
                                    ->where('restaurant_user', '=', Auth::user()->id_user)->first();
        
        if(!$restaurant) {
            $restaurant = Restaurant::with('user')->where('restaurant_slug', $restaurant_name)->first();

            $seen = $restaurant->restaurant_seen;
            $restaurant->update([
                'restaurant_seen' => ++$seen
            ]);

        }

        return $this->sendResponseOkApi($restaurant);
    }

    public function owner()
    {
        $restaurants = Restaurant::with('user', 'ratings')->where('restaurant_user', '=', Auth::user()->id_user)->paginate(6);

        if(!$restaurants) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi($restaurants);
    }

    public function search(Request $request)
    {
        $key = $request->input('key');
        $restaurants = Restaurant::where('restaurant_name', 'LIKE', "%$key%")->where('restaurant_user', '=', Auth::user()->id_user)->paginate(8);

        return $this->sendResponseOkApi($restaurants);
    }

    public function category($restaurant)
    {
        $restaurants = Restaurant::with('categories')->where('restaurant_slug', '=', $restaurant)->get();

        if(!$restaurants) return $this->sendResponseNotFoundApi();

        $arr = [];
        foreach ($restaurants as $restaurant) {
            foreach ($restaurant['categories'] as $id_category) {
                $arr[] = $id_category['id_category'];
            }
        }

        return $this->sendResponseOkApi([
            'result' => $restaurants,
            'id_category_selected' => $arr,
            'total' => count($restaurants)
        ]);
    }

    public function menu($restaurant)
    {
        $restaurants = Restaurant::with('menus')->where('restaurant_slug', '=', $restaurant)->get();

        if(!$restaurants) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'result' => $restaurants,
            'total' => count($restaurants)
        ]);
    }

    public function gallery($restaurant)
    {
        $restaurants = Restaurant::with('galleries')->where('restaurant_slug', '=', $restaurant)->get();

        if(!$restaurants) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'result' => $restaurants,
            'total' => count($restaurants)
        ]);
    }

    public function like($restaurant)
    {
        $restaurants = Restaurant::with('likes')->where('restaurant_slug', '=', $restaurant)->get();

        if(!$restaurants) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'total' => count($restaurants[0]['likes'])
        ]);
    }

    public function rating()
    {
        $restaurants = Restaurant::with('ratings')->where('restaurant_user', Auth::user()->id_user)->get();

        if(!$restaurants) return $this->sendResponseNotFoundApi();

        if (count($restaurants) > 0) {
            foreach ($restaurants as $rating) {
                $result = 0;
                foreach ($rating['ratings'] as $val) {
                    $result += $val['rating_value'];
                    $total  = ( count($rating['ratings']) / $result ) * 100;
                    $value = doubleval(substr($total, 0,3));
                    $rating['restaurant_rating'] = $value;
                }
            }
        }

        return $this->sendResponseOkApi([
            'result' => $restaurants,
            'total' => count($restaurants),
        ]);
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

        return $this->sendResponseOkApi($restaurant);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_name' => 'required|max:30',
            'restaurant_owner' => 'required|max:30',
            'restaurant_address' => 'required',
            'restaurant_image' => 'required|max:2000|mimes:jpeg,jpg,png,bmp',
            'restaurant_category' => 'required',
            'restaurant_latitude' => 'required',
            'restaurant_longitude' => 'required',
            'restaurant_description' => 'required',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

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

        // INSERT
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

        $restaurant->categories()->attach(explode(',', $request->input('restaurant_category')));

        if(!$restaurant) return $this->sendResponseBadRequestApi();

        return $this->sendResponseCreatedApi();
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
            'restaurant_image' => 'sometimes|mimes:jpeg,jpg,png,bmp|max:2000',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        $restaurant = Restaurant::findOrFail($id);

        if(!$restaurant) return $this->sendResponseNotFoundApi();

        // check user
        if(Auth::user()->id_user !== $restaurant->restaurant_user) return $this->sendResponseForbiddenApi();
        
        // UPLOAD IMAGE
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

        // UPDATE
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

        $restaurant->categories()->sync(explode(',', $request->input('restaurant_category')));

        return $this->sendResponseUpdatedApi();
    }

    public function delete($id)
    {
        $restaurant = Restaurant::findOrFail($id);

        if(!$restaurant) return $this->sendResponseNotFoundApi();

        // check user
        if(Auth::user()->id_user !== $restaurant->restaurant_user) return $this->sendResponseForbiddenApi();
        
        $detach = $restaurant->categories()->detach();

        $delete = $restaurant->delete();
        if(!$delete) return $this->sendResponseBadRequestApi();

        // fetch image name
        $imgDB = explode('/', $restaurant->restaurant_image);
        $imgDB = end($imgDB);

        $path = base_path("public/images/resto/$imgDB");

        if(file_exists($path)) {
            unlink($path);
        }

        return $this->sendResponseDeletedApi();
    }
}
