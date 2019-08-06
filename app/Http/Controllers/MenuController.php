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
        $menus = Menu::with('restaurant')->paginate(10);

        foreach ($menus as $menu) {
            if($menu->menu_favorite == 1){
                $menu->menu_favorite = true;
            }else{
                $menu->menu_favorite = false;
            }
        }
        
        return response()->json($menus, 200);
    }

    public function owner()
    {
        $menus = Menu::with('restaurant')->whereHas('restaurant', function($q) {
            $q->where('restaurants.restaurant_user', '=', Auth::user()->id_user);
        })->get();

        if(!$menus) {
            return response()->json([
                'error' => 'Menu not found'
            ], 404);
        }

        return response()->json([
            'result' => $menus,
            'total' => count($menus)
        ], 200);
    }

    public function index($restaurant)
    {
        $menus = Menu::with('restaurant')->whereHas('restaurant', function($q) use($restaurant) {
            $q->where('restaurants.restaurant_slug', '=', "$restaurant");
        })->get();

        return response()->json([
            'result' => $menus,
            'total' => count($menus)
        ], 200);
    }

    public function show($restaurant, $menu)
    {
        $menu = Menu::with('restaurant')->where('menu_slug', $menu)->whereHas('restaurant', function($q) use($restaurant) {
            $q->where('restaurants.restaurant_slug', '=', "$restaurant");
        })->get();

        return response()->json($menu[0], 200);
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

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);            
        }

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
        $store = Menu::create([
            'menu_name' => $request->input('menu_name'),
            'menu_slug' => $menuSlug,
            'menu_price' => $request->input('menu_price'),
            'menu_image' => $img,
            'menu_info' => $request->input('menu_info'),
            'menu_favorite' => $request->input('menu_favorite'),
            'menu_restaurant' => $request->input('menu_restaurant'),
        ]);

        return response()->json([
            'message' => 'Data successfully created',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'menu_image' => 'max:2000|mimes:jpeg,jpg,png,bmp',
            'menu_name' => 'required',
            'menu_price' => 'required',
            'menu_info' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $menu = Menu::findOrFail($id);

        // check user
        $id_menu = Menu::with('restaurant')->where('menus.id_menu', '=', $id)->get();
        foreach ($id_menu as $id_user) {
            if (Auth::user()->id_user !== $id_user->restaurant->restaurant_user) {
                return response()->json(['error' => "Access denied"], 403);
            }
        }

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

        $menu->update([
            'menu_name' => $request->input('menu_name'),
            'menu_slug' => str_slug($request->input('menu_name'), '-'),
            'menu_price' => $request->input('menu_price'),
            'menu_image' => $img,
            'menu_info' => $request->input('menu_info'),
            'menu_favorite' => $request->input('menu_favorite'),
        ]);

        return response()->json([
            'message' => 'Data successfully updated',
        ], 200);
    }

    public function delete($id)
    {
        $menu = Menu::findOrFail($id);

        // check user
        $id_menu = Menu::with('restaurant')->where('menus.id_menu', '=', $id)->get();
        foreach ($id_menu as $id_user) {
            if (Auth::user()->id_user !== $id_user->restaurant->restaurant_user) {
                return response()->json(['error' => "Access denied"], 403);
            }
        }

        // fetch image name
        $imgDB = explode('/', $menu->menu_image);
        $imgDB = end($imgDB);

        $path = base_path("public/images/menus/$imgDB");

        if(file_exists($path)) {
            unlink($path);
        }

        $menu->delete();

        return response()->json([
            'message' => 'Data successfully delete'
        ]);
    }
}
