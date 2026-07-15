<?php

namespace App\Http\Controllers\Koordinator;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->get();
        return view('koordinator.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:dpl,koordinator,anggota',
            'divisi' => 'nullable|string|max:100',
            'nim' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'divisi' => $request->divisi,
            'nim' => $request->nim,
            'phone' => $request->phone,
            'is_active' => true,
        ]);

        return redirect()->route('koordinator.users.index')
            ->with('success', 'Data anggota berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:dpl,koordinator,anggota',
            'divisi' => 'nullable|string|max:100',
            'nim' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'divisi' => $request->divisi,
            'nim' => $request->nim,
            'phone' => $request->phone,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8',
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('koordinator.users.index')
            ->with('success', 'Data anggota berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Cegah menghapus diri sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('koordinator.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('koordinator.users.index')
            ->with('success', 'Data anggota berhasil dihapus.');
    }
}
