<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contents = Content::all();
        return response()->json($contents);
    }

    /**
     * obtener contenidos por categoria
     *
     * @urlParam name string required El nombre de la categoría. Ejemplo: Películas
     * @response 200 [
     *   {
     *     "id": 1,
     *     "category_name": "Películas",
     *     "name": "Matrix",
     *     "description": "Ciencia ficción",
     *     ...
     *   }
     * ]
     */
    public function indexByCategory($name)
    {
        $contents = Content::where('category_name', $name)->get();

        if ($contents->isEmpty()) {
            return response()->json(['message' => 'No se encontraron contenidos para esta categoría'], 404);
        }

        return response()->json($contents);
    }

    /**
     * Crea un nuevo contenido.

     * @bodyParam category_name string required Nombre de la categoría a la que pertenece el contenido. Ejemplo: Serie
     * @bodyParam name string required Nombre del contenido. Ejemplo: Stranger Things
     * @bodyParam description string Descripción del contenido. Ejemplo: Serie de misterio y ciencia ficción.
     * @bodyParam image file Imagen del contenido (opcional).
     * @bodyParam duration integer Duración en minutos. Ejemplo: 50
     * @bodyParam number_of_episodes integer Número de episodios (opcional). Ejemplo: 8
     * @bodyParam genre string Género del contenido. Ejemplo: Ciencia ficción
     *
     * @response 201 {
     *   "id": 10,
     *   "name": "Stranger Things",
     *   "description": "Serie de misterio y ciencia ficción.",
     *   "duration": 50,
     *   "number_of_episodes": 8,
     *   "genre": "Ciencia ficción",
     *   "category_id": 3,
     *   "image": "storage/content_images/stranger_things.jpg",
     *   "created_at": "2025-06-18T14:00:00Z",
     *   "updated_at": "2025-06-18T14:00:00Z"
     * }
     */
    public function store(Request $request)
    {
        //validaciones
        $validated = $request->validate([
            'category_name' => 'required|string|exists:categories,name',
            'name' => 'required|string|max:30|unique:contents,name',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'image' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'number_of_episodes' => 'nullable|integer|min:0',
            'genre' => 'nullable|string',
        ], [
            'category_name.required' => 'El nombre de la categoría es obligatorio',
            'category_name.exists' => 'La categoría seleccionada no existe',
            'name.required' => 'El nombre del contenido es obligatorio',
            'name.string' => 'El nombre del contenido debe ser un string',
            'name.max' => 'El nombre no puede superar los 30 caracteres',
            'name.unique' => 'El nombre del contenido debe ser único',
            'description.required' => 'La descripción es obligatoria',
            'description.string' => 'La descripción del contenido debe ser un string',
            'duration.integer' => 'La duración debe ser un número',
            'duration.min' => 'La duración no puede ser negativa',
            'number_of_episodes.integer' => 'El número de episodios debe ser un número',
            'number_of_episodes.min' => 'El número de episodios no puede ser negativo',
            'genre.string' => 'El género del anime debe ser un string',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg, webp.',
        ]);

        if ($request->hasFile('image')) {
            // guardar en storage/app/public/category_images
            $imagePath = $request->file('image')->store('content_images', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        //crear elemento
        $content = Content::create([
            'category_name' => $validated['category_name'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'image' => $validated['image'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'number_of_episodes' => $validated['number_of_episodes'] ?? null,
            'genre' => $validated['genre'] ?? null,
        ]);

        return response()->json($content, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Content $content)
    {
        return response()->json($content);
    }

    /**
     * Actualiza un contenido existente.
     *
     * @urlParam content int required ID del contenido que se va a actualizar. Ejemplo: 10
     *
     * @bodyParam category_name string required Nombre de la categoría a la que pertenece el contenido. Ejemplo: Serie
     * @bodyParam name string required Nombre del contenido. Ejemplo: Stranger Things 2
     * @bodyParam description string Descripción del contenido. Ejemplo: Serie de misterio y ciencia ficción.
     * @bodyParam image file Imagen del contenido (opcional).
     * @bodyParam duration integer Duración en minutos. Ejemplo: 50
     * @bodyParam number_of_episodes integer Número de episodios (opcional). Ejemplo: 8
     * @bodyParam genre string Género del contenido. Ejemplo: Ciencia ficción
     *
     * @response 200 {
     *   "id": 10,
     *   "name": "Stranger Things",
     *   "description": "Serie de misterio y ciencia ficción.",
     *   "duration": 50,
     *   "number_of_episodes": 8,
     *   "genre": "Ciencia ficción",
     *   "category_id": 3,
     *   "image": "storage/content_images/stranger_things_updated.jpg",
     *   "created_at": "2025-06-18T14:00:00Z",
     *   "updated_at": "2025-06-18T14:30:00Z"
     * }
     */
    public function update(Request $request, Content $content)
    {
        //validaciones
        $validated = $request->validate([
            'category_name' => 'required|string|exists:categories,name',
            'name' => [
                'required',
                'string',
                'max:30',
                Rule::unique('contents')->ignore($content->id),
            ],
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            // 'image' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'number_of_episodes' => 'nullable|integer|min:0',
            'genre' => 'nullable|string',
        ], [
            'category_name.required' => 'El nombre de la categoría es obligatorio',
            'category_name.exists' => 'La categoría seleccionada no existe',
            'name.required' => 'El nombre del contenido es obligatorio',
            'name.string' => 'El nombre del contenido debe ser un string',
            'name.max' => 'El nombre no puede superar los 30 caracteres',
            'name.unique' => 'El nombre del contenido debe ser único',
            'description.required' => 'La descripción es obligatoria',
            'description.string' => 'La descripción del contenido debe ser un string',
            'duration.integer' => 'La duración debe ser un número',
            'duration.min' => 'La duración no puede ser negativa',
            'number_of_episodes.integer' => 'El número de episodios debe ser un número',
            'number_of_episodes.min' => 'El número de episodios no puede ser negativo',
            'genre.string' => 'El género del anime debe ser un string',
            'image.image' => 'El archivo debe ser una imagen.',
            'image.mimes' => 'La imagen debe ser un archivo de tipo: jpeg, png, jpg, webp.',
        ]);

        if ($request->hasFile('image')) {
            // eliminar la imagen anterior
            Storage::delete(str_replace('storage/', 'public/', $content->image));
            $imagePath = $request->file('image')->store('category_images', 'public');
            $validated['image'] = 'storage/' . $imagePath;
        }

        //actualizar elemento
        $content->update([
            'category_name' => $validated['category_name'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'image' => $validated['image'] ?? null,
            'duration' => $validated['duration'] ?? null,
            'number_of_episodes' => $validated['number_of_episodes'] ?? null,
            'genre' => $validated['genre'] ?? null,
        ]);

        return response()->json($content, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Content $content)
    {
        $content->delete();
        return response()->json(['message' => 'Contenido eliminado correctamente'], 200);
    }
}
