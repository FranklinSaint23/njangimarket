<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }

        $users = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total'    => User::count(),
            'clients'  => User::where('role', 'client')->count(),
            'vendeurs' => User::where('role', 'vendeur')->count(),
            'livreurs' => User::where('role', 'livreur')->count(),
            'actifs'   => User::where('actif', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,vendeur,client,livreur',
            'password' => 'required|string|min:8|confirmed',
            'actif'    => 'boolean',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
            'actif'    => $request->boolean('actif', true),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} créé avec succès.");
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'    => 'nullable|string|max:20',
            'role'     => 'required|in:admin,vendeur,client,livreur',
            'adresse'  => 'nullable|string|max:255',
            'actif'    => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->only('name', 'email', 'phone', 'role', 'adresse');
        $data['actif'] = $request->boolean('actif');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "Utilisateur {$user->name} mis à jour.");
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Vous ne pouvez pas supprimer votre propre compte.');
        $user->delete();
        return back()->with('success', 'Utilisateur supprimé.');
    }
}
