# TaxiShop — Структура проекта (для AI-ассистентов)

Этот файл описывает архитектуру и расположение всех ключевых файлов проекта TaxiShop, чтобы AI-ассистент мог быстро ориентироваться в кодовой базе.

---

## Общая информация

- **Фреймворк:** Laravel 12 (PHP 8.2+)
- **Админ-панель:** Filament v5
- **Frontend:** Blade + Alpine.js + TailwindCSS 4
- **БД:** SQLite (настраиваемо)
- **Сборка:** Vite 7

---

## Структура директорий

```
taxishop/
├── app/                          # Основной код приложения
│   ├── Filament/                 # Админ-панель Filament
│   │   ├── Pages/Auth/           # Кастомная страница логина админки
│   │   ├── Resources/            # CRUD-ресурсы админки
│   │   │   ├── CategoryResource/ # Управление категориями
│   │   │   ├── DriverResource/   # Управление водителями + их стоком
│   │   │   ├── OrderResource/    # Просмотр и управление заказами
│   │   │   └── ProductResource/  # Управление товарами
│   │   └── Widgets/              # Виджеты дашборда (статистика, графики)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/
│   │   │   │   └── LoginController.php      # Логин водителей (по полю login)
│   │   │   ├── Shop/
│   │   │   │   ├── HomeController.php        # Главная: категории + бестселлеры
│   │   │   │   ├── ProductController.php     # Страница товара + breadcrumbs
│   │   │   │   ├── CategoryController.php    # Список товаров в категории + сортировка
│   │   │   │   ├── CartController.php        # Добавление/обновление/удаление корзины
│   │   │   │   ├── CheckoutController.php    # Оформление заказа + страница thanks
│   │   │   │   └── SearchController.php      # Поиск товаров по названию
│   │   │   ├── LocaleController.php          # Переключение языка (ru/en/uz)
│   │   │   └── SiteController.php            # Legacy контроллер (не используется)
│   │   └── Middleware/
│   │       ├── EnsureIsDriver.php            # Проверка роли driver/admin
│   │       ├── SetLocale.php                 # Установка языка из сессии
│   │       └── SetAdminLocale.php            # Язык для админ-панели
│   ├── Models/
│   │   ├── User.php              # Пользователь (login, role, car_number, is_active)
│   │   ├── Product.php           # Товар (name, slug, price, old_price, main_image)
│   │   ├── Category.php          # Категория (name, slug, parent_id, icon, sort_order)
│   │   ├── Order.php             # Заказ (order_number, status, payment_method, total)
│   │   ├── OrderItem.php         # Позиция заказа (product_id, quantity, price, subtotal)
│   │   ├── DriverStock.php       # Сток водителя (driver_id, product_id, quantity)
│   │   └── ProductImage.php      # Доп. изображения товара (image_path, sort_order)
│   ├── Services/
│   │   ├── CartService.php       # Логика корзины (сессионная, add/update/remove/total)
│   │   ├── OrderService.php      # Создание заказа + декремент стока (транзакция)
│   │   └── SiteService.php       # Legacy (захардкоженные данные, не используется)
│   └── Providers/
│       ├── AppServiceProvider.php
│       └── Filament/
│           └── AdminPanelProvider.php  # Конфигурация Filament (путь /admin, тема, SPA)
│
├── config/                       # Конфигурация Laravel
│   ├── app.php                   # locale: ru, fallback: en, available: [ru, en, uz]
│   ├── auth.php                  # Guards и providers (стандарт)
│   ├── database.php              # SQLite по умолчанию
│   └── filament.php              # Filament конфигурация
│
├── database/
│   ├── migrations/               # Миграции БД (в порядке выполнения)
│   │   ├── ..._modify_users_table_for_taxishop.php   # Добавление полей login, role и т.д.
│   │   ├── ..._create_categories_table.php            # Категории с иерархией
│   │   ├── ..._create_products_table.php              # Товары
│   │   ├── ..._create_product_images_table.php        # Доп. изображения
│   │   ├── ..._create_driver_stock_table.php          # Сток водителей
│   │   ├── ..._create_orders_table.php                # Заказы
│   │   └── ..._create_order_items_table.php           # Позиции заказов
│   ├── factories/                # Фабрики моделей для тестов
│   └── seeders/
│       └── DatabaseSeeder.php    # Сидер с тестовыми данными
│
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   │   ├── shop.blade.php    # Основной layout (header, footer, cart badge, lang switcher)
│   │   │   ├── auth.blade.php    # Layout для страницы входа
│   │   │   └── site.blade.php    # Legacy layout
│   │   ├── shop/
│   │   │   ├── home.blade.php    # Главная: баннер-карусель, категории, бестселлеры
│   │   │   ├── product.blade.php # Детальная страница товара
│   │   │   ├── category.blade.php# Товары в категории + сортировка
│   │   │   ├── cart.blade.php    # Корзина с управлением количеством
│   │   │   ├── checkout.blade.php# Форма оформления заказа
│   │   │   ├── search.blade.php  # Результаты поиска
│   │   │   └── thanks.blade.php  # Подтверждение заказа
│   │   ├── auth/
│   │   │   └── login.blade.php   # Форма входа для водителей
│   │   └── components/
│   │       ├── product-card.blade.php    # Карточка товара (переиспользуемая)
│   │       ├── breadcrumbs.blade.php     # Хлебные крошки
│   │       ├── header.blade.php          # Шапка сайта
│   │       ├── footer.blade.php          # Подвал сайта
│   │       └── category-item.blade.php   # Карточка категории
│   ├── css/
│   │   └── app.css               # Стили (TailwindCSS)
│   └── js/
│       ├── app.js                # Точка входа JS
│       └── bootstrap.js          # Инициализация axios
│
├── routes/
│   ├── web.php                   # ВСЕ маршруты (auth, shop, cart, checkout)
│   └── console.php               # Консольные команды
│
├── lang/                         # Переводы
│   ├── ru/shop.php               # Русский (основной)
│   ├── en/shop.php               # Английский
│   └── uz/shop.php               # Узбекский
│
├── public/                       # Публичная директория
│   ├── images/                   # Изображения (баннеры, товары)
│   └── js/filament/              # Скомпилированные JS Filament
│
├── tests/                        # Тесты
│   ├── Feature/                  # Функциональные тесты
│   └── Unit/                     # Unit-тесты
│
├── storage/                      # Хранилище (логи, кэш, загрузки)
├── composer.json                 # PHP-зависимости
├── package.json                  # Node-зависимости
├── vite.config.js                # Конфигурация Vite
├── .env.example                  # Шаблон переменных окружения
└── docs/
    ├── PRD.md                    # Документ требований продукта
    └── PROJECT_STRUCTURE.md      # Этот файл
```

