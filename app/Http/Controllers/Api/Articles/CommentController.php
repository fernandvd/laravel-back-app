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
     * @OA\Get(
     *      path="/api/articles/{slug}/comments",
     *      tags={"Comment"},
     *      summary="Get a list of comment of some article",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="The slug of article",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="string",
     *              )
     *          ),
     *      )
     * )
     * Display a listing of the resource.
     */
    public function list(string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();

        return new CommentsCollection($article->comments);
    }


    /**
     * @OA\Post(
     *      path="/api/articles/{slug}/comments",
     *      tags={"Comment"},
     *      summary="Create a comment in some article",
     *      description="Only user authenticate can create a comment in some article",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="The slug of article",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Article not found",
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="Successful operation",
     *      ),
     *      @OA\RequestBody(
     *          description="Create comment object",
     *          required=true,
     *      ),
     *      security={
     *          {"token": {}}
     *      }
     * )
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * @OA\Delete(
     *      path="/api/articles/{slug}/comments/{id}",
     *      tags={"Comment"},
     *      summary="Delete comment",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="The slug of article",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          description="The id of comment",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found",
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *      ),
     * )
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
