# TaxiShop Filament Admin Panel — Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Build a complete Filament 5 admin panel for TaxiShop — managing categories, products, drivers, stock, and orders via `/admin`.

**Architecture:** Filament 5 resources with separate form/table classes. Custom login page using `login` field instead of `email`. Dashboard with stats, charts, and tables. All labels in Russian.

**Tech Stack:** Laravel 12, PHP 8.2, PostgreSQL 16, Filament 5, Livewire 4

---

## Task 1: Migrations — Modify users table + create all new tables

**Files:**
- Create: `database/migrations/2026_03_09_000001_modify_users_table_for_taxishop.php`
- Create: `database/migrations/2026_03_09_000002_create_categories_table.php`
- Create: `database/migrations/2026_03_09_000003_create_products_table.php`
- Create: `database/migrations/2026_03_09_000004_create_product_images_table.php`
- Create: `database/migrations/2026_03_09_000005_create_driver_stock_table.php`
- Create: `database/migrations/2026_03_09_000006_create_orders_table.php`
- Create: `database/migrations/2026_03_09_000007_create_order_items_table.php`

### Step 1: Create users modification migration

Rename `email` → `login` (varchar 100), drop `email_verified_at`, add `phone`, `role` (enum), `car_number`, `is_active`. Also update `password_reset_tokens` to use `login` instead of `email`.

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('email_verified_at');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('email', 'login');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('login', 100)->change();
            $table->string('phone', 20)->nullable()->after('password');
            $table->enum('role', ['admin', 'driver'])->default('admin')->after('phone');
            $table->string('car_number', 20)->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('car_number');
        });

        // Update password_reset_tokens
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->renameColumn('email', 'login');
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->renameColumn('login', 'email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'car_number', 'is_active']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('login', 'email');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->change();
            $table->timestamp('email_verified_at')->nullable()->after('email');
        });
    }
};
```

### Step 2: Create categories migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('icon')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('parent_id');
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
```

