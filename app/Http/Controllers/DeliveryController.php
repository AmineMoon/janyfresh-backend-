<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignDeliveryRequest;
use App\Http\Requests\UpdateDeliveryStatusRequest;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /* public function index()
    {
        return Delivery::query()
            ->with([
                'order',
                'driver.user',
                'assignedBy'
            ])
            ->latest()
            ->paginate(15);
    }   */ 


        
  
    
          public function index()
{
    $deliveries = Delivery::query()
        ->with([
            'order.retailer.retailer',
            'driver.user',
            'assignedBy'
        ])
        ->latest()
        ->paginate(50); // 👈 pagination here

    // group only current page results
    $grouped = $deliveries->getCollection()
        ->groupBy(fn ($delivery) => $delivery->order->retailer_id)
        ->map(function ($deliveries) {

            $user = $deliveries->first()->order->retailer;
            $retailer = $user->retailer;

            return [
                'retailer_id' => $user->id,

                'name' => $user->name,
                'phone' => $user->phone,

                'shop_name' => $retailer?->shop_name,
                'address' => $retailer?->address,
                'city' => $retailer?->city,

                'orders_count' => $deliveries->count(),

                'orders' => $deliveries->map(function ($delivery) {
                    return [
                        'delivery_id' => $delivery->id,
                        'order_id' => $delivery->order->id,
                        'order_number' => $delivery->order->order_number,
                        'status' => $delivery->status,
                        'total_price' => $delivery->order->total_price,
                    ];
                })->values()
            ];
        })
        ->values();

    return response()->json([
        'data' => $grouped,
        'meta' => [
            'current_page' => $deliveries->currentPage(),
            'last_page' => $deliveries->lastPage(),
            'per_page' => $deliveries->perPage(),
            'total' => $deliveries->total(),
        ]
    ]);
}
   

 
 

    public function store(AssignDeliveryRequest $request)
    {
        return DB::transaction(function () use ($request) {

            $order = Order::lockForUpdate()
                ->findOrFail($request->order_id);

            if ($order->delivery) {
                abort(422, 'Order already assigned.');
            }

            $delivery = Delivery::create([
                'order_id' => $order->id,
                'driver_id' => $request->driver_id,
                'assigned_by' => auth()->id(),
                'status' => 'assigned',
            ]);

            $order->update([
                'status' => 'assigned',
            ]);

            return response()->json([
                'message' => 'Delivery assigned.',
                'data' => $delivery->load('driver.user')
            ], 201);
        });
    }

    public function show(Delivery $delivery)
    {
        return $delivery->load([
            'order',
            'driver.user',
            'assignedBy'
        ]);
    }

    public function updateStatus(
        UpdateDeliveryStatusRequest $request,
        Delivery $delivery
    ) {
        DB::transaction(function () use ($request, $delivery) {

            switch ($request->status) {

                case 'picked_up':

                    $delivery->update([
                        'status' => 'picked_up',
                        'picked_up_at' => now(),
                    ]);

                    break;

                case 'in_transit':

                    $delivery->update([
                        'status' => 'in_transit',
                        'in_transit_at' => now(),
                    ]);

                    $delivery->order->update([
                        'status' => 'out_for_delivery'
                    ]);

                    break;

                case 'delivered':

                    $delivery->update([
                        'status' => 'delivered',
                        'delivered_at' => now(),
                    ]);

                    $delivery->order->update([
                        'status' => 'delivered'
                    ]);

                    break;

                default:

                    $delivery->update([
                        'status' => $request->status
                    ]);
            }
        });

        return response()->json([
            'message' => 'Status updated successfully.',
            'data' => $delivery->fresh()
        ]);
    }

    public function destroy(Delivery $delivery)
    {
        $delivery->delete();

        return response()->json([
            'message' => 'Delivery removed successfully.'
        ]);
    }
}