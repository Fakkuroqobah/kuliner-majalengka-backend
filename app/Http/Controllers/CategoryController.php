<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use Validator;
use Auth;

class CategoryController extends Controller
{
    public function all ()
    {
        $categories = Category::all();

        return $this->sendResponseOkApi($categories);
    }

    public function owner()
    {
        $categories = Category::with('user')->where('category_user', '=', Auth::user()->id_user)->get();

        if(!$categories) return $this->sendResponseNotFoundApi();

        return $this->sendResponseOkApi([
            'result' => $categories,
            'total' => count($categories)
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'category_image' => 'required|max:2000|mimes:jpeg,jpg,png,bmp',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        // UPLOAD IMAGE
        $img = $request->file('category_image')->getClientOriginalExtension();
        $img = str_random(30) . '.' . $img;
        $path = "images/categories/";
        $request->file('category_image')->move($path, $img);

        // CREATE
        $create = $request->user()->categories()->create([
            'category_name' => $request->input('category_name'),
            'category_image' => $img,
        ]);
        
        if(!$create) return $this->sendResponseBadRequestApi();

        return $this->sendResponseCreatedApi();
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'category_image' => 'sometimes|mimes:jpeg,jpg,png,bmp|max:2000',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        $category = Category::findOrFail($id);

        if(!$category) return $this->sendResponseNotFoundApi();

        // check user
        if(Auth::user()->id_user !== $category->category_user) return $this->sendResponseForbiddenApi();

        // UPLOAD
        if(empty($request->file('category_image'))) {
            $img = $category->category_image;
        }else{
            // Save new image
            $img = $request->file('category_image')->getClientOriginalExtension();
            $img = str_random(30) . '.' . $img;
            $path = 'images/categories/';
            $request->file('category_image')->move($path, $img);

            // and delete old image
            $imgDB = explode('/', $category->category_image);
            $imgDB = end($imgDB);

            $path = base_path("public/images/categories/$imgDB");

            if(file_exists($path)) {
                unlink($path);
            }
        }

        // UPDATE
        $update = $category->update([
            'category_name' => $request->input('category_name'),
            'category_image' => $img,
        ]);

        if(!$update) return $this->sendResponseBadRequestApi();

        return $this->sendResponseUpdatedApi();
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);

        if(!$category) return $this->sendResponseNotFoundApi();

        // check user
        if(Auth::user()->id_user !== $category->category_user) return $this->sendResponseForbiddenApi();

        // DELETE
        $delete = $category->delete();

        if(!$delete) return $this->sendResponseBadRequestApi();
        
        // fetch image name
        $imgDB = explode('/', $category->category_image);
        $imgDB = end($imgDB);

        $path = base_path("public/images/categories/$imgDB");

        if(file_exists($path)) {
            unlink($path);
        }

        return $this->sendResponseDeletedApi();
    }
}
