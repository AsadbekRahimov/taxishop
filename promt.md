Ты — опытный Laravel/Filament-разработчик. Создай полную админ-панель на Filament 5 для проекта TaxiShop.

=== КОНТЕКСТ ПРОЕКТА ===
TaxiShop — интернет-магазин на планшетах в такси. Стек: Laravel 12, PHP 8.2, PgSql 16, Filament 5. Владелец таксопарка (Admin) управляет товарами, категориями, водителями, складом и заказами.

=== СТРУКТУРА БАЗЫ ДАННЫХ ===

Таблица users (drivers + admins):
- id: bigint PK auto-increment
- name: varchar(255) NOT NULL — Имя/
- login: varchar(100) UNIQUE NOT NULL — Логин
- password: varchar(255) NOT NULL — bcrypt hash
- phone: varchar(20) NULL — Телефон
- role: enum('admin','driver') NOT NULL — Роль
- car_number: varchar(20) NULL — Номер машины (только для driver)
- is_active: boolean NOT NULL default true
- remember_token: varchar(100) NULL
- created_at, updated_at: timestamps

Таблица categories:
- id: bigint PK
- name: varchar(255) NOT NULL
- slug: varchar(255) UNIQUE NOT NULL
- parent_id: bigint FK NULL → categories(id) — self-referencing (до 3 уровней вложенности)
- icon: varchar(255) NULL — путь к иконке
- sort_order: int NOT NULL default 0
- timestamps

Таблица products:
- id: bigint PK
- category_id: bigint FK NOT NULL → categories(id)
- name: varchar(255) NOT NULL
- slug: varchar(255) UNIQUE NOT NULL
- description: text NULL
- price: decimal(10,2) NOT NULL — цена продажи
- old_price: decimal(10,2) NULL — старая цена (для отображения скидки)
- main_image: varchar(500) NOT NULL — путь к главному фото
- is_active: boolean NOT NULL default true
- is_deliverable: boolean NOT NULL default true — можно ли заказать с доставкой
- timestamps

Таблица product_images:
- id: bigint PK
- product_id: bigint FK NOT NULL → products(id) CASCADE DELETE
- image_path: varchar(500) NOT NULL
- sort_order: int NOT NULL default 0

Таблица driver_stock (склад товаров в машине):
- id: bigint PK
- driver_id: bigint FK NOT NULL → users(id)
- product_id: bigint FK NOT NULL → products(id)
- quantity: int NOT NULL — количество в машине
- UNIQUE(driver_id, product_id)

Таблица orders:
- id: bigint PK
- order_number: varchar(20) UNIQUE NOT NULL — формат TS-000001
- driver_id: bigint FK NOT NULL → users(id) — через какого водителя заказ
- customer_name: varchar(255) NOT NULL
- customer_phone: varchar(20) NOT NULL
- delivery_address: text NULL — заполняется только для доставки
- payment_method: enum('cash','qr','delivery') NOT NULL
- status: enum('new','paid','delivered','cancelled') NOT NULL default 'new'
- total: decimal(12,2) NOT NULL
- timestamps

Таблица order_items:
- id: bigint PK
- order_id: bigint FK NOT NULL → orders(id) CASCADE DELETE
- product_id: bigint FK NOT NULL → products(id)
- quantity: int NOT NULL
- price: decimal(10,2) NOT NULL — цена на момент заказа
- subtotal: decimal(10,2) NOT NULL — price * quantity

=== ЧТО НУЖНО СГЕНЕРИРОВАТЬ ===

1. МИГРАЦИИ (database/migrations/)
   Все 7 таблиц с правильными foreign keys, индексами, каскадным удалением.

2. МОДЕЛИ (app/Models/)
   User, Category, Product, ProductImage, DriverStock, Order, OrderItem
   С отношениями (belongsTo, hasMany, belongsToMany), casts, fillable, sluggable.

