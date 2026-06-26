<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Filterable;
    
    protected $fillable = ['name', 'slug', 'description', 'price', 'sku', 'stock', 'status', 'category_id'];


    // Relationships

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
