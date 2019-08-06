<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gallery;
use Validator;
use Auth;

class GalleryController extends Controller
{
    public function all ()
    {
        $galleries = Gallery::with('restaurant')->paginate(10);
        
        return response()->json($galleries, 200);
    }

    public function index($restaurant)
    {
        $galleries = Gallery::with('restaurant')->whereHas('restaurant', function($q) use($restaurant) {
            $q->where('restaurants.restaurant_slug', '=', "$restaurant");
        })->get();

        return response()->json([
            'result' => $galleries,
            'total' => count($galleries)
        ], 200);
    }

    public function owner()
    {
        $galleries = Gallery::with('restaurant')->whereHas('restaurant', function($q) {
            $q->where('restaurants.restaurant_user', '=', Auth::user()->id_user);
        })->get();

        if(!$galleries) {
            return response()->json([
                'error' => 'Gallery not found'
            ], 404);
        }

        return response()->json([
            'result' => $galleries,
            'total' => count($galleries)
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gallery_image' => 'required|max:2000|mimes:jpeg,jpg,png,bmp',
            'gallery_info' => 'required',
            'gallery_copyright' => 'required',
            'gallery_restaurant' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);            
        }

        // UPLOAD IMAGE
        $img = $request->file('gallery_image')->getClientOriginalExtension();
        $img = str_random(30) . '.' . $img;
        $path = "galleries/";
        $request->file('gallery_image')->move($path, $img);

        $imgStore = 'http://localhost:8000/galleries/' . $img;

        Gallery::create([
            'gallery_image' => $imgStore,
            'gallery_info' => $request->input('gallery_info'),
            'gallery_copyright' => $request->input('gallery_copyright'),
            'gallery_restaurant' => $request->input('gallery_restaurant'),
        ]);

        return response()->json([
            'message' => 'Data successfully created',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'gallery_image' => 'max:2000|mimes:jpeg,jpg,png,bmp',
            'gallery_info' => 'required',
            'gallery_copyright' => 'required',
            'gallery_restaurant' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // check user
        $id_gallery = Gallery::with('restaurant')->where('galleries.id_gallery', '=', $id)->get();
        foreach ($id_gallery as $id_user) {
            if (Auth::user()->id_user !== $id_user->restaurant->restaurant_user) {
                return response()->json(['error' => "Access denied"], 403);
            }
        }

        $gallery = Gallery::findOrFail($id);

        if(empty($request->file('gallery_image'))) {
            $imgStore = $gallery->gallery_image;
        }else{
            // Save new image
            $img = $request->file('gallery_image')->getClientOriginalExtension();
            $img = str_random(30) . '.' . $img;
            $path = 'galleries/';
            $request->file('gallery_image')->move($path, $img);

            $imgStore = 'http://localhost:8000/galleries/' . $img;

            // and delete old image
            $imgDB = explode('/', $gallery->gallery_image);
            $imgDB = end($imgDB);

            $path = base_path("public/galleries/$imgDB");

            if(file_exists($path)) {
                unlink($path);
            }
        }

        $gallery->update([
            'gallery_image' => $imgStore,
            'gallery_info' => $request->input('gallery_info'),
            'gallery_copyright' => $request->input('gallery_copyright'),
            'gallery_restaurant' => $request->input('gallery_restaurant'),
        ]);

        return response()->json([
            'message' => 'Data successfully updated',
        ], 200);
    }

    public function delete($id)
    {
        $gallery = Gallery::findOrFail($id);

        // check user
        $id_gallery = Gallery::with('restaurant')->where('galleries.id_gallery', '=', $id)->get();
        foreach ($id_gallery as $id_user) {
            if (Auth::user()->id_user !== $id_user->restaurant->restaurant_user) {
                return response()->json(['error' => "Access denied"], 403);
            }
        }

        // fetch image name
        $imgDB = explode('/', $gallery->gallery_image);
        $imgDB = end($imgDB);

        $path = base_path("public/galleries/$imgDB");

        if(file_exists($path)) {
            unlink($path);
        }

        $gallery->delete();

        return response()->json([
            'message' => 'Data successfully delete'
        ]);
    }
}
