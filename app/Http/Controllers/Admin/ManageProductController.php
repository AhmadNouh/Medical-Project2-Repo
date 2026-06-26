<?php

namespace App\Http\Controllers\Admin;

use App\Actions\CreateProductAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManageProductController extends Controller
{
    use ApiResponseTrait;
    
    public function createProduct(StoreProductRequest $request, CreateProductAction $action): JsonResponse
    {
        
        $product = $action->execute( // action exexute
            $request->validated(),
            $request->file('images')
        );

        $product->load('images');
        return $this->successResponse(
            [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'sku' => $product->sku,
                'category_id' => $product->category_id
            ] ,
            'تم إضافة المنتج بنجاح',
            201
        );

    }

    
}
