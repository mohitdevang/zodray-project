# Setup Instructions - Checkout and Payment API

## Quick Setup Guide

### Prerequisites Checklist
- [ ] PHP 8.2 or higher installed
- [ ] Composer installed
- [ ] MySQL/MariaDB installed
- [ ] Node.js and npm installed
- [ ] Laragon (or similar PHP development environment)

---

## Step-by-Step Installation

### Step 1: Verify Prerequisites

**Check PHP Version:**
```bash
php -v
```
Should be PHP 8.2 or higher.

**Check Composer:**
```bash
composer --version
```

**Check Node.js:**
```bash
node -v
npm -v
```

---

### Step 2: Clone/Download Project

If using git:
```bash
git clone <repository-url>
cd zodray-ass-project
```

Or extract the project ZIP file to your web directory.

---

### Step 3: Install PHP Dependencies

```bash
composer install
```

This will install all required Laravel packages and dependencies.

---

### Step 4: Install NPM Dependencies

```bash
npm install
```

This will install Tailwind CSS and frontend dependencies.

---

### Step 5: Environment Configuration

**Copy environment file:**
```bash
# On Linux/Mac
cp .env.example .env

# On Windows (if .env.example exists)
copy .env.example .env
```

**Generate application key:**
```bash
php artisan key:generate
```

**Edit `.env` file:**
Open `.env` in your text editor and configure database settings:

```env
APP_NAME="Checkout API"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=zodray_ass_project
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

**Note:** Adjust database settings according to your local MySQL configuration.

---

### Step 6: Create Database

**Option A: Using MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE zodray_ass_project CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

**Option B: Using phpMyAdmin**
1. Open http://localhost/phpmyadmin
2. Click "New" to create a database
3. Name it `zodray_ass_project`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

---

### Step 7: Run Migrations

This creates all necessary database tables:
```bash
php artisan migrate
```

---

### Step 8: Seed Test Data

This creates test users, orders, and payments:
```bash
php artisan db:seed
```

**Created Test Users:**
- **Admin:** admin@example.com / password
- **User 1:** john@example.com / password
- **User 2:** jane@example.com / password

---

### Step 9: Build Frontend Assets

```bash
npm run build
```

This compiles Tailwind CSS for production.

**For development** (auto-reload on changes):
```bash
npm run dev
```
(Run in a separate terminal and keep it running)

---

### Step 10: Start Development Server

```bash
php artisan serve
```

Server will start at: **http://localhost:8000**

---

## Verification

### Test Admin Dashboard

1. Open browser: `http://localhost:8000/admin/dashboard`
2. You should see the dashboard with statistics
3. Click on "Orders" to view order list

### Test API

Using Postman or cURL:

**1. Login:**
```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password"
}
```

**2. Create Order** (use token from login):
```bash
POST http://localhost:8000/api/checkout
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "items": [
    {"product_name": "Laptop", "product_price": 999.99, "quantity": 1}
  ],
  "payment_method": "cod",
  "shipping_name": "John Doe",
  "shipping_address": "123 Main St",
  "shipping_phone": "+1234567890",
  "shipping_email": "john@example.com"
}
```

---

## Common Issues & Solutions

### Issue: "could not find driver" (SQLite error)

**Solution:** You need to use MySQL, not SQLite. Ensure your `.env` has:
```env
DB_CONNECTION=mysql
```

### Issue: "Access denied for user"

**Solution:** Check your MySQL credentials in `.env`:
- Verify DB_USERNAME and DB_PASSWORD
- Ensure database exists
- Check MySQL is running

### Issue: "npm: command not found"

**Solution:** Install Node.js from https://nodejs.org

### Issue: "composer: command not found"

**Solution:** Install Composer from https://getcomposer.org

### Issue: Port 8000 already in use

**Solution:** Use a different port:
```bash
php artisan serve --port=8001
```

### Issue: NPM build fails

**Solution:** Clear cache and reinstall:
```bash
rm -rf node_modules package-lock.json
npm install
npm run build
```

---

## Production Deployment

### 1. Update .env for production

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 2. Optimize

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### 3. Set File Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Configure Web Server

Point your web server document root to the `public` directory.

---

## Database Schema Overview

### Tables Created

1. **users** - User accounts with authentication
2. **orders** - Order information
3. **order_items** - Items in each order
4. **payments** - Payment transactions
5. **personal_access_tokens** - API tokens
6. **cache** - Session and cache storage
7. **jobs** - Queue jobs

### Sample Data

After seeding, you'll have:
- 3 users (1 admin, 2 customers)
- 8 orders total
- Various payment methods (COD and Online)
- Different order statuses for testing

---

## Development Workflow

### Making Changes

1. **Backend (PHP):**
   - Edit files in `app/` directory
   - Run `php artisan serve`
   - Changes reflect immediately

2. **Frontend (CSS/JS):**
   - Edit `resources/css/app.css` or `resources/js/app.js`
   - Run `npm run dev` in separate terminal
   - Or run `npm run build` after changes

### Database Changes

**Create a migration:**
```bash
php artisan make:migration add_new_field_to_orders
```

**Run migrations:**
```bash
php artisan migrate
```

**Rollback last migration:**
```bash
php artisan migrate:rollback
```

---

## Additional Resources

- **Laravel Docs:** https://laravel.com/docs
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Laravel Sanctum:** https://laravel.com/docs/sanctum
- **Chart.js:** https://www.chartjs.org/docs

---

## Support

For issues or questions:
- Check `README.md` for feature overview
- Check `API_DOCUMENTATION.md` for API details
- Review error logs in `storage/logs/laravel.log`

---

## Next Steps After Setup

1. ✅ Visit admin dashboard
2. ✅ Test API endpoints with Postman
3. ✅ Create a new order via API
4. ✅ View orders in admin panel
5. ✅ Update order status
6. ✅ Check statistics and charts

Enjoy exploring the Checkout and Payment API! 🎉
