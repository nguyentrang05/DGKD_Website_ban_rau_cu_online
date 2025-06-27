<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        $categories = Category::all();

        $allProducts = $products->map(function($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'unit' => $product->unit,
                'stock_quantity' => $product->stock_quantity,
                'category' => [
                    'name' => $product->category->name ?? 'Không rõ',
                    'slug' => $product->category->slug ?? 'unknown',
                ],
                'image_url' => $product->image_url ? asset('storage/' . $product->image_url) : null,
                'views' => $product->views ?? 0,
                'created_at' => $product->created_at->format('Y-m-d'),
            ];
        });

        return view('products.index', compact('products', 'categories', 'allProducts'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return view('products.show', compact('product'));
    }


}
