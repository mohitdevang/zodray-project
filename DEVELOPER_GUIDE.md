# Developer Guide

## Tech Stack
- Laravel 12.x (PHP 8.2+)
- MySQL 8.x
- Sanctum (API tokens)
- Blade + Tailwind CSS
- L5-Swagger (API docs)

## Project Structure (key paths)
- Routes: `routes/api.php` (API), `routes/web.php` (web/admin)
- Middleware: `app/Http/Middleware/*` (e.g., `CheckSanctumToken.php`, `EnsureUserIsAdmin.php`)
- Controllers (API): `app/Http/Controllers/Api/*`
- Controllers (Admin): `app/Http/Controllers/Admin/*`
- Models: `app/Models/*`
- Views (Admin): `resources/views/admin/*`
- Swagger config: `config/l5-swagger.php`

## Authentication Model
- API: Sanctum bearer tokens; in production (SmarterASP) tokens must be sent via `X-Api-Token` (the app ignores `Authorization` to avoid BasicAuth conflicts). Fallbacks: `X-Access-Token`, `X-Authorization`, `Api-Token`, or query `?api_token=`.
- Admin: Session auth + role check (`admin` middleware). Admin panel is separate from API tokens.

## Database
### Migrations (purpose)
- `0001_01_01_000000_create_users_table.php`: users with `role` field (admin/user).
- `2025_11_03_131125_create_orders_table.php`: orders (user_id, totals, status, payment_method, order_number).
- `2025_11_03_131127_create_order_items_table.php`: items per order (denormalized name/price, quantity, subtotal).
- `2025_11_03_131131_create_payments_table.php`: payments (order_id, amount, method, status, paid_at, details).
- `2025_11_04_000000_create_items_table.php`: catalog items (name, price, is_active, description).
- `2025_11_04_000001_add_item_id_to_order_items_table.php`: adds `item_id` FK to `order_items`.

### Seeders
- `DatabaseSeeder.php`: calls `ItemSeeder` then `OrderSeeder`.
- `ItemSeeder.php`: seeds sample catalog items.
- `OrderSeeder.php`: sample users/orders (may conflict if re-run).

## Setup (Local)
1) Clone repo and install dependencies:
```
composer install
cp .env.example .env
php artisan key:generate
```
2) Configure `.env` (DB connection):
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zodray
DB_USERNAME=root
DB_PASSWORD=
```
3) Run migrations and seeders:
```
php artisan migrate
php artisan db:seed --class=Database\\Seeders\\ItemSeeder
```
4) Serve:
```
php artisan serve
```

## Setup (SmarterASP Hosting)
- Server Location: United States (Las Vegas)
- Hosting Account ID: `mdmohitdevang-001`
- First Website Root Folder: `zodray`

### Server Access
- Server access link: `www.smarterasp.net`
- Member Login Name: `mdmohitdevang`
- Email: `logises183@fantastu.com`
- Password: `Maapaa@143`
- Hosting Account Password: `Maapaa@1432`

### Database (MySQL 8.x)
- MySQL Server: `mysql8001.site4now.net`
- DB Name: `db_ac045a_zodray`
- MySQL Username: `ac045a_zodray`
- DB Password: `root@143`

### Server Basic Auth Credentials
- Username: `11273306`
- Password: `60-dayfreetrial`


Update `.env` on server accordingly:
```
DB_CONNECTION=mysql
DB_HOST=mysql8001.site4now.net
DB_PORT=3306
DB_DATABASE=db_ac045a_zodray
DB_USERNAME=ac045a_zodray
DB_PASSWORD=root@143
```

Run in production (panel or SSH):
```
php artisan migrate --force
php artisan db:seed --class=Database\\Seeders\\ItemSeeder --force
```

## Running & Building Frontend
- Blade + Tailwind compiled via Vite. For dev:
```
npm install
npm run dev
```
For production build:
```
npm run build
```

## API Routing Overview
- Public: `/api/register`, `/api/login`, `/api/items`
- Protected (Sanctum): `/api/me`, `/api/checkout`, `/api/payment/*`
- Middleware stack for API: `force.json`, then `sanctum.token`, then `auth:sanctum` for protected routes.

## Key Middleware
- `CheckSanctumToken.php`:
  - Production: strip `Authorization` (Basic/Bearer) and accept only `X-Api-Token` (fallback headers or `?api_token=`). Converts to `Authorization: Bearer` for Sanctum.
- `EnsureUserIsAdmin.php`: asserts `role=admin` for admin routes.
- `ForceJsonResponse.php`: forces JSON responses for API.

## Admin Panel
- Login: `/admin/login`
- Routes: `routes/web.php`
- Controllers: `app/Http/Controllers/Admin/*`
- Views: `resources/views/admin/*`
- Features:
  - Dashboard with stats and charts
  - Orders list with filters (status, payment_method, payment_status, date range)
  - CSV export: `GET /admin/orders-export` (respects current filters)
  - Order detail with status/payment status updates

## Developer Notes
- Checkout uses `items[].item_id` with `quantity`. Item name/price is snapshotted in `order_items` for historical correctness.
- Payment flow:
  - COD: payment `pending` until explicitly completed.
  - Online: mock processing sets `completed` by default (configurable in controller).
- Swagger docs: visit `/api/documentation` and use Bearer auth.