### Step 3: Create products migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->string('main_image', 500);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deliverable')->default(true);
            $table->timestamps();

            $table->index('category_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

### Step 4: Create product_images migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('image_path', 500);
            $table->integer('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
```

### Step 5: Create driver_stock migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->unique(['driver_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_stock');
    }
};
```

### Step 6: Create orders migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->unique();
            $table->foreignId('driver_id')->constrained('users')->restrictOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone', 20);
            $table->text('delivery_address')->nullable();
            $table->enum('payment_method', ['cash', 'qr', 'delivery']);
            $table->enum('status', ['new', 'paid', 'delivered', 'cancelled'])->default('new');
            $table->decimal('total', 12, 2);
            $table->timestamps();

            $table->index('driver_id');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
```

### Step 7: Create order_items migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('subtotal', 10, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
```

### Step 8: Run migrations

Run: `php artisan migrate`
Expected: All 7 migrations run successfully.

### Step 9: Commit

```bash
git add database/migrations/
git commit -m "feat: add TaxiShop database schema migrations"
```

---

## Task 2: Models — All 7 models with relationships

**Files:**
- Modify: `app/Models/User.php`
- Create: `app/Models/Category.php`
- Create: `app/Models/Product.php`
- Create: `app/Models/ProductImage.php`
- Create: `app/Models/DriverStock.php`
- Create: `app/Models/Order.php`
- Create: `app/Models/OrderItem.php`

### Step 1: Update User model

Key changes: `login` instead of `email`, add relationships (driverStock, orders), `canAccessPanel` checks role=admin, fillable includes new fields.

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'login',
        'password',
        'phone',
        'role',
        'car_number',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin' && $this->is_active;
    }

    public function driverStock(): HasMany
    {
        return $this->hasMany(DriverStock::class, 'driver_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'driver_id');
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }
}
```

### Step 2: Create Category model

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'icon',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
```

### Step 3: Create Product model

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'old_price',
        'main_image',
        'is_active',
        'is_deliverable',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'old_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_deliverable' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function driverStock(): HasMany
    {
        return $this->hasMany(DriverStock::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

### Step 4: Create ProductImage model

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

### Step 5: Create DriverStock model

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DriverStock extends Model
{
    public $timestamps = false;

    protected $table = 'driver_stock';

    protected $fillable = [
        'driver_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

### Step 6: Create Order model

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'driver_id',
        'customer_name',
        'customer_phone',
        'delivery_address',
        'payment_method',
        'status',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $last = Order::max('id') ?? 0;
                $order->order_number = 'TS-' . str_pad((string) ($last + 1), 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
```

### Step 7: Create OrderItem model

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
```

### Step 8: Commit

```bash
git add app/Models/
git commit -m "feat: add TaxiShop models with relationships"
```

---

## Task 3: Filament Auth — Custom Login page + AdminPanelProvider

**Files:**
- Create: `app/Filament/Pages/Auth/Login.php`
- Modify: `app/Providers/Filament/AdminPanelProvider.php`

### Step 1: Create custom Login page

Override Filament's default login to use `login` field instead of `email`. Must override `getCredentialsFromFormData()` and the form schema.

```php
<?php

declare(strict_types=1);

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Логин')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'login' => $data['login'],
            'password' => $data['password'],
        ];
    }
}
```

### Step 2: Update AdminPanelProvider

Green color theme, Russian locale, custom login, SPA mode, branding.

```php
// Key changes to panel():
->login(\App\Filament\Pages\Auth\Login::class)
->brandName('TaxiShop Admin')
->colors(['primary' => Color::Green])
->spa()
->unsavedChangesAlerts()
->databaseTransactions()
->sidebarCollapsibleOnDesktop()
```

### Step 3: Commit

```bash
git add app/Filament/Pages/Auth/ app/Providers/Filament/
git commit -m "feat: custom login page with login field + panel config"
```

---

## Task 4: CategoryResource

**Files:**
- Create: `app/Filament/Resources/CategoryResource.php`
- Create: `app/Filament/Resources/CategoryResource/Pages/ListCategories.php`
- Create: `app/Filament/Resources/CategoryResource/Pages/CreateCategory.php`
- Create: `app/Filament/Resources/CategoryResource/Pages/EditCategory.php`

Features: auto-slug from name, parent select (self-referencing), icon upload, sort_order, product count column, filters (root only, by parent). Delete protection when category has products.

---

## Task 5: ProductResource

**Files:**
- Create: `app/Filament/Resources/ProductResource.php`
- Create: `app/Filament/Resources/ProductResource/Pages/ListProducts.php`
- Create: `app/Filament/Resources/ProductResource/Pages/CreateProduct.php`
- Create: `app/Filament/Resources/ProductResource/Pages/EditProduct.php`

Features: 4 sections (Основное, Цены, Медиа, Настройки), auto-slug, category select with search, RichEditor for description, FileUpload for main_image, Repeater for additional images, ToggleColumns for is_active, filters by category/status/discount, bulk actions activate/deactivate.

---

## Task 6: DriverResource

**Files:**
- Create: `app/Filament/Resources/DriverResource.php`
- Create: `app/Filament/Resources/DriverResource/Pages/ListDrivers.php`
- Create: `app/Filament/Resources/DriverResource/Pages/CreateDriver.php`
- Create: `app/Filament/Resources/DriverResource/Pages/EditDriver.php`
- Create: `app/Filament/Resources/DriverResource/RelationManagers/StockRelationManager.php`

Features: Scoped to role=driver, password hashing (visible only on create), stock count, ToggleColumn for is_active. StockRelationManager: product select with search (exclude already added), quantity input, edit/delete.

---

## Task 7: OrderResource

**Files:**
- Create: `app/Filament/Resources/OrderResource.php`
- Create: `app/Filament/Resources/OrderResource/Pages/ListOrders.php`
- Create: `app/Filament/Resources/OrderResource/Pages/ViewOrder.php`

Features: View-only (no create/edit forms), status badges with colors, payment method badges, order items as repeater/table (read-only), status change action, filters by status/payment/driver/date, bulk status change.

---

## Task 8: Dashboard Widgets

**Files:**
- Create: `app/Filament/Widgets/StatsOverview.php`
- Create: `app/Filament/Widgets/LatestOrders.php`
- Create: `app/Filament/Widgets/SalesChart.php`
- Create: `app/Filament/Widgets/TopProducts.php`

StatsOverview: orders today, revenue today, active products, active drivers.
LatestOrders: last 10 orders table.
SalesChart: line chart of daily revenue for last 7 days.
TopProducts: top 5 products by order count.

---

## Task 9: DatabaseSeeder

**Files:**
- Modify: `database/seeders/DatabaseSeeder.php`

Seed: 1 admin (login: admin, password: password), 3 drivers, 5 categories (with nesting), 15 products with images, some driver_stock entries, 10 orders with items.

---

## Task 10: Final verification

- Run `php artisan migrate:fresh --seed`
- Verify login at `/admin` with admin/password
- Check all resources load correctly
- Verify dashboard widgets render
- Commit all changes

```bash
git add -A
git commit -m "feat: complete TaxiShop Filament admin panel"
```
