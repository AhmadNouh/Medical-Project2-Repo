<?php

namespace App\Services;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class OrdersManagmentService
{
        public function getOrderDetails(int $id){
            $order = Order::findorfail($id);

            return new OrderResource($order->load('items.product'));
        }
        
        public function getDoctorOrders($doctorId)
        {
            $orders = Order::where('user_id', $doctorId)
                ->latest()
                ->get();

            return OrderResource::collection($orders);
        }
}