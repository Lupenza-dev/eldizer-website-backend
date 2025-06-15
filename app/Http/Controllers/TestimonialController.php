<?php

namespace App\Http\Controllers;

use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TestimonialController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $testimonials = Testimonial::with(['creator'])
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            TestimonialResource::collection($testimonials),
            'Testimonials retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $testimonial = Testimonial::create([
            'name' => $request->name,
            'position' => $request->position,
            'content' => $request->content,
            'is_published' => $request->boolean('is_published', true),
            'created_by' => Auth::id(),
        ]);

        // Add media using Spatie Media Library
        if ($request->hasFile('image')) {
            $testimonial->addMediaFromRequest('image')
                ->withResponsiveImages()
                ->toMediaCollection('testimonials');
        }

        return $this->sendResponse(
            new TestimonialResource($testimonial->load('creator')),
            'Testimonial created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Testimonial $testimonial): JsonResponse
    {
        return $this->sendResponse(
            new TestimonialResource($testimonial->load('creator')),
            'Testimonial retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Testimonial $testimonial): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $data = $request->only(['name', 'position', 'content', 'is_published']);

        // Handle image update using Spatie Media Library
        if ($request->hasFile('image')) {
            // Clear existing media in the 'testimonials' collection
            $testimonial->clearMediaCollection('testimonials');
            // Add new media
            $testimonial->addMediaFromRequest('image')
                ->withResponsiveImages()
                ->toMediaCollection('testimonials');
        }

        $testimonial->update($data);

        return $this->sendResponse(
            new TestimonialResource($testimonial->load('creator')),
            'Testimonial updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Testimonial $testimonial): JsonResponse
    {
        // Delete all associated media
        $testimonial->clearMediaCollection('testimonials');
        
        $testimonial->delete();

        return $this->sendResponse(
            null,
            'Testimonial deleted successfully.'
        );
    }

    /**
     * Get all published testimonials.
     */
    public function published(): JsonResponse
    {
        $testimonials = Testimonial::where('is_published', true)
            ->latest()
            ->get();

        return $this->sendResponse(
            TestimonialResource::collection($testimonials),
            'Published testimonials retrieved successfully.'
        );
    }

    /**
     * Get random published testimonials.
     */
    public function random($count = 3): JsonResponse
    {
        $testimonials = Testimonial::where('is_published', true)
            ->inRandomOrder()
            ->limit($count)
            ->get();

        return $this->sendResponse(
            TestimonialResource::collection($testimonials),
            'Random testimonials retrieved successfully.'
        );
    }
}
