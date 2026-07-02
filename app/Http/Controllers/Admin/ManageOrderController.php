<?php

namespace App\Http\Controllers\Admin;

use App\Actions\UpdateOrderStatusAction;
use App\Http\Controllers\Controller;
use App\Services\OrdersManagmentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManageOrderController extends Controller
{
    use ApiResponseTrait;
    protected $orderManagmentService;

    public function __construct(OrdersManagmentService $orderManagmentService)
    {
        $this->orderManagmentService = $orderManagmentService;
    }

    public function updateOrderStatus(Request $request, UpdateOrderStatusAction $updateStatusAction, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,accepted,processing,completed,cancelled']
        ]);

        try {
            $order = $updateStatusAction->execute($id, $request->status);

            
            return $this->successResponse(
                [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status
                ],
                'تم تحديث حالة الطلب وإدارة المخزون بنجاح'
            );

        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 422);
        }
    }

    public function getOrderDetails(int $id){
        $orderDetails = $this->orderManagmentService->getOrderDetails($id);
        return $this->successResponse($orderDetails);
    }

    public function getDoctorOrders(int $doctorID){
        $doctroOrders = $this->orderManagmentService->getDoctorOrders($doctorID);
        return $this->successResponse($doctroOrders);    
    }
}

