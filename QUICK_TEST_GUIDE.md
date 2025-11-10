# Quick Test Guide

## Test the Admin Dashboard

### 1. Access Dashboard
```
URL: http://localhost:8000/admin/dashboard
```

You should see:
- Statistics cards (Total Orders, Sales, etc.)
- Payment method breakdown
- Sales chart (last 7 days)
- Order status distribution
- Recent orders list

### 2. View All Orders
```
URL: http://localhost:8000/admin/orders
```

Features:
- Filter by status (pending/processing/completed/failed/cancelled)
- Filter by payment method (COD/online)
- Filter by payment status
- Paginated results

### 3. View Order Details
Click on any order number to see:
- Complete order information
- Order items table
- Financial breakdown
- Payment details
- Status update forms

### 4. Update Order Status
On order details page:
- Select new status from dropdown
- Click "Update" button
- Confirm changes
- Status updates immediately

### 5. Update Payment Status
On order details page:
- Select new payment status
- Click "Update" button
- Confirm changes
- Payment status updates

---

## Test the API

### Setup
Use any REST client (Postman, Insomnia, or cURL)

### Test User Credentials
```
Email: john@example.com
Password: password
```

### Test Flow

#### 1. Login
```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "john@example.com",
  "password": "password"
}
```

**Response:**
- Copy the `token` from response
- Use it as Bearer token in subsequent requests

#### 2. Create Order (COD)
```bash
POST http://localhost:8000/api/checkout
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "items": [
    {"product_name": "Laptop", "product_price": 999.99, "quantity": 1},
    {"product_name": "Mouse", "product_price": 29.99, "quantity": 2}
  ],
  "payment_method": "cod",
  "shipping_name": "John Doe",
  "shipping_address": "123 Main Street, City",
  "shipping_phone": "+1234567890",
  "shipping_email": "john@example.com"
}
```

**Expected:**
- Order created successfully
- Status: pending (for COD)
- Payment status: pending
- Order number generated

#### 3. Create Order (Online Payment)
```bash
POST http://localhost:8000/api/checkout
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "items": [
    {"product_name": "Monitor", "product_price": 249.99, "quantity": 1}
  ],
  "payment_method": "online",
  "shipping_name": "John Doe",
  "shipping_address": "123 Main Street, City",
  "shipping_phone": "+1234567890",
  "shipping_email": "john@example.com"
}
```

**Expected:**
- Order created successfully
- Status: processing (for online)
- Payment status: pending

#### 4. Process Payment
```bash
POST http://localhost:8000/api/payment/1
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "transaction_id": "TXN123456"
}
```

**Expected:**
- Payment processed successfully
- Status: completed
- Order status: completed
- Transaction ID saved

#### 5. Get Payment Status
```bash
GET http://localhost:8000/api/payment/1
Authorization: Bearer YOUR_TOKEN_HERE
```

**Expected:**
- Complete order details
- Payment information
- All items list

#### 6. Update Payment Status
```bash
PUT http://localhost:8000/api/payment/1
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "status": "completed"
}
```

**Expected:**
- Payment status updated
- paid_at timestamp set

#### 7. Get Current User
```bash
GET http://localhost:8000/api/me
Authorization: Bearer YOUR_TOKEN_HERE
```

**Expected:**
- User information returned

#### 8. Logout
```bash
POST http://localhost:8000/api/logout
Authorization: Bearer YOUR_TOKEN_HERE
```

**Expected:**
- Logged out successfully
- Token revoked

---

## Verification Checklist

### Dashboard
- [ ] Dashboard loads without errors
- [ ] Statistics display correctly
- [ ] Charts render properly
- [ ] Recent orders visible
- [ ] Can navigate to orders list
- [ ] Filtering works
- [ ] Can view order details
- [ ] Can update statuses

### API
- [ ] Can register a new user
- [ ] Can login and get token
- [ ] Can create COD order
- [ ] Can create online payment order
- [ ] Can process payment
- [ ] Can get payment status
- [ ] Can update payment status
- [ ] Can get current user info
- [ ] Can logout
- [ ] All responses formatted correctly
- [ ] Error handling works

---

## Common Test Scenarios

### Scenario 1: Complete COD Order Flow
1. User creates order with COD payment
2. Order status: pending
3. Admin views order in dashboard
4. Admin updates order to processing
5. Admin updates order to completed
6. Payment status remains pending until admin updates it

### Scenario 2: Complete Online Payment Flow
1. User creates order with online payment
2. Order status: processing
3. User processes payment via API
4. Payment status: completed
5. Order status: completed
6. Admin can view in dashboard

