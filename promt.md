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
