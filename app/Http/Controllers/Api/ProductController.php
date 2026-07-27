<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of all products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%$search%")
                  ->orWhere('sku', 'like', "%$search%");
        }

        // Filter active products
        if ($request->has('active')) {
            $query->where('active', $request->boolean('active'));
        }

        return response()->json(
            $query->orderBy('name')->paginate($request->get('per_page', 50)),
            200
        );
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|unique:products',
            'description' => 'nullable|string',
            'sku' => 'required|string|unique:products',
            'price' => 'required|numeric|min:0',
            'cost' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'active' => 'boolean',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Producto creado exitosamente',
            'product' => $product->load('category'),
        ], 201);
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = Product::with('category')->findOrFail($id);

        return response()->json($product, 200);
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'category_id' => 'exists:categories,id',
            'name' => 'string|unique:products,name,' . $id,
            'description' => 'nullable|string',
            'sku' => 'string|unique:products,sku,' . $id,
            'price' => 'numeric|min:0',
            'cost' => 'numeric|min:0',
            'stock' => 'integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'active' => 'boolean',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Producto actualizado exitosamente',
            'product' => $product->load('category'),
        ], 200);
    }

    /**
     * Remove the specified product.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente',
        ], 200);
    }

    /**
     * Upload product image.
     */
    public function uploadImage(Request $request, $id)
    {
        $product = Product::findOrFail($id); // Get product by ID

        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Delete old image if exists
        if ($product->image_path && file_exists(public_path($product->image_path))) {
            unlink(public_path($product->image_path));
        }

        // Store new image
        $path = $request->file('image')->store('products', 'public');
        $product->update(['image_path' => 'storage/' . $path]);

        return response()->json([
            'message' => 'Imagen actualizada exitosamente',
            'image_path' => 'storage/' . $path,
        ], 200);
    }

    /**
     * Get all active products (public endpoint).
     */
    public function publicIndex()
    {
        $products = Product::where('active', true)
            ->with('category')
            ->orderBy('name')
            ->get();

        return response()->json($products, 200);
    }

    /**
     * Get product by ID (public endpoint).
     */
    public function publicShow($id)
    {
        $product = Product::where('active', true)
            ->with('category')
            ->findOrFail($id);

        return response()->json($product, 200);
    }
}