3. FILAMENT RESOURCES (app/Filament/Resources/)

   3.1. CategoryResource
    - Таблица: name, slug (auto-generate), parent (select из categories), icon (FileUpload), sort_order, кол-во товаров (count)
    - Форма: name, slug (auto из name), parent_id (Select::relationship), icon (FileUpload::image), sort_order
    - Фильтры: родительская категория, только корневые
    - Действия: create, edit, delete (с проверкой что нет товаров)
    - Tree view или группировка по parent

   3.2. ProductResource
    - Таблица: main_image (ImageColumn), name, category, price, old_price, is_active (ToggleColumn), is_deliverable, created_at
    - Форма:
        * Section «Основное»: name, slug (auto), category_id (Select::relationship с поиском), description (RichEditor)
        * Section «Цены»: price (TextInput::numeric), old_price (TextInput::numeric)
        * Section «Медиа»: main_image (FileUpload::image, directory: products), доп. фото (Repeater: image_path + sort_order или FileUpload::multiple)
        * Section «Настройки»: is_active (Toggle), is_deliverable (Toggle)
    - Фильтры: по категории, по статусу (активные/неактивные), по наличию скидки (old_price != null)
    - Bulk actions: активировать, деактивировать, удалить

   3.3. DriverResource (UserResource для роли driver)
    - Таблица: name, login, phone, car_number, is_active (ToggleColumn), кол-во товаров на складе (count driver_stock)
    - Форма: name, login, password (хешировать, показывать только при создании), phone, car_number, is_active
    - Фильтры: активные/неактивные
    - Relation Manager: DriverStockRelationManager
        * Таблица: product (name + фото), quantity
        * Действия: добавить товар (Select product + quantity), изменить кол-во, удалить
        * При добавлении — Select с поиском по товарам, исключить уже добавленные

   3.4. OrderResource
    - Таблица: order_number, customer_name, customer_phone, driver (имя), payment_method (Badge с цветами: cash=success, qr=info, delivery=warning), status (Badge: new=gray, paid=success, delivered=primary, cancelled=danger), total, created_at
    - Форма (только просмотр + смена статуса):
        * Информация о заказе (Infolist или disabled fields)
        * Select для смены статуса
        * Repeater/Table для order_items (read-only): product, quantity, price, subtotal
    - Фильтры: по статусу, по способу оплаты, по водителю, по дате
    - Bulk actions: сменить статус

4. FILAMENT DASHBOARD (app/Filament/Pages/Dashboard.php)
   Виджеты:
    - StatsOverviewWidget:
        * Заказов сегодня (count orders where created_at = today)
        * Выручка сегодня (sum orders.total where status != cancelled AND created_at = today)
        * Всего товаров (count products where is_active)
        * Активных водителей (count users where role=driver AND is_active)
    - LatestOrdersWidget (Table): последние 10 заказов
    - Chart: продажи за последние 7 дней (LineChart)
    - TopProductsWidget: топ-5 товаров по количеству заказов

5. FILAMENT PANEL PROVIDER (app/Providers/Filament/AdminPanelProvider.php)
    - Path: /admin
    - Auth: login по полю 'login' (не email!)
    - Только пользователи с role='admin' могут войти
    - Брендинг: название «TaxiShop Admin», зелёная тема
    - Локализация: русский

=== ВАЖНЫЕ ДЕТАЛИ ===
- Авторизация в Filament по полю login, НЕ email (нужно кастомизировать Login page)
- User model использует поле 'login' вместо 'email' для аутентификации
- Slug генерируется автоматически из name (для categories и products)
- Загрузка изображений через Filament FileUpload в storage/app/public/{products,categories}
- Все формы с русскими лейблами
- Валидация: price > 0, old_price > price (если задана), quantity > 0, phone формат +998..., login unique

