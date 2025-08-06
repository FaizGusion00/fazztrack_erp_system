<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied. Only SuperAdmin, Admin, and Sales Manager can access products.');
        }

        $products = Product::orderBy('created_at', 'desc')->paginate(10);
        
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied. Only SuperAdmin, Admin, and Sales Manager can create products.');
        }

        return view('products.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied. Only SuperAdmin, Admin, and Sales Manager can create products.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'size' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'comments' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $productData = $request->except('images');
        
        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
        }
        
        $productData['images'] = $images;

        Product::create($productData);

        return redirect()->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied. Only SuperAdmin, Admin, and Sales Manager can view products.');
        }

        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied. Only SuperAdmin, Admin, and Sales Manager can edit products.');
        }

        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied. Only SuperAdmin, Admin, and Sales Manager can update products.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'size' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
            'material' => 'nullable|string|max:100',
            'comments' => 'nullable|string',
            'status' => 'required|in:Active,Inactive',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $productData = $request->except('images');
        
        // Handle image uploads
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    Storage::disk('public')->delete($oldImage);
                }
            }
            
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $images[] = $path;
            }
            $productData['images'] = $images;
        }

        $product->update($productData);

        return redirect()->route('products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied. Only SuperAdmin, Admin, and Sales Manager can delete products.');
        }

        // Delete product images
        if ($product->images) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Update stock for a product
     */
    public function updateStock(Request $request, Product $product)
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied. Only SuperAdmin, Admin, and Sales Manager can update stock.');
        }

        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product->update(['stock' => $request->stock]);

        return redirect()->route('products.index')
            ->with('success', 'Product stock updated successfully.');
    }

    /**
     * Get products for order creation (AJAX)
     */
    public function getProductsForOrder()
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied.');
        }

        $products = Product::active()->inStock()
            ->select('product_id', 'name', 'size', 'price', 'stock', 'category', 'color')
            ->orderBy('name')
            ->get();

        return response()->json($products);
    }

    /**
     * Get product details (AJAX)
     */
    public function getProductDetails(Product $product)
    {
        // Check if user has access to products
        if (!Auth::user()->isSuperAdmin() && !Auth::user()->isAdmin() && !Auth::user()->isSalesManager()) {
            abort(403, 'Access denied.');
        }

        return response()->json([
            'product_id' => $product->product_id,
            'name' => $product->name,
            'description' => $product->description,
            'size' => $product->size,
            'price' => $product->price,
            'stock' => $product->stock,
            'category' => $product->category,
            'color' => $product->color,
            'material' => $product->material,
            'comments' => $product->comments,
            'first_image' => $product->first_image_url,
        ]);
    }
} 