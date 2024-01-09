<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentsCollection extends ResourceCollection
{

    public static $wrap = 'comments';
    
    /**
     * The rosurce that this resource collects.
     *
     * @var string
     */
    public $collects = CommentResource::class;
}
