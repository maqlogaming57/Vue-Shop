<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductTableResource;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $products = Product::query()
            ->with(['category', 'brand', 'product_images'])
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', '%' . $search . '%')
                    ->OrWhere('description', 'like', '%' . $search . '%')
                    ->OrWhere('price', 'like', '%' . $search . '%')
                    ->orWhereHas('category', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('brand', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
        $brands = Brand::all();
        $categories = Category::all();

        return Inertia::render('Admin/Product/Index',[
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'search'=> $request->search
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
        try {
            $request->validate([
                'title' => 'required|string|max:200',
                'price' => 'required|numeric|min:0',
                'quantity' => 'required|integer|min:0',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'brand_id' => 'required|exists:brands,id',
                'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $product = new Product;
            $product->title = $title = $request->title;
            $product->slug = Str::slug($title);
            $product->price = $request->price;
            $product->quantity = $request->quantity;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->inStock = $request->quantity > 0; // Set inStock based on quantity
            $product->published = true; // Set published to true by default
            
            if($product->save()){
                if ($request->hasFile('product_images')) {
                    $productImages = $request->file('product_images');
                    foreach ($productImages as $image) {
                        $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                        $image->move('product_images', $uniqueName);
                        
                        ProductImage::create([
                            'product_id' => $product->id,
                            'image' => 'product_images/' . $uniqueName,
                        ]);
                    }
                }

                return redirect()->route('admin.product.index')->with('success', 'Product created successfully.');
            }

            return redirect()->back()->with('error', 'Failed to create product');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        $product = Product::findOrFail($id);

//         dd($request->all());
        $product->title = $request->title;
        $product->price = $request->price;
        $product->quantity = $request->quantity;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        // Check if product images were uploaded
        if ($request->hasFile('product_images')) {
            $productImages = $request->file('product_images');
            // Loop through each uploaded image
            foreach ($productImages as $image) {
                // Generate a unique name for the image using timestamp and random string
                $uniqueName = time() . '-' . Str::random(10) . '.' . $image->getClientOriginalExtension();

                // Store the image in the public folder with the unique name
                $image->move('product_images', $uniqueName);

                // Create a new product image record with the product_id and unique name
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => 'product_images/' . $uniqueName,
                ]);
            }
        }
        $product->update();
        return redirect()->route('admin.product.index')->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if($product){
            $image = ProductImage::where('product_id', $id)->get();
            foreach ($image as $img){
                $filePath = public_path('product_images/' . str_replace('product_images/', '', $img->image));
                if (is_file($filePath)) {
                    unlink($filePath);
                }

                $image->delete();
            }

            $product->delete();
        }
        return redirect()->route('admin.product.index')->with('success', 'Product deleted successfully.');
    }

    public function deleteImage($id){
        $image = ProductImage::find($id);
        if($image){
            $filePath = public_path('product_images/' . str_replace('product_images/', '', $image->image));
            if (is_file($filePath)) {
                unlink($filePath);
            }

            $image->delete();
            return redirect()->route('admin.product.index')->with('success', 'Image deleted successfully');
        }else{
            return redirect()->back()->with('errors', 'Image not found');

        }
    }
}
