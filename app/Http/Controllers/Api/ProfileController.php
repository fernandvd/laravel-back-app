<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the specified resource.
     * 
     * @param string $username
     * @return \App\Http\Controllers\Api\ProfileController
     * 
     * @OA\Get(
     *     path="/api/profiles/{username}",
     *     tags={"Profile"},
     *     summary="List profiles by username",
     *     description="Returns a list of user",
     *     operationId="show",
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         description="username of user",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invalid username supplier"
     *     ),
     * )
     */
    public function show(string $username)
    {
        $profile = User::whereUsername($username)
            ->firstOrFail();
        return new ProfileResource($profile);
    }

    /**
     * Follow an author.
     * 
     * @param \Illuminate\Http\Request $request
     * @param string $username
     * @return \App\Http\Resources\ProfileResource
     * 
     * @OA\Post(
     *     path="/api/profiles/{username}/follow",
     *     tags={"Profile"},
     *     summary="Follow the user",
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         description="username of user",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invalid username"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     security={
     *         {"token": {}}
     *     }
     * )
     */
    public function follow(Request $request, string $username)
    {
        $profile = User::whereUsername($username)
            ->firstOrFail();

        $profile->followers()
            ->syncWithoutDetaching($request->user());

        return new ProfileResource($profile);
    }

    /**
     * 
     * @OA\Delete(
     *     path="/api/profiles/{username}/follow",
     *     tags={"Profile"},
     *     summary="Unfollow the user",
     *     @OA\Parameter(
     *         name="username",
     *         in="path",
     *         description="username of user",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invalid username"
     *     ),
     *      @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfull"
     *     ),
     *     security={
     *         {"token": {}}
     *     }
     * )
     */
    public function unfollow(Request $request, string $username)
    {
        $profile = User::whereUsername($username)
            ->firstOrFail();
        $profile->followers()->detach($request->user());

        return new ProfileResource($profile);
    }

}
