<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ApiResponseTrait;

    public function getCategories(){

        $categories = Category::whereNull('parent_id')->with('children')->get();
        return $this->successResponse($categories);
    }

    public function getProductsInCategory(Request $request , string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();

        $query = $category->products()
            ->where('status', 'active')
            ->with('images'); 
            
        if($request->has('search') && !empty($request->search)){
            $query->where('name' , 'like' , '%' . $request->search . '%');
        }    

        $products = $query->latest()->paginate(15);

        return response()->json([
            'status'  => true,
            'message' => 'Category products retrieved successfully.',
            'category' => [
                'id'   => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
            'products' => $products 
        ], 200);
    }

}
