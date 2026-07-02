<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Requests\StoreOrderRequest;
use App\Actions\CreateOrderAction;
use App\Http\Controllers\Controller;
use App\Services\DoctorOrdersService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use ApiResponseTrait;

    protected $doctorOrdersService;

    public function __construct(DoctorOrdersService $doctorOrdersService){
        $this->doctorOrdersService = $doctorOrdersService;
    }

    public function CreateNewOrder(StoreOrderRequest $request, CreateOrderAction $createOrderAction): JsonResponse
    {
        try {

            $order = $createOrderAction->execute($request->validated(), Auth::id());

            return $this->successResponse(
                $order,
                'تم تسجيل الطلب بنجاح و ينتظر الموافقة',
                201
            );

        } catch (\Exception $e) {
            
            return $this->errorResponse(
                $e->getMessage(),
                422
            );
        }
    }

    public function getOrderDetails(int $id){
        $orderData = $this->doctorOrdersService->getOrderDetails($id);
        
        return $this->successResponse($orderData); 
    }

  
    public function getDoctorOrders(): JsonResponse
    {
        $orders = $this->doctorOrdersService->getDoctorOrders();
            
        return $this->successResponse($orders, 'تم جلب قائمة الطلبات بنجاح');    
    }

}