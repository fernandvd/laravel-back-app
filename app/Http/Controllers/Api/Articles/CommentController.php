<?php

namespace App\Http\Controllers\Api\Articles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\NewCommentRequest;
use App\Http\Resources\{CommentResource, CommentsCollection};
use App\Models\{Article, Comment};

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function list(string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();

        return new CommentsCollection($article->comments);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function create(NewCommentRequest $request, string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();
        $user = $request->user();

        $comment = Comment::create([
            'article_id' => $article->getKey(),
            'author_id' => $user->getKey(),
            'body' => $request->input('comment.body'),
        ]);

        return (new CommentResource($comment))
            ->response()
            ->setStatusCode(201);
    }



    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function delete(string $slug, $id)
    {
        $article = Article::whereSlug($slug)->firstOrFail();

        $comment = $article->comments()->findOrFail((int) $id);

        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json([
            'message' => trans('models.comment.deleted'),
        ], 204);
    }
}
