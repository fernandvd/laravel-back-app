<?php

use App\Http\Controllers\Api\Articles\{ArticleController, CommentController, FavoritesController};
use App\Http\Controllers\Api\{AuthController, ProfileController, TagsController, UserController};

use Illuminate\Support\Facades\Route;


Route::name('api.')->group(function () {
    Route::name('users.')->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::get('user', [UserController::class, 'show'])->name('current');
            Route::put('user', [UserController::class, 'update'])->name('update');
        });

        Route::post('users/login', [AuthController::class, 'login'])->name('login');
        Route::post('users', [AuthController::class, 'register'])->name('register');
    });

    Route::name('profiles.')->group(function () {
        Route::middleware('auth:api')->group(function () {
            Route::post('profiles/{username}/follow', [ProfileController::class, 'follow'])->name('follow');
            Route::delete('profiles/{username}/follow', [ProfileController::class, 'unfollow'])->name('unfollow');
        });

        Route::get('profiles/{username}', [ProfileController::class, 'show'])->name('get');

    });

    Route::name('articles.')->group(function() {
        Route::middleware('auth:api')->group(function () {
            Route::get('articles/feed', [ArticleController::class, 'feed'])->name('feed');
            Route::post('articles', [ArticleController::class, 'create'])->name('create');
            Route::put('articles/{slug}', [ArticleController::class, 'update'])->name('update');
            Route::delete('articles/{slug}', [ArticleController::class, 'delete'])->name('delete');
        });

        Route::get('articles', [ArticleController::class, 'list'])->name('list');
        Route::get('articles/{slug}', [ArticleController::class, 'show'])->name('get');

        Route::name('comments.')->group(function () {
            Route::middleware('auth:api')->group(function () {
                Route::post('articles/{slug}/comments', [CommentController::class, 'create'])->name('create');
                Route::delete('articles/{slug}/comments/{id}', [CommentController::class, 'delete'])->name('delete');
            });

            Route::get('articles/{slug}/comments', [CommentController::class, 'list'])->name('get');
        });

        Route::name('favorites.')->middleware('auth:api')->group(function () {
            Route::post('articles/{slug}/favorite', [FavoritesController::class, 'add'])->name('add');
            Route::delete('articles/{slug}/favorite', [FavoritesController::class, 'remove'])->name('remove');
        });
    });
});

Route::name('tags.')->group(function ( ) {
    Route::get('tags', [TagsController::class, 'list'])->name('list');
});

