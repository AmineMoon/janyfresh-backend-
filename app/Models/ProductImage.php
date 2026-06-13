<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\product;

class ProductImage extends Model
{
     protected $fillable = [
        'product_id',
        'image_path',
        'position',
        'is_primary',
    ];

    protected $casts = [
        'position' => 'integer',
        'is_primary' => 'boolean',
    ];

    // ✅ Relationship

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
