<?php

namespace App\Services;

use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DoctorOrdersService
{
        public function getOrderDetails(int $id){
            $order = Order::where('user_id' , Auth::id())->findOrFail($id);

            return new OrderResource($order->load('items.product'));
        }
        
        public function getDoctorOrders()
        {
            $orders = Order::where('user_id', Auth::id())
                ->latest()
                ->get();

            return OrderResource::collection($orders);
        }
}