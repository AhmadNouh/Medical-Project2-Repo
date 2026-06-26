<?php

namespace App\Actions;

use App\Models\Product;
use App\Events\ProductCreated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateProductAction
{
    public function execute(array $data, array $images): Product
    {
        
        $data['slug'] = Str::slug($data['name']) . '-' . uniqid();

        return DB::transaction(function () use ($data , $images){
            $product = Product::create([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'sku' => $data['sku'],
                'stock' => $data['stock'] ?? 0,
                'category_id' => $data['category_id']
            ]);

            foreach ($images as $index => $imageFile) {
            
                $path = $imageFile->store('products/images', 'public');

                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => $index === 0, // أول صورة هي الرئيسية
                ]);
            }
    

            
            return $product;
        });
    }
            
        // event(new ProductCreated($product, $uploadedPaths));
}