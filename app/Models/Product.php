<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\favorites;

class Product extends Model
{
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'subcategory_id',
        'unit',
        'price',
        'quantity',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'is_active' => 'boolean',
    ];

        // Computed availability
    //protected $appends = ['is_available'];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

      public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }


 public function getIsAvailableAttribute()
    {
        return $this->is_active && $this->quantity > 0;
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function favorites()
{
    return $this->hasMany(Favorite::class);
}

}

