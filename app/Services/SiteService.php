<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SiteService
{
    protected $categories;
    protected $products;

    public function __construct()
    {
        $this->categories = [
            ['slug' => 'napitki', 'name' => 'Напитки', 'icon' => 'fa-solid fa-bottle-water'],
            ['slug' => 'snacks', 'name' => 'Снеки', 'icon' => 'fa-solid fa-cookie'],
            ['slug' => 'parfyumeriya', 'name' => 'Парфюмерия', 'icon' => 'fa-solid fa-spray-can'],
            ['slug' => 'elektronika', 'name' => 'Электроника', 'icon' => 'fa-solid fa-headphones'],
            ['slug' => 'avto', 'name' => 'Авто', 'icon' => 'fa-solid fa-car'],
            ['slug' => 'zdorove', 'name' => 'Здоровье', 'icon' => 'fa-solid fa-pills'],
        ];

        $this->products = [
            [
                'id' => 1,
                'title' => 'Вода минеральная Hydrolife 0.5л',
                'price_new' => '4 000 сум',
                'price_old' => null,
                'image' => 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Water',
                'inCar' => true,
                'category' => 'napitki'
            ],
            [
                'id' => 2,
                'title' => 'Чипсы Lays Краб 80г',
                'price_new' => '15 000 сум',
                'price_old' => '18 000 сум',
                'image' => 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Chips',
                'inCar' => true,
                'category' => 'snacks'
            ],
            [
                'id' => 3,
                'title' => 'Coca-Cola Classic 0.5л',
                'price_new' => '7 000 сум',
                'price_old' => null,
                'image' => 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Cola',
                'inCar' => true,
                'category' => 'napitki'
            ],
            [
                'id' => 4,
                'title' => 'Жевательная резинка Orbit Mint',
                'price_new' => '5 000 сум',
                'price_old' => null,
                'image' => 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Gum',
                'inCar' => true,
                'category' => 'zdorove'
            ],
            [
                'id' => 5,
                'title' => 'Кабель зарядки Type-C',
                'price_new' => '45 000 сум',
                'price_old' => null,
                'image' => 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Cable',
                'inCar' => true,
                'category' => 'elektronika'
            ],
            [
                'id' => 6,
                'title' => 'Парфюм автомобильный VIP',
                'price_new' => '120 000 сум',
                'price_old' => null,
                'image' => 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Perfume',
                'inCar' => false,
                'category' => 'parfyumeriya'
            ],
            [
                'id' => 7,
                'title' => 'PowerBank Xiaomi 10000mAh',
                'price_new' => '250 000 сум',
                'price_old' => '300 000 сум',
                'image' => 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Powerbank',
                'inCar' => false,
                'category' => 'elektronika'
            ],
            [
                'id' => 8,
                'title' => 'Ореховый микс 150г',
                'price_new' => '35 000 сум',
                'price_old' => null,
                'image' => 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Snack',
                'inCar' => false,
                'category' => 'snacks'
            ],
            [
                'id' => 9,
                'title' => 'Энергетик Red Bull 0.25л',
                'price_new' => '20 000 сум',
                'price_old' => null,
                'image' => 'https://placehold.co/400x400/E5E7EB/A3A8B8?text=Energy',
                'inCar' => true,
                'category' => 'napitki'
            ],
        ];
    }

    public function getHomePageData()
    {
        $cart = Session::get('cart', []);
        
        return [
            'title' => 'Главная',
            'categories' => $this->categories,
            'inCarProducts' => array_filter($this->products, fn($p) => $p['inCar']),
            'bestsellerProducts' => array_slice($this->products, 5, 4),
            'cartCount' => array_sum($cart),
            'driverName' => 'Иван Петров'
        ];
    }

    public function getCategoryPageData($slug)
    {
        $category = collect($this->categories)->firstWhere('slug', $slug);
        $categoryName = $category ? $category['name'] : 'Все товары';
        
        $products = $slug 
            ? array_filter($this->products, fn($p) => $p['category'] === $slug)
            : $this->products;
        
        $cart = Session::get('cart', []);
        
        return [
            'title' => $categoryName,
            'categoryName' => $categoryName,
            'categorySlug' => $slug,
            'products' => $products,
            'cartCount' => array_sum($cart)
        ];
    }

    public function getProductPageData($id)
    {
        $product = collect($this->products)->firstWhere('id', $id);
        
        if (!$product) {
            abort(404);
        }
        
        $cart = Session::get('cart', []);
        
        return [
            'title' => $product['title'],
            'product' => $product,
            'cartCount' => array_sum($cart),
            'driverName' => 'Иван Петров'
        ];
    }

    public function getCartData()
    {
        $cart = Session::get('cart', []);
        $cartItems = Session::get('cart_items', []);
        
        $total = 0;
        $detailedItems = [];
        
        foreach ($cartItems as $productId => $quantity) {
            $product = collect($this->products)->firstWhere('id', $productId);
            if ($product) {
                $price = (int) str_replace([' ', 'сум'], '', $product['price_new']);
                $itemTotal = $price * $quantity;
                $total += $itemTotal;
                
                $detailedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => number_format($itemTotal, 0, ' ', ' ') . ' сум'
                ];
            }
        }
        
        return [
            'title' => 'Корзина',
            'cartItems' => $detailedItems,
            'cartCount' => array_sum($cart),
            'total' => number_format($total, 0, ' ', ' ') . ' сум'
        ];
    }

    public function getCheckoutData()
    {
        $cart = Session::get('cart', []);
        $cartItems = Session::get('cart_items', []);
        
        $total = 0;
        $detailedItems = [];
        
        foreach ($cartItems as $productId => $quantity) {
            $product = collect($this->products)->firstWhere('id', $productId);
            if ($product) {
                $price = (int) str_replace([' ', 'сум'], '', $product['price_new']);
                $itemTotal = $price * $quantity;
                $total += $itemTotal;
                
                $detailedItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $itemTotal
                ];
            }
        }
        
        return [
            'title' => 'Оформление заказа',
            'cartItems' => $detailedItems,
            'cartCount' => array_sum($cart),
            'total' => number_format($total, 0, ' ', ' ') . ' сум'
        ];
    }

    public function getThanksPageData()
    {
        return [
            'title' => 'Спасибо',
            'orderNumber' => 'TS-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
            'hideHeader' => false
        ];
    }
    
    public function getOrderThanksData($number = null)
    {
        $order = Session::get('last_order', [
            'number' => $number ?? 'TS-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
            'payment' => 'cash_to_driver',
            'total' => Session::get('order_total', '54 000 сум')
        ]);
        
        return [
            'title' => 'Спасибо за заказ',
            'orderNumber' => $order['number'],
            'paymentMethod' => $order['payment'],
            'total' => $order['total'],
            'hideHeader' => false
        ];
    }

    public function addToCart($data)
    {
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        
        if (!$productId) {
            return ['success' => false, 'message' => 'Product ID required'];
        }
        
        $cart = Session::get('cart', []);
        $cartItems = Session::get('cart_items', []);
        
        if (isset($cartItems[$productId])) {
            $cartItems[$productId] += $quantity;
        } else {
            $cartItems[$productId] = $quantity;
        }
        
        $cart[] = $productId;
        
        Session::put('cart', $cart);
        Session::put('cart_items', $cartItems);
        
        return [
            'success' => true,
            'cartCount' => array_sum($cart)
        ];
    }

    public function removeFromCart($data)
    {
        $productId = $data['product_id'] ?? null;
        
        if (!$productId) {
            return ['success' => false, 'message' => 'Product ID required'];
        }
        
        $cart = Session::get('cart', []);
        $cartItems = Session::get('cart_items', []);
        
        unset($cartItems[$productId]);
        
        $cart = array_values(array_filter($cart, fn($id) => $id != $productId));
        
        Session::put('cart', $cart);
        Session::put('cart_items', $cartItems);
        
        return [
            'success' => true,
            'cartCount' => array_sum($cart)
        ];
    }

    public function updateCart($data)
    {
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        
        if (!$productId || $quantity < 1) {
            return ['success' => false, 'message' => 'Invalid data'];
        }
        
        $cart = Session::get('cart', []);
        $cartItems = Session::get('cart_items', []);
        
        $oldQuantity = $cartItems[$productId] ?? 0;
        $diff = $quantity - $oldQuantity;
        
        if ($diff > 0) {
            for ($i = 0; $i < $diff; $i++) {
                $cart[] = $productId;
            }
        } else {
            $cart = array_slice($cart, 0, count($cart) + $diff);
        }
        
        $cartItems[$productId] = $quantity;
        
        Session::put('cart', $cart);
        Session::put('cart_items', $cartItems);
        
        return [
            'success' => true,
            'cartCount' => array_sum($cart)
        ];
    }

    public function placeOrder($data)
    {
        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return ['success' => false, 'errors' => ['cart' => 'Cart is empty']];
        }
        
        $order = [
            'number' => 'TS-' . str_pad(random_int(1, 999999), 6, '0', STR_PAD_LEFT),
            'name' => $data['name'] ?? '',
            'phone' => $data['phone'] ?? '',
            'payment' => $data['payment_method'] ?? 'cash_to_driver',
            'address' => $data['delivery_address'] ?? '',
            'items' => Session::get('cart_items', []),
            'total' => $data['total'] ?? 0
        ];
        
        // Сохраняем данные заказа для страницы спасибо
        Session::put('last_order', $order);
        Session::put('order_total', $order['total']);
        
        Session::forget('cart');
        Session::forget('cart_items');
        
        return [
            'success' => true,
            'order' => $order
        ];
    }
}
