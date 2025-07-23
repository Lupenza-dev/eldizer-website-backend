<?php

namespace App\Http\Controllers;

use App\Http\Resources\MinServiceResource;
use App\Models\MinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MinServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $minServices = MinService::with(['creator'])
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            MinServiceResource::collection($minServices),
            'Minimal services retrieved successfully.'
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
            'icon' => 'required|string',
            'is_published' => 'sometimes|boolean',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',

        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $minService = MinService::create([
            'title' => $request->title,
            'content' => $request->content,
            'icon' => $request->icon,
            'is_published' => $request->boolean('is_published', true),
            'created_by' => 1
            // 'created_by' => Auth::id(),
        ]);

        if ($request->hasFile('image')) {
            $minService->addMedia($request['image'])->toMediaCollection('images');

        }

        return $this->sendResponse(
            new MinServiceResource($minService->load('creator')),
            'Minimal service created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(MinService $minService): JsonResponse
    {
        return $this->sendResponse(
            new MinServiceResource($minService->load('creator')),
            'Minimal service retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MinService $minService): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'icon' => 'sometimes|required|string',
            'is_published' => 'sometimes|boolean',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',


        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        if ($request->hasFile('image')) {
            // Clear existing media in the 'services' collection
            $minService->clearMediaCollection('images');
            // Add new media
            $minService->addMedia($request['image'])->toMediaCollection('images');

        }

        $minService->update($request->only(['title', 'content', 'is_published','icon']));

        return $this->sendResponse(
            new MinServiceResource($minService->load('creator')),
            'Minimal service updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MinService $minService): JsonResponse
    {
        $minService->delete();

        return $this->sendResponse(
            null,
            'Minimal service deleted successfully.'
        );
    }

    /**
     * Get all published minimal services.
     */
    public function published(): JsonResponse
    {
        $minServices = MinService::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            MinServiceResource::collection($minServices),
            'Published minimal services retrieved successfully.'
        );
    }
}
