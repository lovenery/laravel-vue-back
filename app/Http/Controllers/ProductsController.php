<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use Auth;

class ProductsController extends Controller
{
    public function index()
    {
        return Product::orderBy('created_at', 'desc')->get();
    }
    public function store(Request $request)
    {
        // \Log::info($request->all()); // in laravel.log
        $exploded = explode(',', $request->image);
        $decode = base64_decode($exploded[1]);
        if (str_contains($exploded[0], 'jpeg')) {
            $extension = 'jpg';
        } else {
            $extension = 'png';
        }
        $fileName = str_random() . '.' . $extension;
        $path = public_path() . '/' . $fileName;
        file_put_contents($path, $decode);
        $product = Product::create($request->except('image') + [
            'user_id' => Auth::id(),
            'image' => $fileName
        ]);
        return $product;
    }
    public function show($id)
    {
        $product = Product::find($id);
        if (count($product) > 0) {
            return response()->json($product);
        }
        return response()->json(['error' => 'Resource not found!'], 404);
    }
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        $product->update($request->all());
        return response()->json($product);
    }
    public function destroy($id)
    {
        try {
            Product::destroy($id);
            return response([], 204);
        } catch (\Exception $e) {
            return response(['Problem deleting the product', 500]);
        }
    }
    
}
