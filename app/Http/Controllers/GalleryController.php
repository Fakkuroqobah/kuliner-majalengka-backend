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

        return $this->sendResponseOkApi($galleries);
    }

    public function index($restaurant)
    {
        $galleries = Gallery::with('restaurant')->whereHas('restaurant', function($q) use($restaurant) {
            $q->where('restaurants.restaurant_slug', '=', "$restaurant");
        })->get();

        if(!$galleries) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'result' => $galleries,
            'total' => count($galleries)
        ]);
    }

    public function show($id)
    {
        $galleries = Gallery::where('id_gallery', '=', $id)->get();

        // if(count($galleries) === 0) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'result' => $galleries,
            'total' => count($galleries)
        ]);
    }

    public function owner()
    {
        $galleries = Gallery::with('restaurant')->whereHas('restaurant', function($q) {
            $q->where('restaurants.restaurant_user', '=', Auth::user()->id_user);
        })->orderBy('created_at', 'ASC')->paginate(8);

        if(!$galleries) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi($galleries);
    }

    public function search(Request $request)
    {
        $key = $request->input('key');
        $galleries = Gallery::with('restaurant')->where('gallery_info', 'LIKE', "%$key%")->whereHas('restaurant', function($q) {
            $q->where('restaurants.restaurant_user', '=', Auth::user()->id_user);
        })->paginate(8);

        return $this->sendResponseOkApi($galleries);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gallery_image' => 'required|max:2000|mimes:jpeg,jpg,png,bmp',
            'gallery_info' => 'required',
            'gallery_copyright' => 'required',
            'gallery_restaurant' => 'required',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        // UPLOAD IMAGE
        $img = $request->file('gallery_image')->getClientOriginalExtension();
        $img = str_random(30) . '.' . $img;
        $path = "images/galleries/";
        $request->file('gallery_image')->move($path, $img);

        // CREATE
        $create = Gallery::create([
            'gallery_image' => $img,
            'gallery_info' => $request->input('gallery_info'),
            'gallery_copyright' => $request->input('gallery_copyright'),
            'gallery_restaurant' => $request->input('gallery_restaurant'),
        ]);

        if(!$create) return $this->sendResponseBadRequestApi();

        return $this->sendResponseCreatedApi();
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'gallery_info' => 'required',
            'gallery_copyright' => 'required',
            'gallery_restaurant' => 'required',
            'gallery_image' => 'sometimes|mimes:jpeg,jpg,png,bmp|max:2000',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        // check user
        $id_gallery = Gallery::with('restaurant')->where('galleries.id_gallery', '=', $id)->get();
        
        if(!$id_gallery) return $this->sendResponseNotFoundApi();

        foreach ($id_gallery as $id_user) {
            if (Auth::user()->id_user !== $id_user->restaurant->restaurant_user) return $this->sendResponseForbiddenApi();
        }

        $gallery = Gallery::findOrFail($id);

        if(!$gallery) return $this->sendResponseNotFoundApi();

        // UPLOAD IMAGE
        if(empty($request->file('gallery_image'))) {
            $img = $gallery->gallery_image;
        }else{
            // Save new image
            $img = $request->file('gallery_image')->getClientOriginalExtension();
            $img = str_random(30) . '.' . $img;
            $path = 'images/galleries/';
            $request->file('gallery_image')->move($path, $img);

            // and delete old image
            $imgDB = explode('/', $gallery->gallery_image);
            $imgDB = end($imgDB);

            $path = base_path("public/images/galleries/$imgDB");

            if(file_exists($path)) {
                unlink($path);
            }
        }

        // UPDATE
        $update = $gallery->update([
            'gallery_image' => $img,
            'gallery_info' => $request->input('gallery_info'),
            'gallery_copyright' => $request->input('gallery_copyright'),
            'gallery_restaurant' => $request->input('gallery_restaurant'),
        ]);

        if(!$update) return $this->sendResponseBadRequestApi();

        return $this->sendResponseUpdatedApi();
    }

    public function delete($id)
    {
        $gallery = Gallery::findOrFail($id);

        if(!$gallery) return $this->sendResponseNotFoundApi();

        // check user
        $id_gallery = Gallery::with('restaurant')->where('galleries.id_gallery', '=', $id)->get();

        if(!$id_gallery) return $this->sendResponseNotFoundApi();
        
        foreach ($id_gallery as $id_user) {
            if (Auth::user()->id_user !== $id_user->restaurant->restaurant_user) return $this->sendResponseForbiddenApi();
        }

        $delete = $gallery->delete();

        if(!$delete) return $this->sendResponseBadRequestApi();

        // fetch image name
        $imgDB = explode('/', $gallery->gallery_image);
        $imgDB = end($imgDB);

        $path = base_path("public/images/galleries/$imgDB");

        if(file_exists($path)) {
            unlink($path);
        }

        return $this->sendResponseDeletedApi();
    }
}
