<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    /**
     * Show authenticated driver profile
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'driver' => $user->driver
            ]
        ]);
    }

    /**
     * Update authenticated driver
     */
    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $driver = $user->driver;

        $validated = $request->validate([
            // USER TABLE
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'phone' => 'sometimes|string|max:20',

            // DRIVER TABLE
            'vehicle_type' => 'sometimes|string|max:255',
            'license_number' => 'sometimes|string|unique:drivers,license_number,' . $driver->id,
            'current_location' => 'sometimes|nullable|string|max:255',
            'is_available' => 'sometimes|boolean',
        ]);

        /**
         * UPDATE USER
         */
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['email'])) {
            $user->email = $validated['email'];
        }

        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }

        if (isset($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        /**
         * UPDATE DRIVER
         */
        if (isset($validated['vehicle_type'])) {
            $driver->vehicle_type = $validated['vehicle_type'];
        }

        if (isset($validated['license_number'])) {
            $driver->license_number = $validated['license_number'];
        }

        if (array_key_exists('current_location', $validated)) {
            $driver->current_location = $validated['current_location'];
        }

        if (isset($validated['is_available'])) {
            $driver->is_available = $validated['is_available'];
        }

        $driver->save();

        return response()->json([
            'success' => true,
            'message' => 'Driver account updated successfully',
            'data' => [
                'user' => $user,
                'driver' => $driver
            ]
        ]);
    }

    /**
     * Delete authenticated driver
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        if (!$user->isDriver()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $driver = $user->driver;

        $driver->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Driver account deleted successfully'
        ]);
    }
}