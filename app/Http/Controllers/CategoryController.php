<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validaciones
        $validated = $request->validate([
            'name' => 'required|string|unique:categories|max:20',
            'image' => 'nullable|string',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio',
            'name.string' => 'El nombre de la categoría debe ser un string',
            'name.unique' => 'Existe una categoria con ese nombre. El nombre de la categoría debe ser único',
            'name.max' => 'El nombre de la categoría no puede superar 20 caracteres',
            'image.string' => 'La ruta de la imagen debe ser un string',
        ]);

        //crear categoria
        $category = Category::create([
            'name' => $validated['name'],
            'image' => $validated['image'] ?? null,
        ]);

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        //validaciones
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
                Rule::unique('categories')->ignore($category->id), //ignora el nombre actual que tiene la categoria para no detectarla como duplicado
            ],
            'image' => 'nullable|string',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio',
            'name.string' => 'El nombre de la categoría debe ser un string',
            'name.unique' => 'Existe una categoria con ese nombre. El nombre de la categoría debe ser único',
            'name.max' => 'El nombre de la categoría no puede superar 20 caracteres',
            'image.string' => 'La ruta de la imagen debe ser un string',
        ]);

        //actualizar categoria
        $category->update([
            'name' => $validated['name'],
            'image' => $validated['image'] ?? null,
        ]);

        return response()->json($category, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['message' => 'Categoría eliminada correctamente',], 200);
    }
}
