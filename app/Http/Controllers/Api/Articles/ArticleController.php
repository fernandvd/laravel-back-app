<?php

namespace App\Http\Controllers\Api\Articles;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\{ArticleListRequest, FeedRequest, NewArticleRequest, UpdateArticleRequest};
use App\Http\Resources\{ArticleResource, ArticlesCollection};
use Illuminate\Support\{Arr, Collection};

class ArticleController extends Controller
{
    protected const FILTER_LIMIT = 20;

    protected const FILTER_OFFSET = 0;


    /**
     * Display global listing of the articles
     * @OA\Get(
     *     path="/api/articles",
     *     tags={"Article"},
     *     summary="Return a list of articles",
     *     description="Returns a list of articles",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     * )
     */
    public function list(ArticleListRequest $request)
    {
        $filter = collect($request->validated());

        $limit = $this->getLimit($filter);
        $offset = $this->getOffset($filter);

        $list = Article::list($limit, $offset);

        if ($tag = $filter->get('tag')) {
            $list->havingTag($tag);
        }

        if ($authorName = $filter->get('author')) {
            $list->ofAuthor($authorName);
        }

        if ($userName = $filter->get('favorited')) {
            $list->favoredByUser($userName);
        }

        return new ArticlesCollection($list->get());
    }

    /**
     * @OA\Get(
     *      path="/api/articles/feed",
     *      operationId="getProjectsList",
     *      tags={"Article"},
     *      summary="Get list of articles",
     *      description="Returns list of articles by current user",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      security={
     *           {"token": {}}
     *      }
     *     )
     * 
     * Display article feed for the user.
     */
    public function feed(FeedRequest $request)
    {
        $filter = collect($request->validated());

        $limit = $this->getLimit($filter);
        $offset = $this->getOffset($filter);

        $feed = Article::list($limit, $offset)
            ->followedAuthorsOf($request->user());


        return new ArticlesCollection($feed->get());
    }

    /**
     * @OA\Post(
     *      path="/api/articles",
     *      operationId="getProductsList",
     *      tags={"Article"},
     *      summary="Create article",
     *      description="Returns a instance of article",
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
    *                   @OA\Property(
    *                       property="title",
    *                       type="string"
    *                   ),
    *                   @OA\Property(
    *                       property="slug",
    *                       type="string",
    *                   ),
    *                   @OA\Property(
    *                       property="description",
    *                       type="string"
    *                   ),
    *                   @OA\Property(
    *                       property="body",
    *                       type="string",
    *                   ),
    *                   example={"title": "title", "slug": "slug", "description": "description", "body": "body",}
    *             )
    *         )
    *     ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation"
     *      ),
     *      security={
     *          {"token": {}}
     *      }
     * 
     * )
     * Store a newly created resource in storage.
     */
    public function create(NewArticleRequest $request)
    {
        $user = $request->user();

        $attributes = $request->validated();
        $attributes['author_id'] = $user->getKey();

        $tags = Arr::pull($attributes, 'tagList');
        $article = Article::create($attributes);

        if (is_array($tags)) {
            $article->attachTags($tags);
        }

        return (new ArticleResource($article))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     * @OA\Get(
     *      path="/api/articles/{slug}",
     *      tags={"Article"},
     *      summary="Return a record of article",
     *      description="Return a record of article",
     *      @OA\Parameter(
     *          name="slug",
     *          description="Slug of article",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="string",
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="succssful operation",
     *      )
     * )
     * 
     */
    public function show(string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();

        return new ArticleResource($article);

    }

    /**
     * Update the specified resource in storage.
     * 
     * @OA\Put(
     *      path="/api/article/{slug}",
     *      tags={"Article"},
     *      summary="Update article",
     *      description="This can update a article",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="slug of article",
     *          required=true,
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Response(
     *          response=400,
     *          description="Invalid input",
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Article not found",
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\RequestBody(
     *          description="Updated article object,",
     *          required=true,
     *      )
     * )
     */
    public function update(UpdateArticleRequest $request, string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();

        $this->authorize('update', $article);

        $article->update($request->validated());

        return new ArticleResource($article);
    }



    /**
     * @OA\Delete(
     *      path="/api/article/{slug}",
     *      tags={"Article"},
     *      summary="Delete article",
     *      description="This delete article",
     *      @OA\Parameter(
     *          name="slug",
     *          in="path",
     *          description="Slug of article",
     *          required=True,
     *          @OA\Schema(
     *              type="string",
     *          )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Not found article",
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="Successful operation",
     *      )
     * )
     * 
     * Remove the specified resource from storage.
     */
    public function delete(string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();

        $this->authorize('delete', $article);

        $article->delete();

        return response()->json([
            'message' => trans('models.article.deleted'),
        ], 204);
    }

    /**
     * Get limit from filter
     */
    private function getLimit(Collection $filter): int 
    {
        return (int) ($filter['limit'] ?? static::FILTER_LIMIT);
    }

    /**
     * Get offset from filter
     */
    private function getOffset(Collection $filter): int 
    {
        return (int) ($filter['offset'] ?? static::FILTER_OFFSET);
    }
}