---

## Схема базы данных

### users
| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | |
| name | string | Имя водителя/админа |
| login | string unique | Логин для входа |
| password | string | Хешированный пароль |
| phone | string nullable | Телефон |
| role | enum(admin, driver) | Роль |
| car_number | string nullable | Гос. номер авто |
| is_active | boolean default:true | Активен ли аккаунт |

### categories
| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | |
| name | string | Название категории |
| slug | string unique | URL-slug |
| parent_id | bigint nullable FK→categories | Родительская категория |
| icon | string nullable | Иконка (Font Awesome класс) |
| sort_order | integer default:0 | Порядок сортировки |

### products
| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | |
| category_id | bigint FK→categories | Категория |
| name | string | Название |
| slug | string unique | URL-slug |
| description | text nullable | Описание (HTML) |
| price | decimal(10,2) | Текущая цена |
| old_price | decimal(10,2) nullable | Старая цена (для скидки) |
| main_image | string nullable | Путь к главному изображению |
| is_active | boolean default:true | Активен ли товар |
| is_deliverable | boolean default:false | Доступен для доставки |

### product_images
| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | |
| product_id | bigint FK→products (cascade) | Товар |
| image_path | string | Путь к изображению |
| sort_order | integer default:0 | Порядок показа |

### driver_stock
| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | |
| driver_id | bigint FK→users (cascade) | Водитель |
| product_id | bigint FK→products (cascade) | Товар |
| quantity | integer default:0 | Количество в машине |
| *unique* | (driver_id, product_id) | Один товар — одна запись |

