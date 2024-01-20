<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'filters' => FacadeRequest::all('search', 'email_verified_at',),
            'users' => User::orderBy('username')
            ->filter(FacadeRequest::only('search', 'email_verified_at',))->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store() 
    {
        FacadeRequest::validate([
            'name' => ['required', 'max:100'],
            'username' => ['required', 'max:255', Rule::unique('users')],
            'email' => ['required', 'max:255', 'email', Rule::unique('users')],
            'password' => ['nullable'],
            'bio' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'name' => FacadeRequest::get('name'),
            'username' => FacadeRequest::get('username'),
            'email' => FacadeRequest::get('email'),
            'password' => Hash::make(FacadeRequest::get('password')),
            'bio' => FacadeRequest::get('bio'),
        ]);

        return Redirect::route('users.list')->with('success', "User: {$user->name} created.");
        
    }

    public function edit(User $user) 
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "username" => $user->username,
                "bio" => $user->bio,
                "deleted" => false,
            ],
        ]);
    }

    public function update(User $user) 
    {
        FacadeRequest::validate([

        ]);

        $user->update(FacadeRequest::only('email', 'username', 'bio', 'name'));

        if (FacadeRequest::get('password')) {
            $user->update(['password' => Hash::make(FacadeRequest::get('password'))]);
        }

        return Redirect::back()->with('success', 'User updated.');
    }

    public function destroy(User $user) {
        $user->delete();

        return Redirect::back()->with('success', 'User deleted.');
    }


}
