<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationsController extends Controller
{
    public function index(): Response {
        return Inertia::render('Organizations/Index', [
            'filters' => request()->all('search', 'trashed'),
            'organizations' =>  request()->user()->account->organizations()
                ->orderBy('name')
                ->filter(request()->only('search', 'trashed'))
                ->withCount('contacts')
                ->paginate(10)
                ->withQueryString()
                ->through(fn ($organization) => [
                    'id' => $organization->id,
                    'name' => $organization->name,
                    'phone' => $organization->phone,
                    'city' => $organization->city,
                    'deleted_at' =>$organization->deleted_at,
                    'contacts_count' => $organization->contacts_count,
                ]),
        ]);
    }

    public function create(): Response {
        return Inertia::render('Organizations/Create');
    }

    public function store(): RedirectResponse {
        request()->user()->account->organizations()->create(
            request()->validate([
                'name' => ['required', 'max:100'],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],
            ])
            );

            return redirect()->route('organizations.index')->with('success', 'Organization created.');
    }

    public function edit(Organization $organization): Response {
        return Inertia::render('Organizations/Edit', [
            'organization' => [
                'id' => $organization->id,
                'name' => $organization->name,
                'email' => $organization->email,
                'phone' => $organization->phone,
                'address' => $organization->address,
                'city' => $organization->city,
                'region' => $organization->region,
                'country' => $organization->country,
                'postal_code' => $organization->postal_code,
                'deleted_at' => $organization->deleted_at,
                'contacts' => $organization->contacts()->orderByName()->get()->map->only('id', 'name', 'city', 'phone')
            ],
            ]);
    }

    public function update(Organization $organization): RedirectResponse {
        $organization->update(
            request()->validate([
                'name' => ['required', 'max:100'],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],
            ])
            );

            return redirect()->back()->with('success', 'Organization updated');
    }

    public function destroy(Organization $organization): RedirectResponse {
        $organization->delete();

        return redirect()->back()->with('success', 'Organization deleted.');
    }

    public function restore(Organization $organization): RedirectResponse {
        $organization->restore();

        return redirect()->back()->with('success', 'Organization restored.');
    }
}
