<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $title = 'Manajemen User';
        $users = User::with(['student', 'teacher'])->get();
        // dd($users);

        return view('admin.user', compact('users', 'title'));
    }

    public function store(Request $request)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:3',
            'role'     => 'required|in:admin,student,teacher',

            'nis'      => 'nullable|string|max:50',
            'nip'      => 'nullable|string|max:50',
            'address'  => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:20',
        ]);

        // dd($validated);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => $validated['role'],
            ]);

            if ($validated['role'] === 'student') {
                $userr = $user->student()->create([
                    'nis'     => $validated['nis'],
                    'address' => $validated['address'] ?? null,
                    'phone'   => $validated['phone'] ?? null,
                ]);
            }

            if ($validated['role'] === 'teacher') {
                $user->teacher()->create([
                    'nip'     => $validated['nip'],
                    'type'    => 'teacher',
                    'address' => $validated['address'] ?? null,
                    'phone'   => $validated['phone'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();

            // ❌ TANPA TAMPILKAN ERROR
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'Gagal menambahkan user');
        }
    }

    public function destroy(User $user)
    {
        DB::beginTransaction();

        try {
            $user->student()?->delete();
            $user->teacher()?->delete();
            $user->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['error' => 'Gagal menghapus user']);
        }
    }
}
