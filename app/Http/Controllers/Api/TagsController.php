<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\TagsCollection;
use App\Models\Tag;

class TagsController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @OA\Get(
     *     path="/api/tags",
     *     tags={"Tag"},
     *     summary="Returns list of tags",
     *     description="Returns a list of string of tag",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     * )
     */
    public function list()
    {
        return new TagsCollection(Tag::all());
    }

}
