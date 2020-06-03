<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Restaurant;
use App\Menu;
use Validator;
use Auth;

class MenuController extends Controller
{
    public function all ()
    {
        $menus = Menu::with('restaurant')->paginate(2);

        foreach ($menus as $menu) {
            if($menu->menu_favorite == 1){
                $menu->menu_favorite = true;
            }else{
                $menu->menu_favorite = false;
            }
        }
        
        return $this->sendResponseOkApi($menus);
    }

    public function owner()
    {
        $menus = Menu::with('restaurant')->whereHas('restaurant', function($q) {
            $q->where('restaurants.restaurant_user', '=', Auth::user()->id_user);
        })->paginate(16);

        if(!$menus) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi($menus);
    }

    public function index($restaurant)
    {
        $menus = Menu::with('restaurant')->whereHas('restaurant', function($q) use($restaurant) {
            $q->where('restaurants.restaurant_slug', '=', "$restaurant");
        })->get();

        if(!$menus) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'result' => $menus,
            'total' => count($menus)
        ]);
    }

    public function show($restaurant, $menu)
    {
        $menu = Menu::with('restaurant')->where('menu_slug', $menu)->whereHas('restaurant', function($q) use($restaurant) {
            $q->where('restaurants.restaurant_slug', '=', "$restaurant");
        })->get();

        if(!$menu) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi($menu[0]);
    }

    public function search(Request $request)
    {
        $key = $request->input('key');
        $menus = Menu::with('restaurant')->where('menu_name', 'LIKE', "%$key%")->whereHas('restaurant', function($q) {
            $q->where('restaurants.restaurant_user', '=', Auth::user()->id_user);
        })->paginate(8);

        return $this->sendResponseOkApi($menus,);
    }

    public function create(Request $request)
    {
        // VALIDATION
        $validator = Validator::make($request->all(), [
            'menu_name' => 'required',
            'menu_price' => 'required',
            'menu_image' => 'required|max:2000|mimes:jpeg,jpg,png,bmp',
            'menu_info' => 'required',
            'menu_restaurant' => 'required',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        // CREATE SLUG
        $menu = Menu::where('menu_name', $request->input('menu_name'))->first();

        if ($menu !== null) {
            $menuSlug = str_slug($menu->menu_name . ' ' . time(), '-');
        }else{
            $menuSlug = str_slug($request->input('menu_name'), '-');
        }

        // UPLOAD IMAGE
        $img = $request->file('menu_image')->getClientOriginalExtension();
        $img = str_random(30) . '.' . $img;
        $path = "images/menus/";
        $request->file('menu_image')->move($path, $img);

        // INSERT
        $create = Menu::create([
            'menu_name' => $request->input('menu_name'),
            'menu_slug' => $menuSlug,
            'menu_price' => $request->input('menu_price'),
            'menu_image' => $img,
            'menu_info' => $request->input('menu_info'),
            'menu_favorite' => $request->input('menu_favorite'),
            'menu_restaurant' => $request->input('menu_restaurant'),
        ]);

        if(!$create) return $this->sendResponseBadRequestApi();

        return $this->sendResponseCreatedApi();
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'menu_name' => 'required',
            'menu_price' => 'required',
            'menu_info' => 'required',
            'menu_image' => 'sometimes|mimes:jpeg,jpg,png,bmp|max:2000',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        $menu = Menu::findOrFail($id);

        if(!$menu) return $this->sendResponseNotFoundApi();

        // check user
        $id_menu = Menu::with('restaurant')->where('menus.id_menu', '=', $id)->get();

        if(!$id_menu) return $this->sendResponseNotFoundApi();

        foreach ($id_menu as $id_user) {
            if (Auth::user()->id_user !== $id_user->restaurant->restaurant_user) return $this->sendResponseForbiddenApi();
        }

        // UPLOAD IMAGE
        if(empty($request->file('menu_image'))) {
            $img = $menu->menu_image;
        }else{
            // Save new image
            $img = $request->file('menu_image')->getClientOriginalExtension();
            $img = str_random(30) . '.' . $img;
            $path = 'images/menus/';
            $request->file('menu_image')->move($path, $img);

            // and delete old image
            $imgDB = explode('/', $menu->menu_image);
            $imgDB = end($imgDB);

            $path = base_path("public/images/menus/$imgDB");

            if(file_exists($path)) {
                unlink($path);
            }
        }

        // UPDATE
        $update = $menu->update([
            'menu_name' => $request->input('menu_name'),
            'menu_slug' => str_slug($request->input('menu_name'), '-'),
            'menu_price' => $request->input('menu_price'),
            'menu_image' => $img,
            'menu_info' => $request->input('menu_info'),
            'menu_favorite' => $request->input('menu_favorite'),
        ]);

        if(!$update) return $this->sendResponseBadRequestApi();

        return $this->sendResponseUpdatedApi();
    }

    public function delete($id)
    {
        $menu = Menu::findOrFail($id);

        if(!$menu) return $this->sendResponseNotFoundApi();

        // check user
        $id_menu = Menu::with('restaurant')->where('menus.id_menu', '=', $id)->get();

        if(!$id_menu) return $this->sendResponseNotFoundApi();

        foreach ($id_menu as $id_user) {
            if (Auth::user()->id_user !== $id_user->restaurant->restaurant_user) return $this->sendResponseForbiddenApi();
        }

        // DELETE
        $delete = $menu->delete();

        if(!$delete) return $this->sendResponseBadRequestApi();

        // fetch image name
        $imgDB = explode('/', $menu->menu_image);
        $imgDB = end($imgDB);

        $path = base_path("public/images/menus/$imgDB");

        if(file_exists($path)) {
            unlink($path);
        }

        return $this->sendResponseDeletedApi();
    }
}
