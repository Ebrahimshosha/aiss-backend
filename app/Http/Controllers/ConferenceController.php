<?php

namespace App\Http\Controllers;

use App\Models\Conference;
use App\Http\Requests\StoreConferenceRequest;
use App\Http\Requests\UpdateConferenceRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ConferenceController extends Controller
{
    // GET ALL
    public function index(): JsonResponse
    {
        $conferences = Conference::orderByDesc('id')->get();

        return response()->json($conferences);
    }

    // GET ONE
    public function show(Conference $conference): JsonResponse
    {
        return response()->json($conference);
    }

    // CREATE
    public function store(StoreConferenceRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('conferences/images', 'public');

                $validated['image'] = str_replace('\\', '/', $imagePath);
            }

            $conference = Conference::create($validated);

            return response()->json([
                'message' => 'Conference created successfully',
                'conference' => $conference,
            ], 201);

        } catch (\Throwable $e) {

            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $e;
        }
    }

    // UPDATE
    public function update(
        UpdateConferenceRequest $request,
        Conference $conference
    ): JsonResponse {
        $validated = $request->validated();

        $oldImage = $conference->image;
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')
                    ->store('conferences/images', 'public');

                $validated['image'] = str_replace('\\', '/', $newImagePath);
            }

            $conference->update($validated);

            // نحذف الصورة القديمة بعد نجاح التعديل
            if (
                $newImagePath &&
                $oldImage &&
                Storage::disk('public')->exists($oldImage)
            ) {
                Storage::disk('public')->delete($oldImage);
            }

            return response()->json([
                'message' => 'Conference updated successfully',
                'conference' => $conference->fresh(),
            ]);

        } catch (\Throwable $e) {

            // لو التعديل فشل نحذف الصورة الجديدة فقط
            if (
                $newImagePath &&
                Storage::disk('public')->exists($newImagePath)
            ) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $e;
        }
    }

    // DELETE
    public function destroy(Conference $conference): JsonResponse
    {
        $imagePath = $conference->image;

        $conference->delete();

        if (
            $imagePath &&
            Storage::disk('public')->exists($imagePath)
        ) {
            Storage::disk('public')->delete($imagePath);
        }

        return response()->json([
            'message' => 'Conference deleted successfully',
        ]);
    }
}