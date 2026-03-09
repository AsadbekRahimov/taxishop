<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DriverStock;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === Admin ===
        $admin = User::create([
            'name' => 'Администратор',
            'login' => 'admin',
            'password' => 'password',
            'phone' => '+998901234567',
            'role' => 'admin',
            'is_active' => true,
        ]);

        // === Drivers ===
        $drivers = collect([
            ['name' => 'Алексей Петров', 'login' => 'driver1', 'phone' => '+998901111111', 'car_number' => '01A777AA'],
            ['name' => 'Сергей Иванов', 'login' => 'driver2', 'phone' => '+998902222222', 'car_number' => '01B888BB'],
            ['name' => 'Дмитрий Козлов', 'login' => 'driver3', 'phone' => '+998903333333', 'car_number' => '01C999CC'],
        ])->map(fn (array $data) => User::create([
            ...$data,
            'password' => 'password',
            'role' => 'driver',
            'is_active' => true,
        ]));

        // === Categories (5, with nesting) ===
        $electronics = Category::create(['name' => 'Электроника', 'slug' => 'electronics', 'sort_order' => 1]);
        $food = Category::create(['name' => 'Еда и напитки', 'slug' => 'food', 'sort_order' => 2]);
        $hygiene = Category::create(['name' => 'Гигиена', 'slug' => 'hygiene', 'sort_order' => 3]);

        $accessories = Category::create([
            'name' => 'Аксессуары',
            'slug' => 'accessories',
            'parent_id' => $electronics->id,
            'sort_order' => 1,
        ]);
        $snacks = Category::create([
            'name' => 'Снеки',
            'slug' => 'snacks',
            'parent_id' => $food->id,
            'sort_order' => 1,
        ]);

        // === Products (15) ===
        $productsData = [
            ['name' => 'Зарядка USB-C', 'category_id' => $electronics->id, 'price' => 590, 'old_price' => 790, 'description' => 'Быстрая зарядка USB-C для смартфонов'],
            ['name' => 'Наушники проводные', 'category_id' => $electronics->id, 'price' => 350, 'old_price' => null, 'description' => 'Наушники-вкладыши с микрофоном'],
            ['name' => 'Powerbank 5000mAh', 'category_id' => $electronics->id, 'price' => 1290, 'old_price' => 1590, 'description' => 'Портативный аккумулятор'],
            ['name' => 'Кабель Lightning', 'category_id' => $accessories->id, 'price' => 390, 'old_price' => null, 'description' => 'Кабель для зарядки iPhone'],
            ['name' => 'Держатель для телефона', 'category_id' => $accessories->id, 'price' => 490, 'old_price' => 690, 'description' => 'Автомобильный держатель на присоске'],
            ['name' => 'Вода 0.5л', 'category_id' => $food->id, 'price' => 80, 'old_price' => null, 'description' => 'Минеральная вода без газа'],
            ['name' => 'Кола 0.33л', 'category_id' => $food->id, 'price' => 120, 'old_price' => null, 'description' => 'Coca-Cola в жестяной банке'],
            ['name' => 'Энергетик Red Bull', 'category_id' => $food->id, 'price' => 190, 'old_price' => null, 'description' => 'Red Bull 250ml'],
            ['name' => 'Чипсы Lays', 'category_id' => $snacks->id, 'price' => 150, 'old_price' => null, 'description' => 'Чипсы со вкусом сметаны'],
            ['name' => 'Сникерс', 'category_id' => $snacks->id, 'price' => 90, 'old_price' => null, 'description' => 'Шоколадный батончик Snickers'],
            ['name' => 'Жвачка Orbit', 'category_id' => $snacks->id, 'price' => 60, 'old_price' => null, 'description' => 'Жевательная резинка мята'],
            ['name' => 'Влажные салфетки', 'category_id' => $hygiene->id, 'price' => 120, 'old_price' => 150, 'description' => 'Упаковка 15 шт.'],
            ['name' => 'Антисептик', 'category_id' => $hygiene->id, 'price' => 180, 'old_price' => null, 'description' => 'Спрей-антисептик для рук 50ml'],
            ['name' => 'Маска медицинская', 'category_id' => $hygiene->id, 'price' => 30, 'old_price' => 50, 'description' => 'Одноразовая медицинская маска'],
            ['name' => 'Пластырь', 'category_id' => $hygiene->id, 'price' => 70, 'old_price' => null, 'description' => 'Набор пластырей 10 шт.'],
        ];

        $products = collect();
        foreach ($productsData as $data) {
            $product = Product::create([
                'name' => $data['name'],
                'slug' => Str::slug($data['name']),
                'category_id' => $data['category_id'],
                'price' => $data['price'],
                'old_price' => $data['old_price'],
                'description' => $data['description'],
                'main_image' => 'products/placeholder.jpg',
                'is_active' => true,
                'is_deliverable' => true,
            ]);
            $products->push($product);
        }

        // === Driver Stock ===
        foreach ($drivers as $driver) {
            $stockProducts = $products->random(5);
            foreach ($stockProducts as $product) {
                DriverStock::create([
                    'driver_id' => $driver->id,
                    'product_id' => $product->id,
                    'quantity' => rand(2, 20),
                ]);
            }
        }

        // === Orders (10) ===
        $statuses = ['new', 'paid', 'delivered', 'cancelled'];
        $paymentMethods = ['cash', 'qr', 'delivery'];
        $customerNames = ['Иван Сидоров', 'Мария Козлова', 'Олег Петрович', 'Анна Иванова', 'Пётр Николаев'];
        $phones = ['+998911111111', '+998922222222', '+998933333333', '+998944444444', '+998955555555'];

        for ($i = 1; $i <= 10; $i++) {
            $orderProducts = $products->random(rand(1, 4));
            $items = $orderProducts->map(fn (Product $p) => [
                'product_id' => $p->id,
                'quantity' => $qty = rand(1, 3),
                'price' => $p->price,
                'subtotal' => $p->price * $qty,
            ]);

            $order = Order::create([
                'order_number' => 'TS-' . str_pad((string) $i, 6, '0', STR_PAD_LEFT),
                'driver_id' => $drivers->random()->id,
                'customer_name' => $customerNames[array_rand($customerNames)],
                'customer_phone' => $phones[array_rand($phones)],
                'delivery_address' => $i % 3 === 0 ? 'ул. Навои, д. ' . rand(1, 100) : null,
                'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                'status' => $statuses[array_rand($statuses)],
                'total' => $items->sum('subtotal'),
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    ...$item,
                ]);
            }
        }
    }
}
