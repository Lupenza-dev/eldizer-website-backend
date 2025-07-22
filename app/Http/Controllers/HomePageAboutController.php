<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHomePageAboutRequest;
use App\Http\Requests\UpdateHomePageAboutRequest;
use App\Http\Resources\HomePageAboutResource;
use App\Models\HomePageAbout;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class HomePageAboutController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
   
    public function index(): JsonResponse
    {
        $minServices = HomePageAbout::with(['creator'])
            ->latest()
            ->first();

        return $this->sendResponse(
            new HomePageAboutResource($minServices),
            'Data retrieved successfully.'
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHomePageAboutRequest $request)
    {
        //
    }
    

    /**
     * Display the specified resource.
     */
    public function show(HomePageAbout $homePageAbout)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HomePageAbout $homePageAbout)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(UpdateHomePageAboutRequest $request, HomePageAbout $homePageAbout)
    // {
    //     //
    // }

    public function update(Request $request, HomePageAbout $homePageAbout): JsonResponse
    {
        $validation = $this->validateRequest($request, [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'badge' => 'sometimes|required|string',
            'values' => 'sometimes|required|string',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validation !== true) {
            return $this->sendError('Validation error', $validation['errors'], 422);
        }

        $homePageAbout =HomePageAbout::latest()->first();

        if ($request->hasFile('image')) {
            // Clear existing media in the 'services' collection
            $homePageAbout->clearMediaCollection('images');
            // Add new media
            $homePageAbout->addMedia($request['image'])->toMediaCollection('images');

        }

        $homePageAbout->update($request->only(['title', 'content', 'badge','values']));

        return $this->sendResponse(
            new HomePageAboutResource($homePageAbout->load('creator')),
            'Data updated successfully.'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HomePageAbout $homePageAbout)
    {
        //
    }
}
