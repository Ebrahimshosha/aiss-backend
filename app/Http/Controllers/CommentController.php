<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use App\Models\Comment;
use App\Models\Magazine;
use App\Models\Booklet;
use App\Models\CodeStandard;
use App\Models\Conference;
use App\Models\Competition;

class CommentController extends Controller
{

    public function store(
        StoreCommentRequest $request,
        Article $article
    ): JsonResponse {
        return $this->storeComment($request, $article);
    }

    public function index(Article $article): JsonResponse
    {
        return $this->getApprovedComments($article);
    }

    public function storeMagazine(
        StoreCommentRequest $request,
        Magazine $magazine
    ): JsonResponse {
        return $this->storeComment($request, $magazine);
    }

    public function indexMagazine(Magazine $magazine): JsonResponse
    {
        return $this->getApprovedComments($magazine);
    }

    private function storeComment(
        StoreCommentRequest $request,
        Article|Magazine|Booklet|CodeStandard|Conference|Competition $commentable
    ): JsonResponse {

        $data = $request->validated();

        $comment = $commentable->comments()->make([
            'name' => $data['name'],
            'email' => $data['email'],
            'body' => $data['body'],
        ]);

        // السيرفر فقط هو الذي يحدد الحالة
        $comment->status = 'pending';

        $comment->save();

        return response()->json([
            'message' => 'Comment submitted successfully and is awaiting approval.',
            'comment' => [
                'id' => $comment->id,
                'name' => $comment->name,
                'body' => $comment->body,
                'status' => $comment->status,
                'created_at' => $comment->created_at,
            ],
        ], 201);
    }

    private function getApprovedComments(
        Article|Magazine|Booklet|CodeStandard|Conference|Competition $commentable
    ): JsonResponse {
        $comments = $commentable->comments()
            ->where('status', 'approved')
            ->latest()
            ->get([
                'id',
                'name',
                'body',
                'created_at',
            ]);

        return response()->json([
            'comments' => $comments,
        ]);
    }

    public function adminIndex(): JsonResponse
    {
        $comments = Comment::query()
            ->where('status', 'pending')
            ->latest()
            ->paginate(20, [
                'id',
                'commentable_type',
                'commentable_id',
                'name',
                'email',
                'body',
                'status',
                'created_at',
            ]);

        return response()->json($comments);
    }

    public function approve(Comment $comment): JsonResponse
    {
        if ($comment->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending comments can be approved.',
            ], 409);
        }

        $comment->status = 'approved';
        $comment->save();

        return response()->json([
            'message' => 'Comment approved successfully.',
            'comment' => [
                'id' => $comment->id,
                'status' => $comment->status,
            ],
        ]);
    }

    public function reject(Comment $comment): JsonResponse
    {
        if ($comment->status !== 'pending') {
            return response()->json([
                'message' => 'Only pending comments can be rejected.',
            ], 409);
        }

        $comment->status = 'rejected';
        $comment->save();

        return response()->json([
            'message' => 'Comment rejected successfully.',
            'comment' => [
                'id' => $comment->id,
                'status' => $comment->status,
            ],
        ]);
    }

    public function storeBooklet(
        StoreCommentRequest $request,
        Booklet $booklet
    ): JsonResponse {
        return $this->storeComment($request, $booklet);
    }

    public function indexBooklet(Booklet $booklet): JsonResponse
    {
        return $this->getApprovedComments($booklet);
    }

    public function storeCodeStandard(
        StoreCommentRequest $request,
        CodeStandard $codeStandard
    ): JsonResponse {
        return $this->storeComment($request, $codeStandard);
    }

    public function indexCodeStandard(
        CodeStandard $codeStandard
    ): JsonResponse {
        return $this->getApprovedComments($codeStandard);
    }

    public function storeConference(
        StoreCommentRequest $request,
        Conference $conference
    ): JsonResponse {
        return $this->storeComment($request, $conference);
    }

    public function indexConference(Conference $conference): JsonResponse
    {
        return $this->getApprovedComments($conference);
    }

    public function storeCompetition(
        StoreCommentRequest $request,
        Competition $competition
    ): JsonResponse {
        return $this->storeComment($request, $competition);
    }

    public function indexCompetition(Competition $competition): JsonResponse
    {
        return $this->getApprovedComments($competition);
    }
}
