<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->with(['category', 'brand', 'product_images']);
        $filterProducts = $products->filtered()
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $categories = Category::all();
        $brands = Brand::all();

        return Inertia::render('User/Product/Index',[
            'products' => $filterProducts,
            'categories' => $categories,
            'brands' => $brands,
            'search' => $request->search
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'product_images']);
        
        $soldQuantity = DB::table('order_items')
            ->where('product_id', $product->id)
            ->sum('quantity');
        
        $product->available_stock = $product->quantity - ($soldQuantity ?? 0);

        return Inertia::render('User/Product/Show', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