=== СТРУКТУРА ФАЙЛОВ ===
app/
├── Models/
│   ├── User.php
│   ├── Category.php
│   ├── Product.php
│   ├── ProductImage.php
│   ├── DriverStock.php
│   ├── Order.php
│   └── OrderItem.php
├── Filament/
│   ├── Resources/
│   │   ├── CategoryResource.php
│   │   ├── CategoryResource/Pages/
│   │   ├── ProductResource.php
│   │   ├── ProductResource/Pages/
│   │   ├── DriverResource.php
│   │   ├── DriverResource/Pages/
│   │   ├── DriverResource/RelationManagers/StockRelationManager.php
│   │   ├── OrderResource.php
│   │   └── OrderResource/Pages/
│   ├── Widgets/
│   │   ├── StatsOverview.php
│   │   ├── LatestOrders.php
│   │   ├── SalesChart.php
│   │   └── TopProducts.php
│   └── Pages/
│       └── Dashboard.php
├── Providers/
│   └── Filament/
│       └── AdminPanelProvider.php
database/
├── migrations/
│   ├── create_users_table.php
│   ├── create_categories_table.php
│   ├── create_products_table.php
│   ├── create_product_images_table.php
│   ├── create_driver_stock_table.php
│   ├── create_orders_table.php
│   └── create_order_items_table.php
└── seeders/
└── DatabaseSeeder.php (admin user + тестовые данные)

Сгенерируй ВСЕ файлы полностью, с рабочим кодом. Каждый файл — с полным содержимым, не сокращай. Добавь DatabaseSeeder с тестовым админом (login: admin, password: password) и 3 тестовыми водителями, 5 категорий, 15 товаров.


Ты — опытный Laravel full-stack разработчик. У меня есть:
1) Готовые HTML/CSS/Alpine.js шаблоны фронтенда (7 страниц)
2) Готовая админ-панель на Filament 5 с моделями и миграциями
3) ТЗ/PRD проекта TaxiShop

Задача: конвертировать HTML-шаблоны в Blade-шаблоны и создать полный бэкенд (контроллеры, роуты, middleware, сервисы) для Laravel 11 монолита. НЕ API — рендерим Blade-шаблоны на сервере.

=== КОНТЕКСТ ПРОЕКТА ===
TaxiShop — интернет-магазин на планшетах в такси. Монолитное Laravel 11 приложение. Blade шаблоны + Alpine.js для интерактивности. Session-based корзина. Session auth с remember_token.

=== СУЩЕСТВУЮЩИЕ МОДЕЛИ (уже созданы в Filament) ===
User, Category, Product, ProductImage, DriverStock, Order, OrderItem
(все связи и миграции уже есть)

=== ЗАДАЧА 1: BLADE LAYOUT И ШАБЛОНЫ ===

Создать Blade layout и конвертировать HTML в Blade:

resources/views/
├── layouts/
│   ├── shop.blade.php          — Основной layout магазина (header, footer, @yield('content'))
│   └── auth.blade.php          — Layout для страницы логина (минимальный)
├── auth/
│   └── login.blade.php         — Форма логина
├── shop/
│   ├── home.blade.php          — Главная страница
│   ├── category.blade.php      — Страница категории
│   ├── product.blade.php       — Страница товара (PDP)
│   ├── cart.blade.php          — Корзина
│   ├── checkout.blade.php      — Оформление заказа
│   └── thanks.blade.php        — Спасибо за заказ
└── components/
├── product-card.blade.php  — Карточка товара (переиспользуемый компонент)
├── breadcrumbs.blade.php   — Хлебные крошки
└── header.blade.php        — Шапка сайта

Правила конвертации HTML → Blade:
- Все статические тексты оставить на русском
- Все данные выводить через {{ $variable }} (XSS-safe)
- {!! !!} ТОЛЬКО для доверенного HTML (description товара, если нужен RichText)
- Условия: @if, @unless, @empty, @isset
- Циклы: @foreach, @forelse (с @empty для пустых коллекций)
- Компоненты: <x-product-card :product="$product" :inStock="$inStock" />
- Ассеты: {{ asset('storage/products/photo.jpg') }}
- Формы: @csrf во всех формах, @method('DELETE') где нужно

