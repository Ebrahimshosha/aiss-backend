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
}
