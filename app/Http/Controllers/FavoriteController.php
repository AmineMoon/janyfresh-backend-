<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Product;
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
    $user = $request->user();

    if (!$user) {
        return response()->json([
            'message' => 'Unauthenticated'
        ], 401);
    }

    $retailer = $user->retailer;

    if (!$retailer) {
        return response()->json([
            'message' => 'Retailer not found for this user'
        ], 400);
    }

    $products = Product::with([
        'category',
        'subcategory',
        'images',
        'primaryImage',
    ])
    ->whereHas('favorites', function ($query) use ($retailer) {
        $query->where('retailer_id', $retailer->id);
    })
    ->get();

    return response()->json($products);
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
public function destroy(Request $request, $productId)
{
    $retailer = $request->user()->retailer;

    $favorite = Favorite::where('product_id', $productId)
        ->where('retailer_id', $retailer->id)
        ->firstOrFail();

    $favorite->delete();

    return response()->json([
        'message' => 'Favorite removed successfully'
    ]);
}
}
