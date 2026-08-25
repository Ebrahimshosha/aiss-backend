<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = Event::orderByDesc('event_date')
            ->orderByDesc('id')
            ->get();

        return response()->json($events);
    }

    public function show(Event $event): JsonResponse
    {
        return response()->json($event);
    }

    public function store(StoreEventRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $imagePath = null;

        try {
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')
                    ->store('events/images', 'public');

                $validated['image'] = str_replace('\\', '/', $imagePath);
            }

            $event = Event::create($validated);

            return response()->json([
                'message' => 'Event created successfully',
                'event' => $event,
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
        UpdateEventRequest $request,
        Event $event
    ): JsonResponse {
        $validated = $request->validated();

        $oldImage = $event->image;
        $newImagePath = null;

        try {
            if ($request->hasFile('image')) {
                $newImagePath = $request->file('image')
                    ->store('events/images', 'public');

                $validated['image'] = str_replace('\\', '/', $newImagePath);
            }

            $event->update($validated);

            if (
                $newImagePath &&
                $oldImage &&
                Storage::disk('public')->exists($oldImage)
            ) {
                Storage::disk('public')->delete($oldImage);
            }

            return response()->json([
                'message' => 'Event updated successfully',
                'event' => $event->fresh(),
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

    public function destroy(Event $event): JsonResponse
    {
        $imagePath = $event->image;

        $event->delete();

        if (
            $imagePath &&
            Storage::disk('public')->exists($imagePath)
        ) {
            Storage::disk('public')->delete($imagePath);
        }

        return response()->json([
            'message' => 'Event deleted successfully',
        ]);
    }
}
