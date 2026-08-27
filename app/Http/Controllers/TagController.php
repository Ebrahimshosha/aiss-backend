<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    public function index()
    {
        return response()->json(Tag::all());
    }

    public function show($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found'
            ], 404);
        }

        return response()->json($tag);
    }

    public function store(StoreTagRequest $request)
    {
        $validated = $request->validated();

        $tag = new Tag();

        $tag->name = $validated['name'];
        $tag->slug = $validated['slug'];

        $tag->save();

        return response()->json([
            'message' => 'Tag created successfully',
            'tag' => $tag,
        ], 201);
    }

    public function update(UpdateTagRequest $request, $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found'
            ], 404);
        }

        $validated = $request->validated();

        if (array_key_exists('name', $validated)) {
            $tag->name = $validated['name'];
        }

        if (array_key_exists('slug', $validated)) {
            $tag->slug = $validated['slug'];
        }

        $tag->save();

        return response()->json([
            'message' => 'Tag updated successfully',
            'tag' => $tag,
        ]);
    }

    public function destroy($id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found'
            ], 404);
        }

        DB::transaction(function () use ($tag) {

            DB::table('article_tags')
                ->where('tag_id', $tag->id)
                ->delete();

            $tag->delete();
        });

        return response()->json([
            'message' => 'Tag deleted successfully'
        ]);
    }
    public function articles(int $id)
    {
        $tag = Tag::find($id);

        if (!$tag) {
            return response()->json([
                'message' => 'Tag not found'
            ], 404);
        }

        $articles = $tag->articles()
            ->orderByDesc('user_articles.id')
            ->paginate(10, [
                'user_articles.id',
                'user_articles.title',
                'user_articles.slug',
                'user_articles.cover_image',
                'user_articles.created_at',
            ]);

        $articles->through(function ($article) {
            return [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'cover_image_url' => $article->cover_image_url,
            ];
        });

        return response()->json([
            'tag' => [
                'id' => $tag->id,
                'name' => $tag->name,
                'slug' => $tag->slug,
            ],
            'articles' => $articles->items(),
        ]);
    }
}
