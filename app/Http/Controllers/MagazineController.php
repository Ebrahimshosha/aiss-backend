<?php

namespace App\Http\Controllers;

use App\Models\Magazine;
use App\Http\Requests\StoreMagazineRequest;
use App\Http\Requests\UpdateMagazineRequest;
use Illuminate\Support\Facades\Storage;

class MagazineController extends Controller
{
    public function index()
    {
        $magazines = Magazine::orderByDesc('id')->get();

        return response()->json($magazines);
    }

    public function show($id)
    {
        $magazine = Magazine::find($id);

        if (!$magazine) {
            return response()->json([
                'message' => 'Magazine not found'
            ], 404);
        }

        return response()->json($magazine);
    }

    public function store(StoreMagazineRequest $request)
    {
        $validated = $request->validated();

        $coverPath = null;
        $filePath = null;

        try {
            $coverPath = str_replace(
                '\\',
                '/',
                $request->file('cover_image')->store('magazines/images', 'public')
            );

            $filePath = str_replace(
                '\\',
                '/',
                $request->file('file')->store('magazines/files', 'public')
            );

            $magazine = new Magazine();

            $magazine->title = $validated['title'];
            $magazine->slug = $validated['slug'];
            $magazine->cover_image = $coverPath;
            $magazine->file_path = $filePath;

            $magazine->save();
        } catch (\Throwable $e) {

            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Magazine created successfully',
            'magazine' => $magazine,
        ], 201);
    }

    public function update(UpdateMagazineRequest $request, $id)
    {
        $magazine = Magazine::find($id);

        if (!$magazine) {
            return response()->json([
                'message' => 'Magazine not found'
            ], 404);
        }

        $validated = $request->validated();

        $newCoverPath = null;
        $newFilePath = null;

        $oldCoverPath = $magazine->cover_image;
        $oldFilePath = $magazine->file_path;

        $oldCoverPath = str_replace('\\', '/', $oldCoverPath);
        $oldFilePath = str_replace('\\', '/', $oldFilePath);

        try {

            if ($request->hasFile('cover_image')) {
                $newCoverPath = str_replace(
                    '\\',
                    '/',
                    $request->file('cover_image')
                        ->store('magazines/images', 'public')
                );
            }

            if ($request->hasFile('file')) {
                $newFilePath = str_replace(
                    '\\',
                    '/',
                    $request->file('file')
                        ->store('magazines/files', 'public')
                );
            }

            if (array_key_exists('title', $validated)) {
                $magazine->title = $validated['title'];
            }

            if (array_key_exists('slug', $validated)) {
                $magazine->slug = $validated['slug'];
            }

            if ($newCoverPath) {
                $magazine->cover_image = $newCoverPath;
            }

            if ($newFilePath) {
                $magazine->file_path = $newFilePath;
            }

            $magazine->save();

            // نمسح الصورة القديمة فقط لو معمولة بـ Laravel الجديد
            if (
                $newCoverPath &&
                $oldCoverPath &&
                str_starts_with($oldCoverPath, 'magazines/images/')
            ) {
                Storage::disk('public')->delete($oldCoverPath);
            }

            // نفس الكلام للـ PDF
            if (
                $newFilePath &&
                $oldFilePath &&
                str_starts_with($oldFilePath, 'magazines/files/')
            ) {
                Storage::disk('public')->delete($oldFilePath);
            }
        } catch (\Throwable $e) {

            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }

            if ($newFilePath) {
                Storage::disk('public')->delete($newFilePath);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Magazine updated successfully',
            'magazine' => $magazine,
        ]);
    }

    public function destroy($id)
    {
        $magazine = Magazine::find($id);

        if (!$magazine) {
            return response()->json([
                'message' => 'Magazine not found'
            ], 404);
        }

        $coverPath = str_replace('\\', '/', $magazine->cover_image);
        $filePath = str_replace('\\', '/', $magazine->file_path);

        // نحذف السجل من الداتابيز الأول
        $magazine->delete();

        // نحذف فقط الملفات اللي اتعملت بالنظام الجديد
        if (
            $coverPath &&
            str_starts_with($coverPath, 'magazines/images/')
        ) {
            Storage::disk('public')->delete($coverPath);
        }

        if (
            $filePath &&
            str_starts_with($filePath, 'magazines/files/')
        ) {
            Storage::disk('public')->delete($filePath);
        }

        return response()->json([
            'message' => 'Magazine deleted successfully'
        ]);
    }
}
