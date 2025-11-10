# API Documentation

## Base URL
```
http://localhost:8000/api
```

## Authentication

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer YOUR_TOKEN_HERE
```

---

## Endpoints

### 1. Register User

**Endpoint:** `POST /api/register`

**Description:** Register a new user

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "user"  // optional: "user" or "admin"
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user",
      "created_at": "2025-11-03T13:00:00.000000Z"
    },
    "token": "1|abcdef123456..."
  }
}
```

---

### 2. Login

**Endpoint:** `POST /api/login`

**Description:** Authenticate user and get access token

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "user"
    },
    "token": "1|abcdef123456..."
  }
}
```

**Error Response (401):**
```json
{
  "success": false,
  "message": "Invalid login credentials"
}
```

---

### 3. Logout

**Endpoint:** `POST /api/logout`

**Description:** Logout and revoke current token (Protected)

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response (200):**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

---

### 4. Get Current User

**Endpoint:** `GET /api/me`

**Description:** Get authenticated user details (Protected)

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user"
  }
}
```

---

### 5. Create Order (Checkout)

**Endpoint:** `POST /api/checkout`

**Description:** Create a new order (Protected)

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json
```

**Request Body:**
```json
{
  "items": [
    {
      "product_name": "Laptop",
      "product_price": 999.99,
      "quantity": 1
    },
    {
      "product_name": "Mouse",
      "product_price": 29.99,
      "quantity": 2
    }
  ],
  "payment_method": "cod",  // "cod" or "online"
  "shipping_name": "John Doe",
  "shipping_address": "123 Main St, City, Country",
  "shipping_phone": "+1234567890",
  "shipping_email": "john@example.com",
  "tax_percentage": 10,  // optional, default: 10
  "shipping_charge": 50  // optional, default: 50
}
```

**Response (201):**
```json
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 1,
    "order_number": "ORD-123ABC",
    "user_id": 1,
    "subtotal": 1059.97,
    "tax": 105.99,
    "shipping_charge": 50.00,
    "total_amount": 1215.96,
    "status": "pending",
    "payment_method": "cod",
    "created_at": "2025-11-03T13:00:00.000000Z",
    "items": [
      {
        "id": 1,
        "product_name": "Laptop",
        "product_price": "999.99",
        "quantity": 1,
        "subtotal": "999.99"
      },
      {
        "id": 2,
        "product_name": "Mouse",
        "product_price": "29.99",
        "quantity": 2,
        "subtotal": "59.98"
      }
    ],
    "payment": {
      "id": 1,
      "order_id": 1,
      "amount": "1215.96",
      "payment_method": "cod",
      "status": "pending"
    }
  }
}
```

**Error Response (422):**
```json
{
  "success": false,
  "errors": {
    "items": ["The items field is required."],
    "shipping_email": ["The shipping email must be a valid email address."]
  }
}
```

---

### 6. Process Payment

**Endpoint:** `POST /api/payment/{orderId}`

**Description:** Process payment for an order (Protected)

**Parameters:**
- `orderId` - The ID of the order

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json
```

**Request Body:**
```json
{
  "transaction_id": "TXN123456",  // optional for online payments
  "payment_details": {  // optional
    "gateway": "stripe",
    "card_last4": "4242"
  }
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Payment processed successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-123ABC",
      "status": "completed",
      "total_amount": "1215.96"
    },
    "payment_status": "completed"
  }
}
```

---

### 7. Get Payment Status

**Endpoint:** `GET /api/payment/{orderId}`

**Description:** Get payment status of an order (Protected)

**Parameters:**
- `orderId` - The ID of the order

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "order_number": "ORD-123ABC",
    "user_id": 1,
    "total_amount": "1215.96",
    "status": "completed",
    "payment": {
      "id": 1,
      "status": "completed",
      "amount": "1215.96",
      "payment_method": "cod",
      "transaction_id": "TXN123456",
      "paid_at": "2025-11-03T13:30:00.000000Z"
    },
    "items": [...]
  }
}
```

---

### 8. Update Payment Status

**Endpoint:** `PUT /api/payment/{orderId}`

**Description:** Manually update payment status (Protected)

**Parameters:**
- `orderId` - The ID of the order

**Headers:**
```
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json
```

**Request Body:**
```json
{
  "status": "completed"  // "pending", "completed", "failed", "refunded"
}
```

**Response (200):**
```json
{
  "success": true,
  "message": "Payment status updated",
  "data": {
    "id": 1,
    "status": "completed",
    "paid_at": "2025-11-03T13:30:00.000000Z"
  }
}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 422 Validation Error
```json
{
  "success": false,
  "errors": {
    "field_name": ["Error message here"]
  }
}
```

### 404 Not Found
```json
{
  "message": "No query results for model..."
}
```

### 500 Server Error
```json
{
  "success": false,
  "message": "Error message",
  "error": "Detailed error"
}
```

---

## Testing the API

### Using cURL

1. **Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password"}'
```

2. **Create Order:**
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "items": [{"product_name":"Laptop","product_price":999.99,"quantity":1}],
    "payment_method":"cod",
    "shipping_name":"John Doe",
    "shipping_address":"123 Main St",
    "shipping_phone":"+1234567890",
    "shipping_email":"john@example.com"
  }'
```

3. **Process Payment:**
```bash
curl -X POST http://localhost:8000/api/payment/1 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"transaction_id":"TXN123456"}'
```

### Using Postman

1. Import the collection
2. Set environment variables:
   - `base_url`: http://localhost:8000
   - `token`: (will be set automatically after login)
3. Run the requests in sequence

---

## Test Credentials

- **Admin:** admin@example.com / password
- **User:** john@example.com / password
- **User:** jane@example.com / password
