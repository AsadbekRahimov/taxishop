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
                'order_type' => $data['order_type'] ?? 'pickup',
                'driver_id' => $driverId,
                'customer_name' => $data['customer_name'] ?? null,
                'customer_phone' => $data['customer_phone'] ?? null,
                'delivery_address' => $data['delivery_address'] ?? null,
                'payment_method' => $data['payment_method'],
                'status' => 'pending',
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
            }

            return $order;
        });
    }

    public function confirmOrder(Order $order): void
    {
        DB::transaction(function () use ($order) {
            if ($order->isPickup()) {
                foreach ($order->items as $item) {
                    $this->decrementDriverStock($order->driver_id, $item->product_id, $item->quantity);
                }
            }

            $order->update(['status' => 'confirmed']);
        });
    }

    public function cancelOrder(Order $order): void
    {
        $order->update(['status' => 'cancelled']);
    }

    public function markDelivered(Order $order): void
    {
        DB::transaction(function () use ($order) {
            $order->update(['status' => 'delivered']);
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
