<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List retailer favorites
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $retailer = $request->user()->retailer;

        $favorites = Favorite::with('product')
            ->where('retailer_id', $retailer->id)
            ->get();

        return response()->json($favorites);
    }

    /*
    |--------------------------------------------------------------------------
    | Add product to favorites
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $retailer = $request->user()->retailer;

        $favorite = Favorite::firstOrCreate([
            'retailer_id' => $retailer->id,
            'product_id' => $validated['product_id'],
        ]);

        return response()->json([
            'message' => 'Product added to favorites',
            'data' => $favorite,
        ], 201);
    }

    /*
    |--------------------------------------------------------------------------
    | Remove favorite
    |--------------------------------------------------------------------------
    */

    public function destroy(Request $request, Favorite $favorite)
    {
        $retailer = $request->user()->retailer;

        /*
        |--------------------------------------------------------------------------
        | Security Check
        |--------------------------------------------------------------------------
        */

        if ($favorite->retailer_id !== $retailer->id) {

            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $favorite->delete();

        return response()->json([
            'message' => 'Favorite removed successfully'
        ]);
    }
}