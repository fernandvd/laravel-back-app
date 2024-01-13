<?php

namespace App\Http\Controllers\Api\Articles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class FavoritesController extends Controller
{
    /**
     * @OA\Post(
     *      path="/api/articles/{slug}/favorite",
     *      tags={"Article"},
     *      summary="Add article favorite",
     *      description="This can only use for user authenticate for add article favorite",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="The slug of article",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\RequestBody(
     *          description="Add article to favorite",
     *          required=true,
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Article not found",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *      ),
     *      security={
     *           {"Token": {}}
     *      }
     * )
     * Add article to user's favorites
     */
    public function add(Request $request, string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();

        $user = $request->user();

        $user->favorites()->syncWithoutDetaching($article);

        return new ArticleResource($article);
    }

    /**
     * @OA\Delete(
     *      path="/api/articles/{slug}/favorite",
     *      tags={"Article"},
     *      summary="Remove a article favorite",
     *      description="This can only use for user authenticate for remove article favorite",
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
     *          description="Not found",
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Succsseful operation",
     *      ),
     *      security={
     *           {"Token": {}}
     *      }
     * )
     * Remove article from user's favorites
     */
    public function remove(Request $request, string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();
        $user = $request->user();

        $user->favorites()->detach($article);

        return new ArticleResource($article);
    }

}
