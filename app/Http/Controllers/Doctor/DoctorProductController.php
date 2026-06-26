<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorProductController extends Controller
{
    use ApiResponseTrait;

    public function getProducts(Request $request): JsonResponse
    {   
        $products = Product::filter($request->all())
            ->where('status', 'active') 
            ->with(['images', 'category']) 
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => true,
            'message' => 'Products retrieved successfully.',
            'data' => $products
        ], 200);
    }

   
    public function showProduct(string $slug): JsonResponse
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->with(['images', 'category'])
            ->firstOrFail(); 

        return $this->successResponse($product);
    }
}
