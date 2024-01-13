<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA;

/**
     * @OA\Info(
     *     version="1.0.0",
     *     title="Todo App",
     *     description="API list for this project",
     *     @OA\Contact(
     *          email="fernando.gam@outlook.com",
     *      ),
     * )
     */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
