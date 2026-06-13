<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    /**
     * List all orders
     */
    
    public function index()
    {
        return Order::with([
            'retailer',
            'confirmedBy',
            'items.product'
        ])->get();
    }

    /**
     * Create new order
     */
    public function store(Request $request)

    {  
     
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request, &$order) {

            $totalPrice = 0;

            $order = Order::create([
                'retailer_id' => auth()->id(),
                'status' => 'pending',
                'total_price' => 0,
            ]);

            foreach ($request->items as $item) {

                $product = Product::findOrFail($item['product_id']);

                $subtotal = $product->price * $item['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $totalPrice += $subtotal;
            }

            $order->update([
                'total_price' => $totalPrice,
            ]);
        });

        return response()->json([
            'message' => 'Order created successfully',
            'order' => $order->load('items.product')
        ], 201);
    }

    /**
     * Show single order
     */
    public function show(Order $order)
    {
        return $order->load([
            'retailer',
            'confirmedBy',
            'items.product'
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,assigned,out_for_delivery,delivered,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return response()->json([
            'message' => 'Status updated successfully',
            'order' => $order
        ]);
    }

    /**
     * Delete order
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully'
        ]);
    }



  // Confirm order
 

public function confirm(Order $order)
{
    if ($order->status !== 'pending') {
        return response()->json([
            'message' => 'Order cannot be confirmed'
        ], 422);
    }

    DB::transaction(function () use ($order) {

        $order->update([
            'status' => 'confirmed',
            'confirmed_by' => auth()->id(), // ✅ IMPORTANT FIX
        ]);

        $order->delivery()->create([
            'status' => 'assigned',
            'assigned_by'=> auth()->id(),
        ]);
    });

    return response()->json([
        'message' => 'Order confirmed and sent to deliveries',
        'order' => $order->fresh()
    ]);
}


}