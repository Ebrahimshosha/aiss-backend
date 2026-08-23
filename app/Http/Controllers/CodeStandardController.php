<?php

namespace App\Http\Controllers;

use App\Models\CodeStandard;
use App\Http\Requests\StoreCodeStandardRequest;
use App\Http\Requests\UpdateCodeStandardRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CodeStandardController extends Controller
{
    public function index()
    {
        $codeStandards = CodeStandard::select([
            'id',
            'user_id',
            'title',
            'slug',
            'cover_image',
            'inner_image',
            'created_at',
        ])
            ->with([
                'user:id,name',
            ])
            ->latest()
            ->paginate(10);

        return response()->json($codeStandards);
    }

    public function show($id)
    {
        $codeStandard = CodeStandard::with([
            'user:id,name',
        ])->find($id);

        if (!$codeStandard) {
            return response()->json([
                'message' => 'Code standard not found',
            ], 404);
        }

        return response()->json($codeStandard);
    }

    public function store(StoreCodeStandardRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user();

        $coverPath = null;
        $innerPath = null;

        try {
            $coverPath = str_replace(
                '\\',
                '/',
                $request->file('cover_image')
                    ->store('code-standards/covers', 'public')
            );

            $innerPath = str_replace(
                '\\',
                '/',
                $request->file('inner_image')
                    ->store('code-standards/inner', 'public')
            );

            $codeStandard = DB::transaction(function () use (
                $validated,
                $user,
                $coverPath,
                $innerPath
            ) {
                $codeStandard = new CodeStandard();

                $codeStandard->user_id = $user->id;
                $codeStandard->title = $validated['title'];
                $codeStandard->slug = $validated['slug'];
                $codeStandard->content = $validated['content'];
                $codeStandard->cover_image = $coverPath;
                $codeStandard->inner_image = $innerPath;

                $codeStandard->save();

                return $codeStandard;
            });
        } catch (\Throwable $e) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            if ($innerPath) {
                Storage::disk('public')->delete($innerPath);
            }

            throw $e;
        }

        $codeStandard->load([
            'user:id,name',
        ]);

        return response()->json([
            'message' => 'Code standard created successfully',
            'code_standard' => $codeStandard,
        ], 201);
    }

    public function update(UpdateCodeStandardRequest $request, $id)
    {
        $codeStandard = CodeStandard::find($id);

        if (!$codeStandard) {
            return response()->json([
                'message' => 'Code standard not found',
            ], 404);
        }

        $validated = $request->validated();

        $newCoverPath = null;
        $newInnerPath = null;

        try {
            if ($request->hasFile('cover_image')) {
                $newCoverPath = str_replace(
                    '\\',
                    '/',
                    $request->file('cover_image')
                        ->store('code-standards/covers', 'public')
                );
            }

            if ($request->hasFile('inner_image')) {
                $newInnerPath = str_replace(
                    '\\',
                    '/',
                    $request->file('inner_image')
                        ->store('code-standards/inner', 'public')
                );
            }

            $oldCoverPath = $codeStandard->cover_image
                ? str_replace('\\', '/', $codeStandard->cover_image)
                : null;

            $oldInnerPath = $codeStandard->inner_image
                ? str_replace('\\', '/', $codeStandard->inner_image)
                : null;

            DB::transaction(function () use (
                $codeStandard,
                $validated,
                $newCoverPath,
                $newInnerPath
            ) {
                if (array_key_exists('title', $validated)) {
                    $codeStandard->title = $validated['title'];
                }

                if (array_key_exists('slug', $validated)) {
                    $codeStandard->slug = $validated['slug'];
                }

                if (array_key_exists('content', $validated)) {
                    $codeStandard->content = $validated['content'];
                }

                if ($newCoverPath) {
                    $codeStandard->cover_image = $newCoverPath;
                }

                if ($newInnerPath) {
                    $codeStandard->inner_image = $newInnerPath;
                }

                $codeStandard->save();
            });

            // نحذف الصورة القديمة فقط بعد نجاح التعديل
            if (
                $newCoverPath &&
                $oldCoverPath &&
                str_starts_with(
                    $oldCoverPath,
                    'code-standards/covers/'
                )
            ) {
                Storage::disk('public')->delete($oldCoverPath);
            }

            if (
                $newInnerPath &&
                $oldInnerPath &&
                str_starts_with(
                    $oldInnerPath,
                    'code-standards/inner/'
                )
            ) {
                Storage::disk('public')->delete($oldInnerPath);
            }
        } catch (\Throwable $e) {
            // لو التعديل فشل نمسح الصور الجديدة فقط
            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }

            if ($newInnerPath) {
                Storage::disk('public')->delete($newInnerPath);
            }

            throw $e;
        }

        $codeStandard->load([
            'user:id,name',
        ]);

        return response()->json([
            'message' => 'Code standard updated successfully',
            'code_standard' => $codeStandard,
        ]);
    }

    public function destroy($id)
    {
        $codeStandard = CodeStandard::find($id);

        if (!$codeStandard) {
            return response()->json([
                'message' => 'Code standard not found',
            ], 404);
        }

        $coverPath = $codeStandard->cover_image
            ? str_replace('\\', '/', $codeStandard->cover_image)
            : null;

        $innerPath = $codeStandard->inner_image
            ? str_replace('\\', '/', $codeStandard->inner_image)
            : null;

        DB::transaction(function () use ($codeStandard) {
            $codeStandard->delete();
        });

        if (
            $coverPath &&
            str_starts_with(
                $coverPath,
                'code-standards/covers/'
            )
        ) {
            Storage::disk('public')->delete($coverPath);
        }

        if (
            $innerPath &&
            str_starts_with(
                $innerPath,
                'code-standards/inner/'
            )
        ) {
            Storage::disk('public')->delete($innerPath);
        }

        return response()->json([
            'message' => 'Code standard deleted successfully',
        ]);
    }
}