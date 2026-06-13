<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display all admins
     */
    public function index()
    {
        return Admin::with('user')->get();
    }

    /**
     * Create a new admin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'phone' => 'nullable|string|max:20',
            'position' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role' => 'admin',
            'is_active' => true,
        ]);

        $admin = Admin::create([
            'user_id' => $user->id,
            'position' => $validated['position'] ?? null,
        ]);

          $token = $user->createToken('api-token')->plainTextToken;
          
        return response()->json([
            'message' => 'Admin created successfully',
            'admin' => $admin->load('user'),
        ], 201);
    }

    /**
     * Show one admin
     */
    public function show(string $id)
    {
        $admin = Admin::with('user')->findOrFail($id);

        return response()->json($admin);
    }

    /**
     * Update admin
     */
    public function update(Request $request, string $id)
    {
        $admin = Admin::with('user')->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'position' => 'sometimes|nullable|string|max:255',
        ]);

        if (isset($validated['name'])) {
            $admin->user->update([
                'name' => $validated['name'],
            ]);
        }

        if (isset($validated['phone'])) {
            $admin->user->update([
                'phone' => $validated['phone'],
            ]);
        }

        if (isset($validated['position'])) {
            $admin->update([
                'position' => $validated['position'],
            ]);
        }

        return response()->json([
            'message' => 'Admin updated successfully',
            'admin' => $admin->fresh()->load('user'),
        ]);
    }

    /**
     * Delete admin
     */
    public function destroy(string $id)
    {
        $admin = Admin::findOrFail($id);

        $user = $admin->user;

        $admin->delete();
        $user->delete();

        return response()->json([
            'message' => 'Admin deleted successfully'
        ]);
    }
}