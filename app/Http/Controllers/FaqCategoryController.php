<?php

namespace App\Http\Controllers;

use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FaqCategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $categories = FaqCategory::with(['creator'])
            ->withCount('faqs')
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            FaqCategoryResource::collection($categories),
            'FAQ categories retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name' => 'required|string|max:255|unique:faq_categories,name',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $category = FaqCategory::create([
            'name' => $request->name,
            'is_published' => $request->boolean('is_published', true),
            'created_by' => Auth::id(),
        ]);

        return $this->sendResponse(
            new FaqCategoryResource($category->load('creator')),
            'FAQ category created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(FaqCategory $faqCategory): JsonResponse
    {
        return $this->sendResponse(
            new FaqCategoryResource($faqCategory->load(['creator', 'faqs'])),
            'FAQ category retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, FaqCategory $faqCategory): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name' => 'sometimes|required|string|max:255|unique:faq_categories,name,' . $faqCategory->id,
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $faqCategory->update($request->only(['name', 'is_published']));

        return $this->sendResponse(
            new FaqCategoryResource($faqCategory->load('creator')),
            'FAQ category updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FaqCategory $faqCategory): JsonResponse
    {
        // Prevent deletion if category has FAQs
        if ($faqCategory->faqs()->exists()) {
            return $this->sendError(
                'Cannot delete category with associated FAQs. Please remove or reassign the FAQs first.',
                [],
                422
            );
        }

        $faqCategory->delete();

        return $this->sendResponse(
            null,
            'FAQ category deleted successfully.'
        );
    }

    /**
     * Get all published FAQ categories with their published FAQs.
     */
    public function published(): JsonResponse
    {
        $categories = FaqCategory::with(['faqs' => function($query) {
            $query->where('is_published', true)
                  ->orderBy('question');
        }])
        ->where('is_published', true)
        ->orderBy('name')
        ->get();

        return $this->sendResponse(
            FaqCategoryResource::collection($categories),
            'Published FAQ categories retrieved successfully.'
        );
    }

    /**
     * Get all FAQ categories with their FAQs for the FAQ page.
     */
    public function forFaqPage(): JsonResponse
    {
        $categories = FaqCategory::with(['faqs' => function($query) {
            $query->where('is_published', true)
                  ->orderBy('question');
        }])
        ->where('is_published', true)
        ->orderBy('name')
        ->get();

        return $this->sendResponse(
            FaqCategoryResource::collection($categories),
            'FAQ categories for FAQ page retrieved successfully.'
        );
    }
}
