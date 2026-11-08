<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->is_admin, 403);

        $users = User::query()
            ->select(['id', 'name', 'username', 'email', 'created_at'])
            ->latest()
            ->get();

        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        abort_unless(auth()->user()->is_admin, 403);

        return view('users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless($request->user()->is_admin, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            'is_admin' => ['required', 'boolean'],
        ]);

        if ($user->is_admin && ! $validated['is_admin'] && User::where('is_admin', true)->count() === 1) {
            return back()->withErrors(['is_admin' => 'Er moet minstens één beheerder blijven.']);
        }

        $isAdmin = $validated['is_admin'];
        unset($validated['is_admin']);

        $user->fill($validated);
        $user->is_admin = $isAdmin;
        $user->save();

        return back()->with('success', 'De gebruiker is bijgewerkt.');
    }

    public function destroy(Request $request, User $user)
    {
        abort_unless($request->user()->is_admin, 403);
        abort_if($user->is($request->user()), 403);

        if ($user->is_admin && User::where('is_admin', true)->count() === 1) {
            return back()->withErrors(['user' => 'De laatste beheerder kan niet worden verwijderd.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'De gebruiker is verwijderd.');
    }
}
