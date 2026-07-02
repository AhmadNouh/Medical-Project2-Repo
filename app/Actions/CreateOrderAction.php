<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;

class CreateOrderAction
{
    public function execute(array $data, int $userId): Order
    {
        return DB::transaction(function () use ($data, $userId) {
            
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'status' => 'pending',
                'notes' => $data['notes'] ?? null,
                'total_price' => 0
            ]);

            $totalPrice = 0;

            foreach ($data['items'] as $item) {
                $product = Product::lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new Exception("الكمية المطلوبة من المنتج ({$product->name}) غير متوفرة في المستودع حالياً.");
                }

                //$product->decrement('stock', $item['quantity']);

                $itemPrice = $product->price * $item['quantity'];
                $totalPrice += $itemPrice;

                $order->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price 
                ]);
            }

         
            $order->update(['total_price' => $totalPrice]);

            return $order;
        });
    }
}