<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');
        $query = $request->query('q');

        $products = Product::with('images')
            ->when($category, function ($q) use ($category) {
                return $q->where('category', $category);
            })
            ->when($query, function ($q) use ($query) {
                return $q->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%$query%")
                      ->orWhere('description', 'like', "%$query%")
                      ->orWhere('features', 'like', "%$query%");
                });
            })
            ->get();

        // return response()->json($products);
        return ProductResource::collection($products);
    }

    public function show($id)
    {
        $product = Product::with('images')->findOrFail($id);
        // return response()->json($product);
        return new ProductResource($product);
    }

    public function bestSellers()
    {
        $products = Product::with('images')->where('is_best_seller', true)->get();
        // return response()->json($products);
        return ProductResource::collection($products);
    }
}
