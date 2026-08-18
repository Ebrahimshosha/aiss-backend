<?php

namespace App\Http\Controllers;

use App\Models\Booklet;
use App\Http\Requests\StoreBookletRequest;
use App\Http\Requests\UpdateBookletRequest;
use Illuminate\Support\Facades\Storage;


class BookletController extends Controller
{
    public function index()
    {
        $booklets = Booklet::orderByDesc('id')->get();

        return response()->json($booklets);
    }
    public function show($id)
    {
        $booklet = Booklet::find($id);

        if (!$booklet) {
            return response()->json([
                'message' => 'Booklet not found'
            ], 404);
        }

        return response()->json($booklet);
    }

    public function store(StoreBookletRequest $request)
    {
        $validated = $request->validated();

        $coverPath = null;
        $filePath = null;

        try {
            $coverPath = str_replace(
                '\\',
                '/',
                $request->file('cover_image')
                    ->store('booklets/images', 'public')
            );

            $filePath = str_replace(
                '\\',
                '/',
                $request->file('file')
                    ->store('booklets/files', 'public')
            );

            $booklet = new Booklet();
            $booklet->title = $validated['title'];
            $booklet->slug = $validated['slug'];
            $booklet->cover_image = $coverPath;
            $booklet->file_path = $filePath;
            $booklet->save();
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
            'message' => 'Booklet created successfully',
            'booklet' => $booklet,
        ], 201);
    }

    public function update(UpdateBookletRequest $request, $id)
    {
        $booklet = Booklet::find($id);

        if (!$booklet) {
            return response()->json([
                'message' => 'Booklet not found'
            ], 404);
        }

        $validated = $request->validated();

        $newCoverPath = null;
        $newFilePath = null;

        $oldCoverPath = str_replace('\\', '/', $booklet->cover_image);
        $oldFilePath = str_replace('\\', '/', $booklet->file_path);

        try {

            if ($request->hasFile('cover_image')) {
                $newCoverPath = str_replace(
                    '\\',
                    '/',
                    $request->file('cover_image')
                        ->store('booklets/images', 'public')
                );
            }

            if ($request->hasFile('file')) {
                $newFilePath = str_replace(
                    '\\',
                    '/',
                    $request->file('file')
                        ->store('booklets/files', 'public')
                );
            }

            if (array_key_exists('title', $validated)) {
                $booklet->title = $validated['title'];
            }

            if (array_key_exists('slug', $validated)) {
                $booklet->slug = $validated['slug'];
            }

            if ($newCoverPath) {
                $booklet->cover_image = $newCoverPath;
            }

            if ($newFilePath) {
                $booklet->file_path = $newFilePath;
            }

            $booklet->save();

            // نمسح الملفات القديمة بعد نجاح الحفظ
            if (
                $newCoverPath &&
                $oldCoverPath &&
                str_starts_with($oldCoverPath, 'booklets/images/')
            ) {
                Storage::disk('public')->delete($oldCoverPath);
            }

            if (
                $newFilePath &&
                $oldFilePath &&
                str_starts_with($oldFilePath, 'booklets/files/')
            ) {
                Storage::disk('public')->delete($oldFilePath);
            }
        } catch (\Throwable $e) {

            // لو حصل خطأ، نمسح الملفات الجديدة اللي اترفعت
            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }

            if ($newFilePath) {
                Storage::disk('public')->delete($newFilePath);
            }

            throw $e;
        }

        return response()->json([
            'message' => 'Booklet updated successfully',
            'booklet' => $booklet,
        ]);
    }

    public function destroy($id)
    {
        $booklet = Booklet::find($id);

        if (!$booklet) {
            return response()->json([
                'message' => 'Booklet not found'
            ], 404);
        }

        $coverPath = str_replace('\\', '/', $booklet->cover_image);
        $filePath = str_replace('\\', '/', $booklet->file_path);

        $booklet->delete();

        if (
            $coverPath &&
            str_starts_with($coverPath, 'booklets/images/')
        ) {
            Storage::disk('public')->delete($coverPath);
        }

        if (
            $filePath &&
            str_starts_with($filePath, 'booklets/files/')
        ) {
            Storage::disk('public')->delete($filePath);
        }

        return response()->json([
            'message' => 'Booklet deleted successfully'
        ]);
    }
}
