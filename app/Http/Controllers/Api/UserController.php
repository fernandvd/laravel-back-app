<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;


class UserController extends Controller
{
    /**
     * Displlay the specified resource.
     */
    public function show(Request $request)
    {
        return new UserResource($request->user());
    }

    /**
     * Update the specified resource in storage.
     * 
     * 
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
