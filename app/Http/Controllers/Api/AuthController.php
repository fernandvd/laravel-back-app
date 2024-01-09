<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{LoginRequest, NewUserRequest};
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user.
     * 
     * @param NewUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(NewUserRequest $request)
    {
        $attributes = $request->validated();

        $attributes['password'] = Hash::make($attributes['password']);

        $user = User::create($attributes);
        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Login existing user.
     * 
     * @param \App\Http\Requests\LoginRequest $request
     * @return \App\Http\Resources\UserResource
     */
    public function login(LoginRequest $request)
    {
        Auth::shouldUse('web');

        if (Auth::attempt($request->validated())) {
            return new UserResource((Auth::user()));
        }

        return response()->json([
            'message' => trans('validation.invalid'),
            'errors' => [
                'user' => [trans('auth.failed')],
            ],
        ], 400);
    }

}
