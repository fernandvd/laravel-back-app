<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\{LoginRequest, NewUserRequest, LoginWebRequest};
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use OpenApi\Annotations as OA;
use Inertia\Inertia;
use App\Providers\RouteServiceProvider;


class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param NewUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @OA\Post(
     *     path="/api/users",
     *     tags={"Auth"},
     *     summary="Add new user",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
    *                 @OA\Property(
    *                     property="username",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="email",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="password",
    *                     type="string",
    *                 ),
    *                 @OA\Property(
    *                     property="rol",
    *                     type="string",
    *                 ),
    *                 example={"username": "username", "email": "user@example.com", "password": 12345678, "rol": "CLIENT"}
    *             )
    *         )
    *     ),
     *     @OA\Response(response="201", description="Create a user"),
     * )
     */
    public function register(NewUserRequest $request)
    {
        $attributes = $request->validated();

        $attributes['password'] = Hash::make($attributes['password']);

        $rol = Arr::pull($attributes, "rol");

        $user = User::create($attributes);

        $user->assignRole($rol);
        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Login existing user.
     *
     * @param \App\Http\Requests\LoginRequest $request
     * @return \App\Http\Resources\UserResource
     *
     * @OA\Post(
     *     path="/api/users/login",
     *     tags={"Auth"},
     *     summary="Login",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
    *                 @OA\Property(
    *                     property="email",
    *                     type="string"
    *                 ),
    *                 @OA\Property(
    *                     property="password",
    *                     type="string",
    *                 ),
    *                 example={"email": "user@example.com", "password": "12345678"}
    *             )
    *         )
    *     ),
     *     @OA\Response(response="200", description="Login user"),
     * )
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

    /**
     * Display the login view
     */
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Handle an incomming authentication request
     */
    public function store(LoginWebRequest $request)
    {
        //var_dump($request->only(['email', 'password']));
        //Auth::attempt($request->only(['email', 'password']));

        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session
     */
    public function destroy(Request $request) {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(RouteServiceProvider::HOME);
    }

}
