<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    protected $fillable = [
        'order_id',
        'driver_id',
        'assigned_by',
        'status',
        'picked_up_at',
        'in_transit_at',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'picked_up_at' => 'datetime',
        'in_transit_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    
    
}