=== ЗАДАЧА 2: КОНТРОЛЛЕРЫ ===

app/Http/Controllers/Auth/LoginController.php
- showLoginForm(): GET /auth/login → view('auth.login')
- login(): POST /auth/login
    * Validate: login (required|string), password (required|string), remember (boolean)
    * Auth::attempt(['login' => $login, 'password' => $password, 'is_active' => true], $remember)
    * ВАЖНО: авторизация по полю 'login', НЕ 'email'
    * Проверить что role === 'driver' ИЛИ 'admin'
    * Rate limiting: 5 попыток в минуту (throttle middleware)
    * При успехе → redirect('/')
    * При ошибке → back()->withErrors()
- logout(): POST /auth/logout → Auth::logout() → redirect('/auth/login')

app/Http/Controllers/Shop/HomeController.php
- index(): GET /
    * $categories = Category::whereNull('parent_id')->with('children')->orderBy('sort_order')->get()
    * $driverStock = auth()->user()->driverStock()->with('product')->get() — товары в машине
    * $inStockProductIds = $driverStock->pluck('product_id')->toArray()
    * $hits = Product::where('is_active', true)->withCount('orderItems')->orderByDesc('order_items_count')->take(8)->get()
    * return view('shop.home', compact('categories', 'driverStock', 'inStockProductIds', 'hits'))

app/Http/Controllers/Shop/CategoryController.php
- show($slug): GET /category/{slug}
    * $category = Category::where('slug', $slug)->firstOrFail()
    * $products = $category->products()->where('is_active', true)->paginate(12)
    * Поддержка сортировки: ?sort=price_asc|price_desc|popular|new
    * $breadcrumbs = собрать цепочку parent категорий
    * $inStockProductIds из driver_stock текущего водителя

app/Http/Controllers/Shop/ProductController.php
- show($slug): GET /product/{slug}
    * $product = Product::where('slug', $slug)->where('is_active', true)->with(['category.parent', 'images'])->firstOrFail()
    * $inStock = DriverStock::where('driver_id', auth()->id())->where('product_id', $product->id)->exists()
    * $stockQty = ... количество в машине (если есть)
    * $breadcrumbs = цепочка категорий
    * return view('shop.product', compact('product', 'inStock', 'stockQty', 'breadcrumbs'))

app/Http/Controllers/Shop/CartController.php
Корзина хранится в session: session('cart') = [product_id => ['qty' => N, 'payment_method' => '...'], ...]
- index(): GET /cart
    * $cart = session('cart', [])
    * $products = Product::whereIn('id', array_keys($cart))->get()
    * Собрать массив с qty, subtotal
    * $total = сумма
- add(): POST /cart/add
    * Validate: product_id (required|exists:products,id), qty (integer|min:1, default 1), payment_method (nullable|in:cash,qr,delivery)
    * Добавить в session('cart')
    * Если payment_method передан → redirect('/checkout')
    * Иначе → redirect('/cart')
- update(): POST /cart/update
    * Validate: product_id, qty
    * Обновить session('cart')
    * redirect('/cart')
- remove($id): DELETE /cart/remove/{id}
    * Удалить product_id из session('cart')
    * redirect('/cart')

app/Http/Controllers/Shop/CheckoutController.php
- show(): GET /checkout
    * Если корзина пуста → redirect('/')
    * $cart, $products, $total (как в CartController)
    * return view('shop.checkout', ...)
- store(): POST /checkout
    * Validate:
        - customer_name: required|string|max:255
        - customer_phone: required|string|regex:/^\+998\d{9}$/
        - payment_method: required|in:cash,qr,delivery
        - delivery_address: required_if:payment_method,delivery|string
    * DB::transaction:
        - Создать Order (order_number = 'TS-' . str_pad(Order::max('id') + 1, 6, '0', STR_PAD_LEFT))
        - Создать OrderItems из корзины
        - Если payment_method === 'cash' и товар в driver_stock → уменьшить quantity в driver_stock
        - Очистить session('cart')
    * redirect("/order/{$order->order_number}/thanks")

