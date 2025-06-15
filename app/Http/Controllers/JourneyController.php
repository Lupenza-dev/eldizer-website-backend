<?php

namespace App\Http\Controllers;

use App\Http\Resources\JourneyResource;
use App\Models\Journey;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JourneyController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $journeys = Journey::with(['creator'])
            ->orderBy('year', 'desc')
            ->paginate(10);

        return $this->sendResponse(
            JourneyResource::collection($journeys),
            'Journey timeline retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'content' => 'required|string',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $journey = Journey::create([
            'year' => $request->year,
            'content' => $request->content,
            'is_published' => $request->boolean('is_published', true),
            'created_by' => Auth::id(),
        ]);

        return $this->sendResponse(
            new JourneyResource($journey->load('creator')),
            'Journey milestone created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Journey $journey): JsonResponse
    {
        return $this->sendResponse(
            new JourneyResource($journey->load('creator')),
            'Journey milestone retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Journey $journey): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'content' => 'sometimes|required|string',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $journey->update($request->only(['year', 'content', 'is_published']));

        return $this->sendResponse(
            new JourneyResource($journey->load('creator')),
            'Journey milestone updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Journey $journey): JsonResponse
    {
        $journey->delete();

        return $this->sendResponse(
            null,
            'Journey milestone deleted successfully.'
        );
    }

    /**
     * Get all published journey milestones.
     */
    public function published(): JsonResponse
    {
        $journeys = Journey::where('is_published', true)
            ->orderBy('year', 'desc')
            ->get();

        return $this->sendResponse(
            JourneyResource::collection($journeys),
            'Published journey timeline retrieved successfully.'
        );
    }

    /**
     * Get journey milestones by year range.
     */
    public function byYearRange(Request $request): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'start_year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'end_year' => 'required|integer|min:1900|max:' . (date('Y') + 1) . '|gte:start_year',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $journeys = Journey::where('is_published', true)
            ->whereBetween('year', [$request->start_year, $request->end_year])
            ->orderBy('year', 'desc')
            ->get();

        return $this->sendResponse(
            JourneyResource::collection($journeys),
            "Journey milestones from {$request->start_year} to {$request->end_year} retrieved successfully."
        );
    }
}