### orders
| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | |
| order_number | string unique | Номер заказа (TS-######) |
| driver_id | bigint FK→users (restrict) | Водитель, оформивший заказ |
| customer_name | string | Имя покупателя |
| customer_phone | string | Телефон покупателя (+998...) |
| delivery_address | string nullable | Адрес доставки |
| payment_method | enum(cash, qr, delivery) | Способ оплаты |
| status | enum(new, paid, delivered, cancelled) default:new | Статус |
| total | decimal(10,2) | Итоговая сумма |

### order_items
| Поле | Тип | Описание |
|------|-----|----------|
| id | bigint PK | |
| order_id | bigint FK→orders (cascade) | Заказ |
| product_id | bigint FK→products (restrict) | Товар |
| quantity | integer | Количество |
| price | decimal(10,2) | Цена на момент заказа |
| subtotal | decimal(10,2) | quantity × price |

---

## Маршруты (routes/web.php)

### Аутентификация
| Метод | URL | Контроллер | Имя маршрута |
|-------|-----|-----------|--------------|
| GET | /auth/login | LoginController@showLoginForm | login |
| POST | /auth/login | LoginController@login | — |
| POST | /auth/logout | LoginController@logout | logout |

### Переключение языка
| Метод | URL | Контроллер | Имя маршрута |
|-------|-----|-----------|--------------|
| GET | /locale/{locale} | LocaleController@switch | locale.switch |

### Магазин (требует auth + роль driver/admin)
| Метод | URL | Контроллер | Имя маршрута |
|-------|-----|-----------|--------------|
| GET | / | HomeController@index | home |
| GET | /category/{slug} | CategoryController@show | category.show |
| GET | /product/{slug} | ProductController@show | product.show |
| GET | /search | SearchController@search | search |

### Корзина
| Метод | URL | Контроллер | Имя маршрута |
|-------|-----|-----------|--------------|
| GET | /cart | CartController@index | cart.index |
| POST | /cart/add | CartController@add | cart.add |
| POST | /cart/update | CartController@update | cart.update |
| DELETE | /cart/remove/{id} | CartController@remove | cart.remove |

### Оформление заказа
| Метод | URL | Контроллер | Имя маршрута |
|-------|-----|-----------|--------------|
| GET | /checkout | CheckoutController@show | checkout.show |
| POST | /checkout | CheckoutController@store | checkout.store |
| GET | /order/{number}/thanks | CheckoutController@thanks | order.thanks |

### Админ-панель
| URL | Описание |
|-----|----------|
| /admin | Filament Dashboard (авто-маршруты для всех ресурсов) |

---

## Ключевые сервисы

### CartService (`app/Services/CartService.php`)
Управление сессионной корзиной:
- `getCart()` — получить массив корзины
- `addItem(productId, qty, paymentMethod)` — добавить/обновить позицию
- `updateItem(productId, qty)` — изменить количество
- `removeItem(productId)` — удалить позицию
- `getItemsWithProducts()` — получить позиции с данными товаров
- `getTotal()` — рассчитать итого
- `getItemsCount()` — количество позиций
- `clear()` — очистить корзину
- `isEmpty()` — проверка пустоты

### OrderService (`app/Services/OrderService.php`)
Создание заказов в транзакции:
- `createOrder(data, cartItems, driverId)` — создаёт Order + OrderItems, генерирует номер TS-######
- `decrementDriverStock(driverId, productId, qty)` — уменьшает сток; удаляет запись если qty = 0
- При оплате `cash` — сток списывается сразу
- При `qr`/`delivery` — сток НЕ списывается (ожидание подтверждения)

---

## Связи моделей

```
User (driver)
 ├── hasMany → DriverStock (товары в машине)
 └── hasMany → Order (оформленные заказы)

Category
 ├── hasMany → Category (children, через parent_id)
 ├── belongsTo → Category (parent)
 └── hasMany → Product

Product
 ├── belongsTo → Category
 ├── hasMany → ProductImage
 ├── hasMany → DriverStock
 └── hasMany → OrderItem

Order
 ├── belongsTo → User (driver)
 └── hasMany → OrderItem

OrderItem
 ├── belongsTo → Order
 └── belongsTo → Product

DriverStock
 ├── belongsTo → User (driver)
 └── belongsTo → Product
```

---

## Filament-ресурсы (Админ-панель)

| Ресурс | Файл | Что делает |
|--------|------|-----------|
| ProductResource | `app/Filament/Resources/ProductResource.php` | CRUD товаров, загрузка изображений, фильтры, массовые действия |
| CategoryResource | `app/Filament/Resources/CategoryResource.php` | CRUD категорий с иерархией |
| DriverResource | `app/Filament/Resources/DriverResource.php` | CRUD водителей + управление стоком (RelationManager) |
| OrderResource | `app/Filament/Resources/OrderResource.php` | Просмотр заказов (read-only), смена статусов, фильтры |

### Виджеты дашборда (`app/Filament/Widgets/`)
- **StatsOverview** — ключевые метрики
- **SalesChart** — график продаж
- **TopProducts** — топ товаров
- **LatestOrders** — последние заказы

---

## Мультиязычность

Файлы переводов: `lang/{ru,en,uz}/shop.php`

Содержат ключи для:
- Навигации (categories, home, cart, search)
- Товаров (price, add_to_cart, in_stock, out_of_stock)
- Корзины (cart_empty, total, checkout)
- Оформления заказа (customer_name, phone, payment_method, place_order)
- Страницы подтверждения (order_success, order_number)
- Страницы логина (login, password, remember_me)

---

## Middleware

| Middleware | Файл | Назначение |
|-----------|------|-----------|
| EnsureIsDriver | `app/Http/Middleware/EnsureIsDriver.php` | Пропускает только роли `driver` и `admin` |
| SetLocale | `app/Http/Middleware/SetLocale.php` | Устанавливает язык приложения из сессии |
| SetAdminLocale | `app/Http/Middleware/SetAdminLocale.php` | Устанавливает язык для Filament |

---

## Команды для разработки

```bash
# Установка проекта
composer setup          # install deps + key:generate + migrate + npm build

# Запуск dev-сервера
composer dev            # Laravel server + queue + logs + Vite (одновременно)

# Тесты
composer test           # config:clear + phpunit

# Сборка фронтенда
npm run build           # Vite production build
npm run dev             # Vite dev server
```
