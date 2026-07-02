<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateOrderStatusAction
{    
    public function execute(int $orderId, string $newStatus): Order
    {
        return DB::transaction(function () use ($orderId, $newStatus) {
            
            $order = Order::findOrFail($orderId);

            if ($newStatus === 'accepted' && $order->status !== 'accepted') {
                
                foreach ($order->items as $item) {
                    $product = Product::lockForUpdate()->findOrFail($item->product_id);

                    if ($product->stock < $item->quantity) {
                        throw new Exception("لا يمكن قبول الطلب. المخزون غير كافٍ للمنتج: ({$product->name}). المتاح حالياً: {$product->stock}");
                    }

                    $product->decrement('stock', $item->quantity);
                }
            }

            $order->update(['status' => $newStatus]);

            return $order;
        });
    }
}