app/Http/Controllers/Shop/SearchController.php
- search(): GET /search?q=
    * $products = Product::where('is_active', true)->where('name', 'LIKE', "%{$q}%")->paginate(12)

=== ЗАДАЧА 3: ROUTES ===

routes/web.php:
// Auth
Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/auth/logout', [LoginController::class, 'logout'])->name('logout');

// Shop (auth:driver middleware)
Route::middleware(['auth', 'driver'])->group(function () {
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.show');
Route::get('/search', [SearchController::class, 'search'])->name('search');

    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/order/{number}/thanks', [CheckoutController::class, 'thanks'])->name('order.thanks');
});

=== ЗАДАЧА 4: MIDDLEWARE ===

app/Http/Middleware/EnsureIsDriver.php
- Проверяет auth()->user()->role === 'driver'
- Если нет → abort(403)
- Зарегистрировать в bootstrap/app.php как alias 'driver'

app/Http/Middleware/EnsureIsAdmin.php (уже может быть от Filament)
- Проверяет role === 'admin'

=== ЗАДАЧА 5: SERVICE LAYER (опционально, но рекомендуется) ===

app/Services/CartService.php
- getCart(): array — получить корзину из session
- addItem(int $productId, int $qty, ?string $paymentMethod): void
- updateItem(int $productId, int $qty): void
- removeItem(int $productId): void
- getTotal(): float
- getItemsCount(): int
- clear(): void

app/Services/OrderService.php
- createOrder(array $data, array $cartItems, int $driverId): Order
- generateOrderNumber(): string
- decrementDriverStock(int $driverId, int $productId, int $qty): void

=== ЗАДАЧА 6: HEADER КОРЗИНЫ (глобальный счётчик) ===

app/View/Composers/CartComposer.php (или middleware):
- На каждой странице в header показывать количество товаров в корзине
- View::composer('components.header', function ($view) { $view->with('cartCount', app(CartService::class)->getItemsCount()); })
- Зарегистрировать в AppServiceProvider

=== БЕЗОПАСНОСТЬ (КРИТИЧНО) ===
- @csrf во ВСЕХ формах
- {{ }} для ВСЕГО пользовательского вывода (XSS protection)
- Eloquent ORM — НИКОГДА raw queries с user input (SQL injection)
- Rate limiting на /auth/login: throttle:5,1
- Валидация ВСЕХ входных данных в контроллерах
- FileUpload: валидация MIME-типов (image/*), макс 2MB
- HTTPS only в production (secure cookies)
- X-Frame-Options: SAMEORIGIN (планшет может быть в iframe)

=== ВАЖНЫЕ НЮАНСЫ ===
- User model: поле аутентификации — 'login' (не email). В модели User добавить:
  public function getAuthIdentifierName() { return 'login'; }
  и в LoginController использовать Auth::attempt(['login' => ..., 'password' => ...])
- Корзина — полностью session-based, клиент (пассажир) не регистрируется
- driver_id в заказе — это auth()->id() (текущий залогиненный водитель)
- При оплате наличными — автоматически уменьшать driver_stock
- Изображения хранятся в storage/app/public/, доступ через asset('storage/...')
- Не забыть php artisan storage:link

Сгенерируй ВСЕ файлы полностью. Каждый контроллер, middleware, service, blade-шаблон — с полным рабочим кодом. Blade-шаблоны должны использовать Tailwind CSS классы и Alpine.js для интерактивности. Это монолитное приложение — всё рендерится на сервере через Blade, Alpine.js только для UI-интерактивности на клиенте (переключение фото, открытие модалок, валидация форм).
