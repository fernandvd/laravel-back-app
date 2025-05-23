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
use Illuminate\Support\Facades\URL;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'filters' => FacadeRequest::all('search', 'email_verified_at', 'role', 'trashed'),
            'users' => request()->user()->account->users()
                ->orderByName()
                ->filter(FacadeRequest::only('search', 'email_verified_at', 'role', 'trashed'))
                ->get()
                ->transform(fn ($user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'owner' => $user->owner,
                    //'image' => $user->image,
                    'image' => $user->image ? URL::route('image', ['path' => $user->image, 'w' => 40, 'h' => 40, 'fit' => 'crop']) : null,
                    'deleted_at' => $user->deleted_at,
                ])
                ,
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store()
    {
        FacadeRequest::validate([
            //'name' => ['required', 'max:100'],
            'first_name' => ['required', 'max:50'],
            'last_name' => ['required', 'max:50'],
            'username' => ['required', 'max:255', Rule::unique('users')],
            'email' => ['required', 'max:255', 'email', Rule::unique('users')],
            'password' => ['nullable'],
            'bio' => ['nullable', 'string'],
            'owner' => ['required', 'boolean'],
            'image' => ['nullable', 'image'],
        ]);

        $user = User::create([
            'name' => FacadeRequest::get('last_name', '') . " " . FacadeRequest::get('fist_name', ''),
             'first_name' => FacadeRequest::get('first_name'),
            'last_name' => FacadeRequest::get('last_name'),
            'username' => FacadeRequest::get('username'),
            'email' => FacadeRequest::get('email'),
            'password' => Hash::make(FacadeRequest::get('password')),
            'owner' => FacadeRequest::get('owner'),
            'bio' => FacadeRequest::get('bio'),
            'account_id' => request()->user()->account_id,
            'image' => FacadeRequest::file('image') ? FacadeRequest::file('image')->store('users') : null,
        ]);

        return Redirect::route('users.list')->with('success', "User: {$user->name} created.");

    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => [
                "id" => $user->id,
                "name" => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                "email" => $user->email,
                "username" => $user->username,
                "bio" => $user->bio,
                "deleted" => $user->deleted_at,
                'owner' => $user->owner,
                'image' => $user->image ? URL::route('image', ['path' => $user->image, 'w' => 60, 'h' => 60, 'fit' => 'crop']) : null,
            ],
        ]);
    }

    public function update(User $user)
    {
        FacadeRequest::validate([
            'first_name' => ['required', 'max:255'],
            'last_name' => ['required', 'max:255'],
            'email' => ['required', 'max:255', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable'],
            'owner' => ['required', 'boolean'],
            'image' => ['nullable', 'image'],
        ]);

        $user->update(array_merge(
            FacadeRequest::only('email', 'bio', 'first_name', 'last_name', 'owner'),
            ['name' => FacadeRequest::get('first_name', ''). " " . FacadeRequest::get('last_name', '')]

        ));

        if (FacadeRequest::file('image')) {
            $user->update([
                'image' => FacadeRequest::file('image')->store('users'),
            ]);
        }
        if (FacadeRequest::get('password')) {
            $user->update(['password' => Hash::make(FacadeRequest::get('password'))]);
        }

        return Redirect::back()->with('success', 'User updated.');
    }

    public function destroy(User $user) {
        if (request()->user()->id === $user->id) {
            return redirect()->back()->with('error', 'Deleting the current user is not allowed.');
        }

        $user->delete();

        return Redirect::back()->with('success', 'User deleted.');
    }

    public function restore(User $user)
    {
        $user->restore();

        return Redirect::back()->with('success', 'User restored');
    }


}
