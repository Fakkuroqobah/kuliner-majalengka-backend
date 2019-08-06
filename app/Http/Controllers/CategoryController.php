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
        $categories = Category::with('user')->paginate(10);
        
        return response()->json($categories, 200);
    }

    public function owner()
    {
        $categories = Category::with('user')->where('category_user', Auth::user()->id_user)->get();

        if(!$categories) {
            return response()->json([
                'error' => 'category not found'
            ], 404);
        }

        return response()->json([
            'result' => $categories,
            'total' => count($categories)
        ], 200);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
            'category_image' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        // UPLOAD IMAGE
        $img = $request->file('category_image')->getClientOriginalExtension();
        $img = str_random(30) . '.' . $img;
        $path = "categories/";
        $request->file('category_image')->move($path, $img);

        $imgStore = 'http://localhost:8000/categories/' . $img;

        $request->user()->categories()->create([
            'category_name' => $request->input('category_name'),
            'category_image' => $imgStore,
        ]);

        return response()->json([
            'message' => 'Data successfully created',
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'category_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $category = Category::findOrFail($id);

        // check user
        if(Auth::user()->id_user !== $category->category_user) {
            return response()->json(['error' => "Access denied"], 403);
        }

        if(empty($request->file('category_image'))) {
            $imgStore = $category->category_image;
        }else{
            // Save new image
            $img = $request->file('category_image')->getClientOriginalExtension();
            $img = str_random(30) . '.' . $img;
            $path = 'categories/';
            $request->file('category_image')->move($path, $img);

            $imgStore = 'http://localhost:8000/categories/' . $img;

            // and delete old image
            $imgDB = explode('/', $category->category_image);
            $imgDB = end($imgDB);

            $path = base_path("public/categories/$imgDB");

            if(file_exists($path)) {
                unlink($path);
            }
        }

        $category->update([
            'category_name' => $request->input('category_name'),
            'category_image' => $imgStore,
        ]);

        return response()->json([
            'message' => 'Data successfully updated',
        ], 200);
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);

        // check user
        if(Auth::user()->id_user !== $category->category_user) {
            return response()->json(['error' => "Access denied"], 403);
        }

        // fetch image name
        $imgDB = explode('/', $category->category_image);
        $imgDB = end($imgDB);

        $path = base_path("public/categories/$imgDB");

        if(file_exists($path)) {
            unlink($path);
        }

        $category->delete();

        return response()->json([
            'message' => 'Data successfully delete'
        ]);
    }
}
