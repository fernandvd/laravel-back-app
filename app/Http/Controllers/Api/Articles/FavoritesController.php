<?php

namespace App\Http\Controllers\Api\Articles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class FavoritesController extends Controller
{
    /**
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
