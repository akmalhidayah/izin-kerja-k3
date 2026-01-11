<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserPanelController extends Controller
{
  public function index(Request $request)
{
    $query = User::with('role');

    if ($request->filled('usertype')) {
        $query->where('usertype', $request->usertype);
    }

    $users = $query->paginate(15);

    return view('admin.user-panel.index', compact('users'));
}

    public function create()
    {
        $roles = Role::all();
        return view('admin.user-panel.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'usertype' => 'required|in:admin,user,pgo',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'usertype' => $request->usertype,
            'role_id' => $request->role_id,
            'password' => bcrypt('password'), // Default password, bisa diubah
        ]);

        return redirect()->route('admin.userpanel.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.user-panel.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'usertype' => 'required|in:admin,user,pgo',
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'usertype' => $request->usertype,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('admin.userpanel.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.userpanel.index')->with('success', 'User berhasil dihapus.');
    }
}
