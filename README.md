# Checkout and Payment API - Laravel Assignment

A complete Checkout and Payment API system built with Laravel 12, featuring an Admin Dashboard to monitor and manage transactions.

## Features

### Checkout API
- ✅ Create orders with multiple items
- ✅ Calculate totals including tax and shipping charges
- ✅ Accept order details and payment method selection
- ✅ Save orders with initial status

### Payment API
- ✅ Cash on Delivery (COD) - Save order with unpaid status
- ✅ Online Payment (Mock/Sandbox) - Success/Completion and Failure handling
- ✅ Payment status tracking

### Admin Dashboard
- ✅ Dashboard with statistics (sales, orders, paid vs unpaid amounts)
- ✅ View and manage orders with filtering
- ✅ Detailed order view with items and payment details
- ✅ Charts for sales trends and order status distribution
- ✅ Order and payment status updates

### Authentication
- ✅ Laravel Sanctum API authentication
- ✅ Session-based admin panel login
- ✅ Role-based access control (Admin vs User)
- ✅ Secure API endpoints
- ✅ Dual authentication support (Bearer Token + Custom Header for IIS)
- ✅ Swagger API documentation

### Database
- ✅ Proper normalization with orders, order_items, and payments tables
- ✅ Seed data for testing
- ✅ Full relationships between models

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL/MariaDB database
- Node.js and npm (for frontend assets)

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd zodray-ass-project
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install NPM dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Edit `.env` file and set your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=zodray_ass_project
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run migrations and seed database**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Build frontend assets**
   ```bash
   npm run build
   ```

7. **Start the server**
   ```bash
   php artisan serve
   ```

   The application will be available at `http://localhost:8000`

## Usage

### Test Users

The seeder creates the following test users:

- **Admin User**
  - Email: `admin@example.com`
  - Password: `password`
  - Role: `admin`

- **Regular Users**
  - Email: `john@example.com` / `jane@example.com`
  - Password: `password`
  - Role: `user`

### API Endpoints

#### Authentication
```
POST   /api/register         - Register a new user
POST   /api/login            - Login and get auth token
POST   /api/logout           - Logout (requires auth)
GET    /api/me               - Get current user (requires auth)
```

#### Checkout
```
POST   /api/checkout         - Create a new order (requires auth)
```

**Checkout Request Example:**
```json
{
  "items": [
    {
      "product_name": "Laptop",
      "product_price": 999.99,
      "quantity": 1
    }
  ],
  "payment_method": "cod",
  "shipping_name": "John Doe",
  "shipping_address": "123 Main St",
  "shipping_phone": "+1234567890",
  "shipping_email": "john@example.com",
  "tax_percentage": 10,
  "shipping_charge": 50
}
```

#### Payment
```
POST   /api/payment/{orderId}        - Process payment (requires auth)
GET    /api/payment/{orderId}        - Get payment status (requires auth)
PUT    /api/payment/{orderId}        - Update payment status (requires auth)
```

### Admin Dashboard

**Login Required:**
Access the admin login at:
```
http://localhost:8000/admin/login
```

**Credentials:**
- Email: admin@example.com
- Password: password

**Features:**
- Session-based authentication
- Role-based access (only admin role)
- Dashboard with statistics and charts
- Order management and filtering
- Status updates

### API Documentation (Swagger)

Access interactive API documentation at:
```
http://localhost:8000/api/documentation
```

- View all endpoints
- Test API calls directly
- See request/response examples

## Database Schema

### Tables
- **users** - User accounts with roles
- **orders** - Order information with shipping details
- **order_items** - Individual items in each order
- **payments** - Payment transactions and status
- **personal_access_tokens** - API authentication tokens

### Relationships
- User has many Orders
- Order belongs to User
- Order has many OrderItems
- Order has one Payment
- OrderItem belongs to Order
- Payment belongs to Order

## API Testing

### Using Postman or cURL

1. **Register/Login**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password"}'
```

2. **Create Order** (using token from login)
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "items": [{"product_name":"Laptop","product_price":999.99,"quantity":1}],
    "payment_method":"cod",
    "shipping_name":"John Doe",
    "shipping_address":"123 Main St",
    "shipping_phone":"+1234567890",
    "shipping_email":"john@example.com"
  }'
```

## Project Structure

```
app/
├── Http/
│   └── Controllers/
│       ├── Api/
│       │   ├── CheckoutController.php
│       │   └── PaymentController.php
│       ├── Admin/
│       │   ├── DashboardController.php
│       │   └── OrderController.php
│       └── AuthController.php
├── Models/
│   ├── User.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── Payment.php
database/
├── migrations/
│   └── (all migrations)
├── seeders/
│   ├── DatabaseSeeder.php
│   └── OrderSeeder.php
resources/
└── views/
    └── admin/
        ├── dashboard.blade.php
        └── orders/
            ├── index.blade.php
            └── show.blade.php
routes/
├── web.php (admin routes)
└── api.php (API routes)
```

## Requirements Met

✅ Latest Laravel version (12.x)  
✅ REST API best practices  
✅ Secure APIs with authentication  
✅ Seed data for testing  
✅ Proper validation and error handling  
✅ Bootstrap/Tailwind for dashboard  
✅ Order filtering and management  
✅ Payment methods (COD + Online)  
✅ Dashboard statistics and charts  
✅ Role-based access control  

## Bonus Features

✅ Role-based access (Admin vs User)  
✅ Test seeders  
✅ Beautiful dashboard UI  
✅ Real-time statistics  
✅ Payment status tracking  

## Next Steps

1. ✅ Add authentication middleware to admin routes (DONE)
2. Implement actual payment gateway integration
3. Add email notifications
4. Add order tracking functionality
5. Implement CSV/PDF export (mentioned as bonus)
6. Add comprehensive test cases

## Additional Documentation

- **AUTHENTICATION_GUIDE.md** - Detailed authentication setup and dual auth solution
- **API_DOCUMENTATION.md** - Complete API reference
- **SETUP_INSTRUCTIONS.md** - Installation and troubleshooting guide
- **QUICK_TEST_GUIDE.md** - Quick testing instructions

## License

This project is part of an assignment for Zodray Technologies.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
