<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Response;

class ContactsController extends Controller
{

    public function index(): Response
    {
        if (Auth::user()->account == null) {
            return inertia('Contacts/Index', [
                'filters' => request()->all('search', 'trashed'),
                'contacts' => [
                    'data' => [],
                    'links' => []
                ],
            ]);
        }
        return inertia('Contacts/Index', [
            'filters' => request()->all('search', 'trashed'),
            'contacts' => Auth::user()->account->contacts()
                ->with('organization')
                ->orderByName()
                ->filter(request()->only('search', 'trashed'))
                ->paginate(10)
                ->withQueryString()
                ->through(fn($contact) => [
                    'id' => $contact->id,
                    'name' => $contact->name,
                    'phone' => $contact->phone,
                    'city' => $contact->city,
                    'deleted_at' => $contact->deleted_at,
                    'organization' => $contact->organization ? $contact->organization->only('name') : null,
                ])
        ]);
    }

    public function create(): Response
    {
        $user = Auth::user();
        if (Auth::user()->account == null) {
            $account = Account::first();
            if ($account) {
                $user->account_id = $account->id;
                $user->save();
            }
            return inertia('Contacts/Create', [
                'organizations' => []
            ]);
        }
        return inertia('Contacts/Create', [
            'organizations' => $user->account
                ->organizations()
                ->orderBy('name')
                ->get()
                ->map
                ->only('id', 'name'),
        ]);
    }

    public function  store(): RedirectResponse
    {
        Auth::user()->account->contacts()->create(
            request()->validate([
                'first_name' => ['required', 'max:50'],
                'last_name' => ['required', 'max:50'],
                'organization_id' => ['nullable', Rule::exists('organizations', 'id')->where(function ($query) {
                    $query->where('account_id', Auth::user()->account_id);
                })],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],
            ])
        );

        return redirect()->route('contacts.index')->with('success', 'Contact created.');
    }

    public function edit(Contact $contact): Response
    {
        $organizations = [];
        if (Auth::user()->account) {
            $organizations = Auth::user()->account->organizations()
                ->orderBy('name')
                ->get()
                ->map
                ->only('id', 'name');
        }
        return inertia('Contacts/Edit', [
            'contact' => [
                'id' => $contact->id,
                'first_name' => $contact->first_name,
                'last_name' => $contact->last_name,
                'organization_id' => $contact->organization_id,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'address' => $contact->address,
                'city' => $contact->city,
                'region' => $contact->region,
                'country' => $contact->country,
                'postal_code' => $contact->postal_code,
                'deleted_at' => $contact->deleted_at,
            ],
            'organizations' => $organizations,
        ]);
    }

    public function update(Contact $contact): RedirectResponse
    {
        $contact->update(
            request()->validate([
                'first_name' => ['required', 'max:50'],
                'last_name' => ['required', 'max:50'],
                'organization_id' => [
                    'nullable',
                    Rule::exists('organizations', 'id')->where(fn($query) => $query->where('account_id', Auth::user()->account_id)),
                ],
                'email' => ['nullable', 'max:50', 'email'],
                'phone' => ['nullable', 'max:50'],
                'address' => ['nullable', 'max:150'],
                'city' => ['nullable', 'max:50'],
                'region' => ['nullable', 'max:50'],
                'country' => ['nullable', 'max:2'],
                'postal_code' => ['nullable', 'max:25'],
            ])
        );
        return redirect()->back()->with('success', 'Contact updated.');
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();
        return redirect()->back()->with('success', 'Contact deleted');
    }

    public function restore(Contact $contact): RedirectResponse {
        $contact->restore();
        return redirect()->back()->with('success', 'Contact restored.');
    }
}
