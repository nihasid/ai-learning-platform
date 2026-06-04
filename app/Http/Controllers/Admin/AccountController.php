<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        return view('admin.accounts.index', [
            'users' => User::with('permissions')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.accounts.create', [
            'permissions' => Permission::orderBy('label')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', 'in:admin,parent'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.accounts.index')->with('status', 'Account created.');
    }

    public function edit(User $account): View
    {
        return view('admin.accounts.edit', [
            'account' => $account->load('permissions'),
            'permissions' => Permission::orderBy('label')->get(),
        ]);
    }

    public function update(Request $request, User $account): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$account->id],
            'role' => ['required', 'in:admin,parent'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'permissions' => ['array'],
            'permissions.*' => ['exists:permissions,id'],
        ]);

        $account->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if (! empty($validated['password'])) {
            $account->password = Hash::make($validated['password']);
        }

        $account->save();
        $account->permissions()->sync($validated['permissions'] ?? []);

        return redirect()->route('admin.accounts.index')->with('status', 'Account updated.');
    }
}
