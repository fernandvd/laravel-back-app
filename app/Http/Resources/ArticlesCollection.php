<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticlesCollection extends ResourceCollection
{

    public static $wrap = 'articles';

    public $collects = ArticleResource::class;
    /**
     * Get additional data that should be returned with the resource.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request)
    {
        return [
            'articlesCount' => $this->collection->count(),
        ];
    }
}
