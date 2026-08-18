<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Http\Requests\StoreArticleRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateArticleRequest;

class ArticleController extends Controller
{
    public function show($id)
    {
        $article = Article::with([
            'user:id,name',
            'tags:id,name,slug'
        ])->find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found'
            ], 404);
        }

        return response()->json($article);
    }

    public function index()
    {
        $articles = Article::select([
            'id',
            'user_id',
            'title',
            'cover_image',
            'type',
            'slug',
            'created_at'
        ])
            ->with([
                'user:id,name',
                'tags:id,name,slug'
            ])
            ->paginate(10);

        return response()->json($articles);
    }

    public function store(StoreArticleRequest $request)
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
                    ->store('articles/covers', 'public')
            );

            $innerPath = str_replace(
                '\\',
                '/',
                $request->file('inner_image')
                    ->store('articles/inner', 'public')
            );

            $article = DB::transaction(function () use (
                $validated,
                $user,
                $coverPath,
                $innerPath
            ) {
                $article = new Article();

                $article->user_id = $user->id;
                $article->title = $validated['title'];
                $article->slug = $validated['slug'];
                $article->content = $validated['content'];
                $article->type = $validated['type'];
                $article->cover_image = $coverPath;
                $article->inner_image = $innerPath;

                $article->save();

                $article->tags()->sync(
                    $validated['tags'] ?? []
                );

                return $article;
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

        $article->load([
            'user:id,name',
            'tags:id,name,slug',
        ]);

        return response()->json([
            'message' => 'Article created successfully',
            'article' => $article,
        ], 201);
    }

    public function update(UpdateArticleRequest $request, $id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found'
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
                        ->store('articles/covers', 'public')
                );
            }

            if ($request->hasFile('inner_image')) {
                $newInnerPath = str_replace(
                    '\\',
                    '/',
                    $request->file('inner_image')
                        ->store('articles/inner', 'public')
                );
            }

            $oldCoverPath = $article->cover_image;
            $oldInnerPath = $article->inner_image;

            $oldCoverPath = str_replace('\\', '/', $oldCoverPath);
            $oldInnerPath = str_replace('\\', '/', $oldInnerPath);

            DB::transaction(function () use (
                $article,
                $validated,
                $newCoverPath,
                $newInnerPath
            ) {
                if (array_key_exists('title', $validated)) {
                    $article->title = $validated['title'];
                }

                if (array_key_exists('slug', $validated)) {
                    $article->slug = $validated['slug'];
                }

                if (array_key_exists('content', $validated)) {
                    $article->content = $validated['content'];
                }

                if (array_key_exists('type', $validated)) {
                    $article->type = $validated['type'];
                }

                if ($newCoverPath) {
                    $article->cover_image = $newCoverPath;
                }

                if ($newInnerPath) {
                    $article->inner_image = $newInnerPath;
                }

                $article->save();

                if (array_key_exists('tags', $validated)) {
                    $article->tags()->sync($validated['tags']);
                }
            });

            // نمسح الصور القديمة بعد نجاح الـ transaction فقط
            if (
                $newCoverPath &&
                $oldCoverPath &&
                str_starts_with($oldCoverPath, 'articles/covers/')
            ) {
                Storage::disk('public')->delete($oldCoverPath);
            }

            if (
                $newInnerPath &&
                $oldInnerPath &&
                str_starts_with($oldInnerPath, 'articles/inner/')
            ) {
                Storage::disk('public')->delete($oldInnerPath);
            }
        } catch (\Throwable $e) {

            // لو حصل خطأ نمسح الصور الجديدة فقط
            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }

            if ($newInnerPath) {
                Storage::disk('public')->delete($newInnerPath);
            }

            throw $e;
        }

        $article->load([
            'user:id,name',
            'tags:id,name,slug',
        ]);

        return response()->json([
            'message' => 'Article updated successfully',
            'article' => $article,
        ]);
    }

    public function destroy($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'message' => 'Article not found'
            ], 404);
        }



        $coverPath = str_replace(
            '\\',
            '/',
            $article->cover_image
        );

        $innerPath = str_replace(
            '\\',
            '/',
            $article->inner_image
        );

        DB::transaction(function () use ($article) {

            // حذف روابط المقال بالـ tags
            $article->tags()->detach();

            // حذف المقال نفسه
            $article->delete();
        });

        // نحذف فقط الصور اللي اتعملت بالنظام الجديد
        if (
            $coverPath &&
            str_starts_with($coverPath, 'articles/covers/')
        ) {
            Storage::disk('public')->delete($coverPath);
        }

        if (
            $innerPath &&
            str_starts_with($innerPath, 'articles/inner/')
        ) {
            Storage::disk('public')->delete($innerPath);
        }

        return response()->json([
            'message' => 'Article deleted successfully'
        ]);
    }
}
