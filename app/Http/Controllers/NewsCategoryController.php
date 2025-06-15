<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsCategoryResource;
use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = NewsCategory::with(['creator'])
            ->withCount('news')
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            NewsCategoryResource::collection($categories),
            'News categories retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name' => 'required|string|max:255|unique:news_categories,name',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $category = NewsCategory::create([
            'name' => $request->name,
            'is_published' => $request->boolean('is_published', true),
            'created_by' => Auth::id(),
        ]);

        return $this->sendResponse(
            new NewsCategoryResource($category->load('creator')),
            'News category created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(NewsCategory $newsCategory): JsonResponse
    {
        return $this->sendResponse(
            new NewsCategoryResource($newsCategory->load(['creator', 'news'])),
            'News category retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NewsCategory $newsCategory): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name' => 'sometimes|required|string|max:255|unique:news_categories,name,' . $newsCategory->id,
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $newsCategory->update($request->only(['name', 'is_published']));

        return $this->sendResponse(
            new NewsCategoryResource($newsCategory->load('creator')),
            'News category updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NewsCategory $newsCategory): JsonResponse
    {
        // Prevent deletion if category has news
        if ($newsCategory->news()->exists()) {
            return $this->sendError(
                'Cannot delete category with associated news. Please remove or reassign the news first.',
                [],
                422
            );
        }

        $newsCategory->delete();

        return $this->sendResponse(
            null,
            'News category deleted successfully.'
        );
    }

    /**
     * Get all published news categories.
     */
    public function published(): JsonResponse
    {
        $categories = NewsCategory::withCount(['news' => function($query) {
            $query->where('is_published', true);
        }])
        ->where('is_published', true)
        ->orderBy('name')
        ->get();

        return $this->sendResponse(
            NewsCategoryResource::collection($categories),
            'Published news categories retrieved successfully.'
        );
    }
}
