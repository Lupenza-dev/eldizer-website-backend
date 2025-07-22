<?php

namespace App\Http\Controllers;

use App\Http\Resources\AboutUsResource;
use App\Models\AboutUs;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AboutUsController extends BaseController
{
    /**
     * Display the about us content.
     */
    public function index(): JsonResponse
    {
        $aboutUs = AboutUs::first();

        if (!$aboutUs) {
            return $this->sendError('About us content not found.', [], 404);
        }

        return $this->sendResponse(
            new AboutUsResource($aboutUs->load('creator')),
            'About us content retrieved successfully.'
        );
    }

    /**
     * Store a newly created about us content in storage.
     */
    public function store(Request $request): JsonResponse
    {
        // Check if about us content already exists
        if (AboutUs::exists()) {
            return $this->sendError('About us content already exists. Use PUT method to update.', [], 400);
        }

        $validation = $this->validateRequest($request, [
            'vision'  => 'required|string',
            'mission' => 'required|string',
            'content' => 'required|string',
            'values'   => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $aboutUs = AboutUs::create([
            'vision' => $request->vision,
            'mission' => $request->mission,
            'content' => $request->content,
            'values' => $request->values,
            'is_published' => $request->boolean('is_published', true),
            'created_by' => 1,
            'created_by' => Auth::id(),
        ]);

         // Add media using Spatie Media Library
         if ($request->hasFile('image')) {
            $aboutUs->addMedia($request['image'])->toMediaCollection('images');

        }

        return $this->sendResponse(
            new AboutUsResource($aboutUs->load('creator')),
            'About us content created successfully.',
            201
        );
    }

    /**
     * Display the specified about us content.
     */
    public function show(AboutUs $aboutUs): JsonResponse
    {
        return $this->sendResponse(
            new AboutUsResource($aboutUs->load('creator')),
            'About us content retrieved successfully.'
        );
    }

    /**
     * Update the about us content in storage.
     */
    public function update(Request $request, AboutUs $aboutUs): JsonResponse
    {
        Log::info(json_encode($aboutUs));
        Log::info(json_encode($request->all()));

        $validation = $this->validateRequest($request, [
            'vision' => 'sometimes|required|string',
            'mission' => 'sometimes|required|string',
            'content' => 'sometimes|required|string',
            'values' => 'sometimes|required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $aboutUs =AboutUs::first();
        $data = $request->only(['vision', 'mission', 'content', 'values']);

        // Handle image update using Spatie Media Library
        if ($request->hasFile('image')) {
            Log::info("Image Ipo");
            // Clear existing media in the 'testimonials' collection
            $aboutUs->clearMediaCollection('images');
            // Add new media
            $aboutUs->addMedia($request['image'])->toMediaCollection('images');

        }

        $aboutUs->update($data);

        return $this->sendResponse(
            new AboutUsResource($aboutUs->load('creator')),
            'About us content updated successfully.'
        );
    }

    /**
     * Remove the about us content from storage.
     */
    public function destroy(AboutUs $aboutUs): JsonResponse
    {
        // Delete the image file
        if ($aboutUs->image && Storage::exists('public/' . $aboutUs->image)) {
            Storage::delete('public/' . $aboutUs->image);
        }

        $aboutUs->delete();

        return $this->sendResponse(
            null,
            'About us content deleted successfully.'
        );
    }

    /**
     * Get the published about us content.
     */
    public function published(): JsonResponse
    {
        $aboutUs = AboutUs::where('is_published', true)->first();

        if (!$aboutUs) {
            return $this->sendError('Published about us content not found.', [], 404);
        }

        return $this->sendResponse(
            new AboutUsResource($aboutUs->load('creator')),
            'Published about us content retrieved successfully.'
        );
    }

    /**
     * Upload an image and return the path.
     */
    private function uploadImage($file, $folder): string
    {
        $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/' . $folder, $fileName);
        return $folder . '/' . $fileName;
    }
}
