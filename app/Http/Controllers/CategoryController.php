<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
     * Crea una nueva categoría
     *
     * @bodyParam name string required El nombre de la categoría. Ejemplo: Animes
     * @bodyParam image file La imagen para la categoría (opcional).
     *
     * @response 201 {
     *   "id": 5,
     *   "name": "Deportes",
     *   "image": "storage/category_images/imagen_subida.jpg",
     *   "created_at": "2025-06-18T12:00:00Z",
     *   "updated_at": "2025-06-18T12:00:00Z"
     * }
     */
    public function store(Request $request)
    {
        //validaciones
        $validated = $request->validate([
            'name' => 'required|string|unique:categories|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'image' => 'nullable|string',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio',
            'name.string' => 'El nombre de la categoría debe ser un string',
            'name.unique' => 'Existe una categoria con ese nombre. El nombre de la categoría debe ser único',
            'name.max' => 'El nombre de la categoría no puede superar 20 caracteres',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg, webp.',
            // 'image.string' => 'La ruta de la imagen debe ser un string',
        ]);

        if ($request->hasFile('image')) {
            // guardar en storage/app/public/category_images
            $imagePath = $request->file('image')->store('category_images', 'public');
            $validated['image'] = 'storage/' . $imagePath; // Ruta accesible públicamente
        }

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
        return response()->json($category);
    }

    /**
     * Actualiza una categoría existente.
     *
     * @urlParam category int required El ID de la categoría a actualizar. Ejemplo: 5
     *
     * @bodyParam name string required El nuevo nombre de la categoría. Ejemplo: Animes Actualizados
     * @bodyParam image file La nueva imagen para la categoría (opcional).
     *
     * @response 200 {
     *   "id": 5,
     *   "name": "Deportes Actualizados",
     *   "image": "storage/category_images/nueva_imagen.jpg",
     *   "created_at": "2025-06-18T12:00:00Z",
     *   "updated_at": "2025-06-18T14:00:00Z"
     * }
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
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'image' => 'nullable|string',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio',
            'name.string' => 'El nombre de la categoría debe ser un string',
            'name.unique' => 'Existe una categoria con ese nombre. El nombre de la categoría debe ser único',
            'name.max' => 'El nombre de la categoría no puede superar 20 caracteres',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg, webp.',
            // 'image.string' => 'La ruta de la imagen debe ser un string',
        ]);

        if ($request->hasFile('image')) {
            // eliminar la imagen anterior
            Storage::delete(str_replace('storage/', 'public/', $category->image));
            $imagePath = $request->file('image')->store('category_images', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

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
