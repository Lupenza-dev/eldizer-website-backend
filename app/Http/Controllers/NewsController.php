<?php

namespace App\Http\Controllers;

use App\Http\Resources\NewsResource;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as FacadesLog;
use Log;

class NewsController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $news = News::with(['category', 'creator'])
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            NewsResource::collection($news),
            'News retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'news_category_id' => 'required',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            // 'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $news = News::create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->news_category_id,
            'is_published' => true,
            'created_by' => 1,
        ]);

        // Add media using Spatie Media Library
        if ($request->hasFile('image')) {
            // $news->addMediaFromRequest('image')
            //     ->withResponsiveImages()
            //     ->toMediaCollection('news');
                $news->addMedia($request['image'])->toMediaCollection('images');
        }

        return $this->sendResponse(
            new NewsResource($news->load(['category', 'creator'])),
            'News created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(News $news): JsonResponse
    {
        return $this->sendResponse(
            new NewsResource($news->load(['category', 'creator'])),
            'News retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'news_category_id' => 'sometimes|required|exists:news_categories,id',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $data = $request->only(['title', 'content', 'news_category_id', 'is_published']);

        // Handle image update using Spatie Media Library
        if ($request->hasFile('image')) {
            // Clear existing media in the 'news' collection
            $news->clearMediaCollection('news');
            // Add new media
            $news->addMediaFromRequest('image')
                ->withResponsiveImages()
                ->toMediaCollection('news');
        }

        $news->update($data);

        return $this->sendResponse(
            new NewsResource($news->load(['category', 'creator'])),
            'News updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news): JsonResponse
    {
        $news->delete();

        return $this->sendResponse(
            null,
            'News deleted successfully.'
        );
    }

    /**
     * Get all published news.
     */
    public function published(): JsonResponse
    {
        $news = News::with(['category'])
            ->where('is_published', true)
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            NewsResource::collection($news),
            'Published news retrieved successfully.'
        );
    }

    /**
     * Get news by category.
     */
    public function byCategory(NewsCategory $category): JsonResponse
    {
        $news = $category->news()
            ->with(['category'])
            ->where('is_published', true)
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            NewsResource::collection($news),
            "News in category '{$category->name}' retrieved successfully."
        );
    }
}
