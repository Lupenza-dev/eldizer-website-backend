<?php

namespace App\Http\Controllers;

use App\Http\Resources\FaqResource;
use App\Models\Faq;
use App\Models\FaqCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $faqs = Faq::with(['category', 'creator'])
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            FaqResource::collection($faqs),
            'FAQs retrieved successfully.'
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
            'category_id' => 'required',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $faq = Faq::create([
            'title' => $request->title,
            'content' => $request->content,
            'category_id' => $request->category_id,
            'is_published' => $request->boolean('is_published', true),
            'created_by' => 1,
            // 'created_by' => Auth::id(),
        ]);

        return $this->sendResponse(
            new FaqResource($faq->load(['category', 'creator'])),
            'FAQ created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Faq $faq): JsonResponse
    {
        return $this->sendResponse(
            new FaqResource($faq->load(['category', 'creator'])),
            'FAQ retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Faq $faq): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'category_id' => 'sometimes|required',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $faq->update($request->only(['title', 'content', 'category_id', 'is_published']));

        return $this->sendResponse(
            new FaqResource($faq->load(['category', 'creator'])),
            'FAQ updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        return $this->sendResponse(
            null,
            'FAQ deleted successfully.'
        );
    }

    /**
     * Get all published FAQs.
     */
    public function published(): JsonResponse
    {
        $faqs = Faq::with(['category'])
            ->where('is_published', true)
            ->latest()
            ->get();

        return $this->sendResponse(
            FaqResource::collection($faqs),
            'Published FAQs retrieved successfully.'
        );
    }

    /**
     * Get FAQs by category.
     */
    public function byCategory(FaqCategory $category): JsonResponse
    {
        $faqs = $category->faqs()
            ->with(['category'])
            ->where('is_published', true)
            ->get();

        return $this->sendResponse(
            FaqResource::collection($faqs),
            "FAQs in category '{$category->name}' retrieved successfully."
        );
    }

    /**
     * Get all FAQs grouped by category.
     */
    public function byCategories(): JsonResponse
    {
        $categories = FaqCategory::with(['faqs' => function($query) {
            $query->where('is_published', true);
        }])
        ->whereHas('faqs', function($query) {
            $query->where('is_published', true);
        })
        ->get();

        return $this->sendResponse(
            $categories->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'faqs' => FaqResource::collection($category->faqs)
                ];
            }),
            'FAQs grouped by category retrieved successfully.'
        );
    }
}
