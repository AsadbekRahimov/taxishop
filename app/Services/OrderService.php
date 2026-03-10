<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DriverStock;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function createOrder(array $data, array $cartItems, int $driverId): Order
    {
        return DB::transaction(function () use ($data, $cartItems, $driverId) {
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['subtotal'];
            }

            $order = Order::create([
                'driver_id' => $driverId,
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'],
                'delivery_address' => $data['delivery_address'] ?? null,
                'payment_method' => $data['payment_method'],
                'status' => 'new',
                'total' => $total,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'quantity' => $item['qty'],
                    'price' => $item['product']->price,
                    'subtotal' => $item['subtotal'],
                ]);

                if ($data['payment_method'] === 'cash') {
                    $this->decrementDriverStock($driverId, $item['product']->id, $item['qty']);
                }
            }

            return $order;
        });
    }

    public function decrementDriverStock(int $driverId, int $productId, int $qty): void
    {
        $stock = DriverStock::where('driver_id', $driverId)
            ->where('product_id', $productId)
            ->first();

        if (! $stock) {
            return;
        }

        $newQty = $stock->quantity - $qty;

        if ($newQty <= 0) {
            $stock->delete();
        } else {
            $stock->update(['quantity' => $newQty]);
        }
    }
}
