<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use OpenApi\Annotations as OA;



class UserController extends Controller
{
    /**
     * Display the specified resource.
     * @OA\Get(
     *     path="/api/user",
     *     tags={"User"},
     *     summary="Get current user",
     *     @OA\Response(response="200", description="Retrieve current user data"),
     *     security={
     *         {"token": {}}
     *     }
     * )
     */
    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Update the specified resource in storage.
     * @OA\Put(
     *     path="/api/user",
     *     tags={"User"},
     *     summary="Updates a current user",
     *     @OA\Response(
     *         response=200,
     *         description="OK"
     *     ),
     *     security={
     *         {"token": {}}
     *     }
     * )
     */
    public function update(UpdateUserRequest $request)
    {
        if (empty($attrs = $request->validated())) {
            return response()->json([
                'message' => trans('validation.invalid'),
                'errors' => [
                    'any' => [trans('validation.required_at_least')],
                ],
            ], 400);
        }

        $user = $request->user();

        $user->update($attrs);
        return new UserResource($user);
    }
}
