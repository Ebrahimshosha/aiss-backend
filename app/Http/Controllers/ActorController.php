<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use App\Http\Requests\StoreActorRequest;
use App\Http\Requests\UpdateActorRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ActorController extends Controller
{
    public function index(): JsonResponse
    {
        $actors = Actor::orderByDesc('id')->get();

        return response()->json($actors);
    }

    public function show(Actor $actor): JsonResponse
    {
        return response()->json($actor);
    }

    public function store(StoreActorRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('actors/images', 'public');

                $validated['image'] = str_replace('\\', '/', $imagePath);
            }

            $actor = Actor::create($validated);

            return response()->json([
                'message' => 'Actor created successfully',
                'actor' => $actor,
            ], 201);
        } catch (\Throwable $e) {

            if (
                $imagePath &&
                Storage::disk('public')->exists($imagePath)
            ) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $e;
        }
    }

    public function update(
        UpdateActorRequest $request,
        Actor $actor
    ): JsonResponse {
        $validated = $request->validated();

        $oldImage = $actor->image;
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')
                    ->store('actors/images', 'public');

                $validated['image'] = str_replace('\\', '/', $newImagePath);
            }

            $actor->update($validated);

            if (
                $newImagePath &&
                $oldImage &&
                Storage::disk('public')->exists($oldImage)
            ) {
                Storage::disk('public')->delete($oldImage);
            }

            return response()->json([
                'message' => 'Actor updated successfully',
                'actor' => $actor->fresh(),
            ]);
        } catch (\Throwable $e) {

            if (
                $newImagePath &&
                Storage::disk('public')->exists($newImagePath)
            ) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $e;
        }
    }

    public function destroy(Actor $actor): JsonResponse
    {
        $imagePath = $actor->image;

        $actor->delete();

        if (
            $imagePath &&
            Storage::disk('public')->exists($imagePath)
        ) {
            Storage::disk('public')->delete($imagePath);
        }

        return response()->json([
            'message' => 'Actor deleted successfully',
        ]);
    }
}
