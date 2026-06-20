<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Retailer;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class RetailerController extends Controller
{
                 


    /* public function index()
{
    return User::where('role', 'retailer')
        ->with([
            'retailer',
            'orders.delivery'
        ])
        ->paginate(20);
}  */
   
    public function show(Request $request)
    {
        $user = $request->user();
        
        if (!$user->isRetailer()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $retailer = $user->retailer;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'retailer' => $retailer
            ]
        ]);
    }

    /**
     * Update retailer account
     */
    public function update(Request $request)
    {
        $user = $request->user();
       //  dd($user);

        if (!$user->isRetailer()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $retailer = $user->retailer;

        // Validate input
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:6',
            'phone' => 'sometimes|string|max:20',
            'shop_name' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
             'age' => 'nullable|numeric',
             'city' => 'nullable|string|max:20',
            'image' => 'sometimes|image|max:2048',
        ]);

        // Update User
        if (isset($validated['name'])) $user->name = $validated['name'];
        if (isset($validated['email'])) $user->email = $validated['email'];
        if (isset($validated['phone'])) $user->phone = $validated['phone'];
        if (isset($validated['password'])) $user->password = Hash::make($validated['password']);
        $user->save();

        // Update Retailer
        if (isset($validated['shop_name'])) $retailer->shop_name = $validated['shop_name'];
        if (isset($validated['address'])) $retailer->address = $validated['address'];
        if (isset($validated['city'])) $retailer->city = $validated['city'];
        if (isset($validated['age'])) $retailer->age = $validated['age'];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($retailer->image) {
                Storage::disk('public')->delete($retailer->image);
            }
            $retailer->image = $request->file('image')->store('retailers', 'public');
        }

        $retailer->save();

        return response()->json([
            'success' => true,
            'message' => 'Retailer account updated successfully',
            'data' => [
                'user' => $user,
                'retailer' => $retailer
            ]
        ]);
    }

    /**
     * Delete retailer account
     */
    public function destroy(Request $request)
    {
        $user = $request->user();

        if (!$user->isRetailer()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $retailer = $user->retailer;

        // Delete image if exists
        if ($retailer->image) {
            Storage::disk('public')->delete($retailer->image);
        }

        $retailer->delete();
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Retailer account deleted successfully'
        ]);
    }
}
 