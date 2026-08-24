<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Http\Requests\StoreCompetitionRequest;
use App\Http\Requests\UpdateCompetitionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CompetitionController extends Controller
{
    public function index(): JsonResponse
    {
        $competitions = Competition::orderByDesc('id')->get();

        return response()->json($competitions);
    }

    public function show(Competition $competition): JsonResponse
    {
        return response()->json($competition);
    }

    public function store(StoreCompetitionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('competitions/images', 'public');

                $validated['image'] = str_replace('\\', '/', $imagePath);
            }

            $competition = Competition::create($validated);

            return response()->json([
                'message' => 'Competition created successfully',
                'competition' => $competition,
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
        UpdateCompetitionRequest $request,
        Competition $competition
    ): JsonResponse {
        $validated = $request->validated();

        $oldImage = $competition->image;
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')
                    ->store('competitions/images', 'public');

                $validated['image'] = str_replace('\\', '/', $newImagePath);
            }

            $competition->update($validated);

            if (
                $newImagePath &&
                $oldImage &&
                Storage::disk('public')->exists($oldImage)
            ) {
                Storage::disk('public')->delete($oldImage);
            }

            return response()->json([
                'message' => 'Competition updated successfully',
                'competition' => $competition->fresh(),
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

    public function destroy(Competition $competition): JsonResponse
    {
        $imagePath = $competition->image;

        $competition->delete();

        if (
            $imagePath &&
            Storage::disk('public')->exists($imagePath)
        ) {
            Storage::disk('public')->delete($imagePath);
        }

        return response()->json([
            'message' => 'Competition deleted successfully',
        ]);
    }
}