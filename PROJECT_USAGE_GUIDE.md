# Project Usage Guide

## Admin Panel
- URL: `http://mdmohitdevang-001-site1.ltempurl.com/admin/login`
- Host Basic Auth (required by SmarterASP for any request):
  - User Name: `11273306`
  - Password: `60-dayfreetrial`
- Admin Login: 
  Email: admin@example.com
  Password: password

Use your admin email/password (seeded or created). If not available, create via API then set role to `admin` in DB.

## API Base
- Base URL: `http://mdmohitdevang-001-site1.ltempurl.com/api`
- Host Basic Auth: always include
  - `Authorization: Basic 11273306:60-dayfreetrial` (use Base64 as usual; Postman can set this automatically)
- Bearer Token: send via `X-Api-Token: <token>` (production requirement). Fallbacks: `X-Access-Token`, `X-Authorization`, `Api-Token`, or `?api_token=` query.

## Auth Flow
1) Register (public)
```
POST /api/register
Headers: Authorization: Basic ...
Body (JSON): {
  "name": "John",
  "email": "john@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```
Response -> `data.token`

2) Login (public)
```
POST /api/login
Headers: Authorization: Basic ...
Body: { "email": "john@example.com", "password": "password" }
```
Response -> `data.token`

3) Me (protected)
```
GET /api/me
Headers:
  Authorization: Basic ...
  X-Api-Token: <TOKEN>
```

## Items
- List Items (public)
```
GET /api/items?search=lap
```
Response shape:
```
{
  "status": true,
  "count": 4,
  "total_count": 4,
  "limit": 20,
  "offset": 0,
  "data": [{ "id": 1, "name": "Laptop", "price": "999.99", ... }]
}
```

## Checkout
- Create Order (protected) using item IDs
```
POST /api/checkout
Headers: Authorization: Basic ..., X-Api-Token: <TOKEN>
Body:
{
  "items": [
    { "item_id": 1, "quantity": 2 },
    { "item_id": 3, "quantity": 1 }
  ],
  "payment_method": "cod",  // or "online"
  "shipping_name": "John Doe",
  "shipping_address": "123 Main",
  "shipping_phone": "+123456789",
  "shipping_email": "john@example.com"
}
```
- Notes:
  - `cod` creates payment as `pending`.
  - `online` sets order status to `processing` until payment is processed.

## Payment
- Process Online Payment (mock)
```
POST /api/payment/{orderId}
Headers: Authorization: Basic ..., X-Api-Token: <TOKEN>
Body (optional): { "transaction_id": "TXN123", "payment_details": { "gateway": "mock" } }
```
- Update Payment Status (any method)
```
PUT /api/payment/{orderId}
Headers: Authorization: Basic ..., X-Api-Token: <TOKEN>
Body: { "status": "completed" }  // or pending, failed, refunded
```
- Get Payment Status
```
GET /api/payment/{orderId}
Headers: Authorization: Basic ..., X-Api-Token: <TOKEN>
```

## Admin Orders
- Filters on Orders list: status, payment_method, payment_status, date range
- CSV Export: Button on Orders page; URL `GET /admin/orders-export` (respects current filters)

## Creating Admin/User
- Create user via API `POST /api/register`, then in DB set `users.role = 'admin'` for admin access.
- Alternatively, log in via API and use the admin panel if already admin.

## Server/DB Credentials (for reference)
- Member Login: `mdmohitdevang` / `Maapaa@143`
- Hosting Account Password: `Maapaa@1432`
- MySQL Server: `mysql8001.site4now.net`
- DB Name: `db_ac045a_zodray`
- DB User: `11273306`
- DB Password: `60-dayfreetrial`

## Notes
- On SmarterASP, always include Basic Auth AND send your API token via `X-Api-Token`. The app ignores any Bearer token in `Authorization` on production to avoid conflicts with host Basic Auth.
- Swagger docs at `/api/documentation` (use Bearer from `X-Api-Token` value in the UI).
