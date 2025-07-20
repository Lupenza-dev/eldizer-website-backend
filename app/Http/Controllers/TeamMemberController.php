<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeamMemberResource;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeamMemberController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $teamMembers = TeamMember::with(['creator'])
            ->latest()
            ->paginate(10);

        return $this->sendResponse(
            TeamMemberResource::collection($teamMembers),
            'Team members retrieved successfully.'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name'     => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'bio'      => 'nullable|string',
            'image'    => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'order' => 'sometimes|integer|min:0',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $teamMember = TeamMember::create([
            'name' => $request->name,
            'position' => $request->position,
            'bio' => $request->bio,
            'is_published' => $request->boolean('is_published', true),
            'order' => $request->order ?? 0,
            'created_by' => 1,
            // 'created_by' => Auth::id(),
        ]);

        // Add media using Spatie Media Library
        if ($request->hasFile('image')) {
            $teamMember->addMedia($request['image'])->toMediaCollection('images');

        }

        return $this->sendResponse(
            new TeamMemberResource($teamMember->load('creator')),
            'Team member created successfully.',
            201
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(TeamMember $teamMember): JsonResponse
    {
        return $this->sendResponse(
            new TeamMemberResource($teamMember->load('creator')),
            'Team member retrieved successfully.'
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TeamMember $teamMember): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'name' => 'sometimes|required|string|max:255',
            'position' => 'sometimes|required|string|max:255',
            'bio' => 'nullable|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $data = $request->only(['name', 'position', 'bio']);

        if ($request->has('social_links')) {
            $data['social_links'] = json_encode($request->social_links);
        }

        // Handle image update using Spatie Media Library
        if ($request->hasFile('image')) {
            // Clear existing media in the 'team-members' collection
            $teamMember->clearMediaCollection('team-members');
            // Add new media
            $teamMember->addMedia($request['image'])->toMediaCollection('images');

        }

        $teamMember->update($data);

        return $this->sendResponse(
            new TeamMemberResource($teamMember->load('creator')),
            'Team member updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TeamMember $teamMember): JsonResponse
    {
        // Delete all associated media
        $teamMember->clearMediaCollection('team-members');
        
        $teamMember->delete();

        return $this->sendResponse(
            null,
            'Team member deleted successfully.'
        );
    }

    /**
     * Get all published team members.
     */
    public function published(): JsonResponse
    {
        $teamMembers = TeamMember::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse(
            TeamMemberResource::collection($teamMembers),
            'Published team members retrieved successfully.'
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
