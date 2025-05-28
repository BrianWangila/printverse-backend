<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\ProductFeature;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'price',
        'description',
        'category',
        'is_best_seller',
    ];

    protected $casts = [
        'is_best_seller' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function mainImage()
    {
        return $this->images()->where('is_main', true)->first();
    }

    public function features()
    {
        return $this->hasMany(ProductFeature::class);
    }
}
