<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Driver extends Model
{
    protected $table = 'drivers';

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'license_number',
        'is_available',
        'current_location',
    ];

    protected $casts = [
        'is_available' => 'boolean',
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function deliveries()
   {
    return $this->hasMany(Delivery::class);
   }

   
}