### Scenario 3: Failed Payment
1. User creates order with online payment
2. Admin can manually set payment to failed
3. Order can be cancelled or retried

### Scenario 4: Multiple Items Order
1. User creates order with multiple products
2. All items saved correctly
3. Totals calculated properly
4. Tax and shipping added
5. Final amount correct

---

## Error Testing

### Test Invalid Login
```bash
POST http://localhost:8000/api/login
Content-Type: application/json

{
  "email": "wrong@example.com",
  "password": "wrong"
}
```

**Expected:** 401 Unauthorized

### Test Invalid Order Creation (Missing Fields)
```bash
POST http://localhost:8000/api/checkout
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "items": []
}
```

**Expected:** 422 Validation Error

### Test Unauthorized Access
```bash
POST http://localhost:8000/api/checkout
Content-Type: application/json

{
  "items": [{"product_name": "Test", "product_price": 100, "quantity": 1}]
}
```

**Expected:** 401 Unauthorized (no token provided)

### Test Invalid Payment Method
```bash
POST http://localhost:8000/api/checkout
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "items": [{"product_name": "Test", "product_price": 100, "quantity": 1}],
  "payment_method": "invalid",
  "shipping_name": "Test",
  "shipping_address": "Test",
  "shipping_phone": "123",
  "shipping_email": "test@example.com"
}
```

**Expected:** 422 Validation Error

---

## Data Verification

### Check Database Directly

Login to phpMyAdmin or MySQL:
```sql
-- View all orders
SELECT * FROM orders;

-- View with payment info
SELECT o.*, p.status as payment_status, p.amount
FROM orders o
LEFT JOIN payments p ON o.id = p.order_id;

-- View order items
SELECT oi.*, o.order_number
FROM order_items oi
LEFT JOIN orders o ON oi.order_id = o.id;

-- View statistics
SELECT 
    COUNT(*) as total_orders,
    SUM(p.amount) as total_sales,
    COUNT(CASE WHEN p.status = 'completed' THEN 1 END) as paid_orders
FROM orders o
LEFT JOIN payments p ON o.id = p.order_id;
```

---

## Performance Testing

### Load Dashboard Multiple Times
- Refresh http://localhost:8000/admin/dashboard repeatedly
- Should load quickly each time
- Charts should render smoothly

### Create Multiple Orders
- Create 10+ orders via API
- Check pagination works
- Verify all appear in dashboard

### Test Filtering Performance
- Use various filter combinations
- Should filter quickly
- Results should be accurate

---

## Mobile Responsiveness

### Test on Different Screen Sizes
1. Open Chrome DevTools
2. Toggle device toolbar (Ctrl+Shift+M)
3. Test various sizes:
   - iPhone SE (375px)
   - iPad (768px)
   - Desktop (1920px)

**Expected:**
- Dashboard responsive
- Tables scroll horizontally if needed
- Charts resize appropriately
- Forms still usable

---

## Browser Compatibility

Test on:
- [ ] Chrome
- [ ] Firefox
- [ ] Safari (if on Mac)
- [ ] Edge

**Expected:** All functionality works across browsers

---

## Additional Tests

### Test Custom Tax Rate
```bash
POST http://localhost:8000/api/checkout
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "items": [{"product_name": "Test", "product_price": 100, "quantity": 1}],
  "payment_method": "cod",
  "tax_percentage": 15,
  "shipping_name": "Test",
  "shipping_address": "Test",
  "shipping_phone": "123",
  "shipping_email": "test@example.com"
}
```

**Expected:** Tax calculated at 15% instead of default 10%

### Test Custom Shipping Charge
```bash
POST http://localhost:8000/api/checkout
Authorization: Bearer YOUR_TOKEN_HERE
Content-Type: application/json

{
  "items": [{"product_name": "Test", "product_price": 100, "quantity": 1}],
  "payment_method": "cod",
  "shipping_charge": 75,
  "shipping_name": "Test",
  "shipping_address": "Test",
  "shipping_phone": "123",
  "shipping_email": "test@example.com"
}
```

**Expected:** Shipping charge is 75 instead of default 50

---

## Success Criteria

✅ Dashboard displays all seeded orders  
✅ Statistics match database counts  
✅ Charts render with data  
✅ Filters work correctly  
✅ Can update order/payment statuses  
✅ API authentication works  
✅ All API endpoints respond correctly  
✅ Validation errors display properly  
✅ Error handling works  
✅ Mobile responsive  
✅ Cross-browser compatible  

---

## Report Issues

If you find any issues during testing:
1. Note the exact steps to reproduce
2. Check browser console for errors
3. Check `storage/logs/laravel.log`
4. Note the expected vs actual behavior

---

Happy Testing! 🧪
