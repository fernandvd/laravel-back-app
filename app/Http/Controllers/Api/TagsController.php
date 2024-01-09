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
     */
    public function list()
    {
        return new TagsCollection(Tag::all());
    }

}
