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
     */
    public function show(string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();

        return new ArticleResource($article);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, string $slug)
    {
        $article = Article::whereSlug($slug)->firstOrFail();

        $this->authorize('update', $article);

        $article->update($request->validated());

        return new ArticleResource($article);
    }



    /**
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
