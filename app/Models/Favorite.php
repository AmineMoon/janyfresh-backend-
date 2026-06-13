<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Retailer;
use App\Models\Product;

class Favorite extends Model
{
    protected $fillable = [
        'retailer_id',
        'product_id',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function retailer()
    {
        return $this->belongsTo(Retailer::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}