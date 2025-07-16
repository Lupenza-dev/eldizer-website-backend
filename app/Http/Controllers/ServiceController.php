<?php

namespace App\Http\Controllers;

use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $services = Service::latest()->paginate(10);
        return $this->sendResponse(
            ServiceResource::collection($services),
            'Services retrieved successfully.'
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
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $service = Service::create([
            'title' => $request->title,
            'content' => $request->content,
            'is_published' => $request->boolean('is_published', true),
            'created_by' => 1,
            // 'created_by' => Auth::id(),
        ]);

        // Add media using Spatie Media Library
        if ($request->hasFile('image')) {
                $service->addMedia($request['image'])->toMediaCollection('images');

        }

        return $this->sendResponse(
            new ServiceResource($service->load('creator')),
            'Service created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service): JsonResponse
    {
        return $this->sendResponse(new ServiceResource($service), 'Service retrieved successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $data = $request->only(['title', 'content', 'is_published']);

        // Handle image update using Spatie Media Library
        if ($request->hasFile('image')) {
            // Clear existing media in the 'services' collection
            $service->clearMediaCollection('services');
            // Add new media
            $service->addMediaFromRequest('image')
                ->withResponsiveImages()
                ->toMediaCollection('services');
        }

        $service->update($data);

        return $this->sendResponse(
            new ServiceResource($service->load('creator')),
            'Service updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service): JsonResponse
    {
        // Delete all associated media
        $service->clearMediaCollection('services');
        
        $service->delete();

        return $this->sendResponse(
            null,
            'Service deleted successfully.'
        );
    }
}
