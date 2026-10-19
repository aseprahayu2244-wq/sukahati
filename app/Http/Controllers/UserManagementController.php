<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('username', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif' ? true : false);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'required|in:super_admin,admin,anggota',
            'jabatan'  => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:20',
        ]);

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'jabatan'  => $request->jabatan,
            'phone'    => $request->phone,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Akun pengguna "' . $request->name . '" berhasil dibuat!');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user->id)],
            'email'    => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'role'     => 'required|in:super_admin,admin,anggota',
            'jabatan'  => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'is_active'=> 'boolean',
        ]);

        // Protect: cannot deactivate the only active super admin
        if ($user->role === 'super_admin' && $request->role !== 'super_admin') {
            $activeSuperAdminCount = User::where('role', 'super_admin')->where('is_active', true)->count();
            if ($activeSuperAdminCount <= 1) {
                return back()->with('error', 'Tidak bisa mengubah role. Ini adalah satu-satunya Super Admin aktif.');
            }
        }

        if ($user->id === auth()->id() && !$request->boolean('is_active', true)) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');
        }

        $user->update([
            'name'      => $request->name,
            'username'  => $request->username,
            'email'     => $request->email,
            'role'      => $request->role,
            'jabatan'   => $request->jabatan,
            'phone'     => $request->phone,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Akun "' . $user->name . '" berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        // Protect: cannot delete self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus akun Anda sendiri.');
        }

        // Protect: cannot delete the last active super admin
        if ($user->role === 'super_admin') {
            $activeSuperAdminCount = User::where('role', 'super_admin')->where('is_active', true)->count();
            if ($activeSuperAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat menghapus. Ini adalah satu-satunya Super Admin aktif.');
            }
        }

        $name = $user->name;
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }
        $user->delete();

        return back()->with('success', 'Akun "' . $name . '" berhasil dihapus!');
    }

    public function toggleActive(User $user)
    {
        // Protect: cannot deactivate self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');
        }

        // Protect: cannot deactivate the only active super admin
        if ($user->role === 'super_admin' && $user->is_active) {
            $activeSuperAdminCount = User::where('role', 'super_admin')->where('is_active', true)->count();
            if ($activeSuperAdminCount <= 1) {
                return back()->with('error', 'Tidak dapat menonaktifkan. Ini adalah satu-satunya Super Admin aktif.');
            }
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', 'Akun "' . $user->name . '" berhasil ' . $status . '!');
    }
}
