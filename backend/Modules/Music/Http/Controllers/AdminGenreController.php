<?php

namespace Modules\Music\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Music\Models\Genre;
use Modules\Music\Http\Requests\StoreGenreRequest;
use Modules\Music\Http\Requests\UpdateGenreRequest;
use Illuminate\Support\Str;

class AdminGenreController extends Controller
{
    /**
     * [API-370] Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Load root genres (parent_id = null) and recursively load their children
        $genres = Genre::whereNull('parent_id')
            ->with('children') // if nested deeper, we can use recursive relations or just 1 level
            ->get();
            
        // For deep recursive loading:
        // Genre model needs: public function children() { return $this->hasMany(Genre::class, 'parent_id')->with('children'); }
        // For now, assuming basic children loading is enough. If we need deep, we'll update the model later.

        return response()->json([
            'success' => true,
            'message' => 'Lấy danh sách thể loại thành công.',
            'data' => $genres
        ]);
    }

    /**
     * [API-371] Store a newly created resource in storage.
     */
    public function store(StoreGenreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        
        // Auto-generate unique slug
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Genre::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        $validated['slug'] = $slug;
        
        $genre = Genre::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Tạo thể loại thành công.',
            'data' => [
                'genre' => $genre
            ]
        ], 201);
    }

    /**
     * [API-372] Update the specified resource in storage.
     */
    public function update(UpdateGenreRequest $request, $id): JsonResponse
    {
        $genre = Genre::findOrFail($id);
        
        $validated = $request->validated();

        // Optional: Update slug if name changes and we want slugs to sync
        // if (isset($validated['name']) && $validated['name'] !== $genre->name) {
        //     $slug = Str::slug($validated['name']);
        //     // Handle unique check...
        //     $validated['slug'] = $slug;
        // }

        $genre->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thể loại thành công.',
            'data' => [
                'genre' => $genre
            ]
        ]);
    }
}
