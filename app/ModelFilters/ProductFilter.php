<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class ProductFilter extends ModelFilter
{

    public function category($slug)
    {
        return $this->whereHas('category', function ($query) use ($slug) {
            $query->where('slug', $slug)
                ->orWhereHas('parent', function ($parentQuery) use ($slug) {
                    $parentQuery->where('slug', $slug); 
                });
        });
    }

    // فلترة البحث النصي: ?search=جهاز
    public function search($text)
    {
        return $this->where('name', 'like', "%{$text}%");
    }

    // فلترة السعر الأعلى: ?max_price=500
    public function maxPrice($price)
    {
        return $this->where('price', '<=', $price);
    }
}
