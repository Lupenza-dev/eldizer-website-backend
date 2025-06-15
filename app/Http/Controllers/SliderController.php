<?php

namespace App\Http\Controllers;

use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SliderController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $sliders = Slider::with(['creator'])
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            SliderResource::collection($sliders),
            'Sliders retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'button_url' => 'nullable|url|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_published' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $slider = Slider::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'button_text' => $request->button_text,
            'button_url' => $request->button_url,
            'is_published' => $request->boolean('is_published', true),
            'order' => $request->order ?? 0,
            'created_by' => Auth::id(),
        ]);

        // Add media using Spatie Media Library
        if ($request->hasFile('image')) {
            $slider->addMediaFromRequest('image')
                ->withResponsiveImages()
                ->toMediaCollection('sliders');
        }

        return $this->sendResponse(
            new SliderResource($slider->load('creator')),
            'Slider created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Slider $slider): JsonResponse
    {
        return $this->sendResponse(
            new SliderResource($slider->load('creator')),
            'Slider retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Slider $slider): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'title' => 'sometimes|required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:50',
            'button_url' => 'nullable|url|max:255',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_published' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $data = $request->only(['title', 'subtitle', 'button_text', 'button_url', 'is_published', 'order']);

        // Handle image update using Spatie Media Library
        if ($request->hasFile('image')) {
            // Clear existing media in the 'sliders' collection
            $slider->clearMediaCollection('sliders');
            // Add new media
            $slider->addMediaFromRequest('image')
                ->withResponsiveImages()
                ->toMediaCollection('sliders');
        }

        $slider->update($data);

        return $this->sendResponse(
            new SliderResource($slider->load('creator')),
            'Slider updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Slider $slider): JsonResponse
    {
        // Delete all associated media
        $slider->clearMediaCollection('sliders');
        
        $slider->delete();

        return $this->sendResponse(
            null,
            'Slider deleted successfully.'
        );
    }

    /**
     * Get all published sliders.
     */
    public function published(): JsonResponse
    {
        $sliders = Slider::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            SliderResource::collection($sliders),
            'Published sliders retrieved successfully.'
        );
    }

    /**
     * Upload an image and return the path.
     */
    /**
     * No longer needed as we're using Spatie Media Library
     */
    private function uploadImage($file, $folder): string
    {
        // This method is kept for backward compatibility but is no longer used
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/' . $folder, $fileName);
        return $folder . '/' . $fileName;
    }
}
