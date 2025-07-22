<?php

namespace App\Http\Controllers;

use App\Http\Resources\CoreValueResource;
use App\Models\CoreValue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoreValueController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $coreValues = CoreValue::with(['creator'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return $this->sendResponse(
            CoreValueResource::collection($coreValues),
            'Core values retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name' => 'required|string|max:255',
            'icon' => 'required|string|max:100',
            'content' => 'required|string',
            // 'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $coreValue = CoreValue::create([
            'name' => $request->name,
            'icon' => $request->icon,
            'content' => $request->content,
            'is_published' => $request->boolean('is_published', true),
            'created_by' => 1,
            // 'created_by' => Auth::id(),
        ]);

        return $this->sendResponse(
            new CoreValueResource($coreValue->load('creator')),
            'Core value created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(CoreValue $coreValue): JsonResponse
    {
        return $this->sendResponse(
            new CoreValueResource($coreValue->load('creator')),
            'Core value retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CoreValue $coreValue): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name' => 'sometimes|required|string|max:255',
            'icon' => 'sometimes|required|string|max:100',
            'content' => 'sometimes|required|string',
            // 'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $coreValue->update($request->only(['name', 'icon', 'content']));

        return $this->sendResponse(
            new CoreValueResource($coreValue->load('creator')),
            'Core value updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CoreValue $coreValue): JsonResponse
    {
        $coreValue->delete();

        return $this->sendResponse(
            null,
            'Core value deleted successfully.'
        );
    }

    /**
     * Get all published core values.
     */
    public function published(): JsonResponse
    {
        $coreValues = CoreValue::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            CoreValueResource::collection($coreValues),
            'Published core values retrieved successfully.'
        );
    }
}
