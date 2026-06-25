<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'amount',
        'method',
        'status',
    ];

    /**
     * The order this payment belongs to
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Optional: scope for paid payments
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Optional: scope for pending payments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Cast enums for safer usage (optional but recommended)
     */
    protected $casts = [
        'amount' => 'decimal:2',
    ];
}