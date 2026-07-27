<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Category::with('products')->get(),
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories',
            'description' => 'nullable|string',
        ]);

        // Generate slug from name
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Categoría creada exitosamente',
            'category' => $category,
        ], 201);
    }

    public function show($id)
    {
        $category = Category::with('products')->findOrFail($id);
        return response()->json($category, 200);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'string|unique:categories,name,' . $id,
            'description' => 'nullable|string',
        ]);

        // Generate slug from name if name is being updated
        if (isset($validated['name'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }

        $category->update($validated);

        return response()->json([
            'message' => 'Categoría actualizada exitosamente',
            'category' => $category,
        ], 200);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada exitosamente',
        ], 200);
    }
}
