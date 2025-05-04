<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    public function store(Request $request)
    {
        Log::info('User store request received', ['data' => $request->all()]);

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'isAdmin' => 'required|in:0,1',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'isAdmin' => (int) $validated['isAdmin'],
            ]);

            Log::info('User created successfully', ['user_id' => $user->id]);
            
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil ditambahkan.');
                
        } catch (ValidationException $e) {
            Log::error('Validation error', ['errors' => $e->errors()]);
            return redirect()->back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Failed to create user', ['exception' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    public function show(User $user)
    {
        return Inertia::render('Admin/Users/Show', [
            'user' => $user
        ]);
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'isAdmin' => 'required|in:0,1',
            ]);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'isAdmin' => (int) $validated['isAdmin'],
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diperbarui.');
                
        } catch (\Exception $e) {
            Log::error('Failed to update user', ['exception' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error('Failed to delete user', ['exception' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}