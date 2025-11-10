# Authentication Guide - Dual Authentication Support

## Problem: IIS Basic Auth Conflict

When hosting on **Windows IIS with Basic Auth**, you encounter a conflict because both Basic Auth and Bearer Token use the same `Authorization` header.

### The Conflict

```
Windows IIS Basic Auth    → Uses "Authorization: Basic xxxxxx"
Laravel Sanctum Bearer     → Uses "Authorization: Bearer xxxxxx"
                                          ↓
                                    CONFLICT!
```

## Solution: Custom Header Support

We've implemented a solution that allows both authentication methods to coexist.

---

## Authentication Methods

### 1. Admin Panel Login (Session-Based)

**For:** Admin dashboard access  
**Method:** Traditional login form with session cookies  

**Access:**
```
URL: http://localhost:8000/admin/login
```

**Login as Admin:**
- Email: `admin@example.com`
- Password: `password`
- Role: Must be `admin` in database

**Features:**
- ✅ Session-based authentication
- ✅ Role-based access control
- ✅ Auto-redirect to login if not authenticated
- ✅ Users with role="user" cannot access admin panel
- ✅ Admins cannot use normal user login flow

---

### 2. API Authentication (Token-Based)

**For:** Mobile apps, frontend apps, third-party integrations  
**Method:** Bearer token authentication  

#### Standard Bearer Token (Normal Servers)

**Header:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

**Example cURL:**
```bash
curl -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"items": [{"product_name":"Laptop","product_price":999.99,"quantity":1}], ...}'
```

#### Custom Header (IIS Basic Auth Servers)

**Header:**
```
X-Api-Token: YOUR_TOKEN_HERE
```

**Example cURL:**
```bash
curl -X POST http://yourserver.com/api/checkout \
  -H "Authorization: Basic IIS_BASIC_AUTH_TOKEN" \
  -H "X-Api-Token: YOUR_LARAVEL_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{"items": [{"product_name":"Laptop","product_price":999.99,"quantity":1}], ...}'
```

**How it Works:**
1. IIS processes `Authorization: Basic xxxxxx` for Basic Auth
2. Laravel checks `X-Api-Token` header for API authentication
3. Middleware automatically converts `X-Api-Token` to `Authorization: Bearer`
4. Both authentication layers work independently!

---

## Test Authentication Flow

### Admin Login Flow

1. **Access Admin Login:**
   ```
   http://localhost:8000/admin/login
   ```

2. **Enter Credentials:**
   - Email: `admin@example.com`
   - Password: `password`

3. **Successful Login:**
   - Redirects to `/admin/dashboard`
   - Session cookie set
   - Can access all admin routes

4. **Logout:**
   - Click "Logout" button
   - Session destroyed
   - Redirects to `/admin/login`

### API Authentication Flow

1. **Register/Login:**
   ```bash
   POST /api/login
   Body: {"email":"john@example.com","password":"password"}
   ```

2. **Get Token:**
   ```json
   {
     "success": true,
     "data": {
       "user": {...},
       "token": "1|abc123def456..."
     }
   }
   ```

3. **Use Token:**
   - Copy the token from response
   - Add to request headers

4. **Make API Calls:**
   - Use `Authorization: Bearer TOKEN` for normal servers
   - Use `X-Api-Token: TOKEN` for IIS servers

---

## Configuration

### Middleware Setup

Located in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        'sanctum.token' => \App\Http\Middleware\CheckSanctumToken::class,
    ]);
})
```

### Route Protection

**Admin Routes** (in `routes/web.php`):
```php
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes here
});
```

**API Routes** (in `routes/api.php`):
```php
Route::middleware(['sanctum.token', 'auth:sanctum'])->group(function () {
    // Protected API routes here
});
```

---

## Testing Dual Authentication

### Test on Local Server (Normal)

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password"}'

# Use token
curl -X POST http://localhost:8000/api/checkout \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{...}'
```

### Test on IIS Server (Basic Auth Required)

```bash
# Login
curl -X POST http://yourserver.com/api/login \
  -H "Authorization: Basic BASE64(username:password)" \
  -H "Content-Type: application/json" \
  -d '{"email":"john@example.com","password":"password"}'

# Use token with custom header
curl -X POST http://yourserver.com/api/checkout \
  -H "Authorization: Basic BASE64(username:password)" \
  -H "X-Api-Token: YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{...}'
```

---

## Code Examples

### JavaScript/Fetch (Normal)

```javascript
// Login
const response = await fetch('http://localhost:8000/api/login', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: 'john@example.com', password: 'password' })
});

const { data } = await response.json();
const token = data.token;

// Make API call
const orderResponse = await fetch('http://localhost:8000/api/checkout', {
    method: 'POST',
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({...})
});
```

### JavaScript/Fetch (IIS Basic Auth)

```javascript
// Login
const response = await fetch('http://yourserver.com/api/login', {
    method: 'POST',
    headers: {
        'Authorization': 'Basic ' + btoa('iis_user:iis_pass'),
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({ email: 'john@example.com', password: 'password' })
});

const { data } = await response.json();
const token = data.token;

// Make API call
const orderResponse = await fetch('http://yourserver.com/api/checkout', {
    method: 'POST',
    headers: {
        'Authorization': 'Basic ' + btoa('iis_user:iis_pass'),
        'X-Api-Token': token,  // Use custom header!
        'Content-Type': 'application/json'
    },
    body: JSON.stringify({...})
});
```

### Postman Configuration

**For Normal Servers:**
1. Set `Authorization` → Type: Bearer Token
2. Paste your token

**For IIS Servers:**
1. Set `Authorization` → Type: Basic Auth
   - Username: IIS username
   - Password: IIS password
2. Add Custom Header:
   - Key: `X-Api-Token`
   - Value: Your Laravel token

---

## Security Considerations

### Admin Panel

- ✅ Session-based authentication
- ✅ CSRF protection on forms
- ✅ Only admin role can access
- ✅ Auto-logout on password change
- ✅ Secure cookie settings

### API

- ✅ Token-based authentication
- ✅ Tokens can be revoked
- ✅ Custom header for IIS compatibility
- ✅ No password exposure in headers
- ✅ Rate limiting recommended for production

---

## Troubleshooting

### Issue: "Unauthenticated" on Admin Dashboard

**Solution:** Make sure you're logged in as admin:
- Go to `/admin/login`
- Use admin credentials
- Check user has `role='admin'` in database

### Issue: "Token Missing" Error

**Solution:** Provide token in header:
- For normal servers: `Authorization: Bearer TOKEN`
- For IIS servers: `X-Api-Token: TOKEN`

### Issue: IIS Basic Auth Works But API Fails

**Solution:** Use custom header:
- Add `X-Api-Token: YOUR_TOKEN` header
- Keep `Authorization: Basic xxxx` for IIS
- Both headers work simultaneously

### Issue: Non-Admin Can Access Admin Panel

**Solution:** Check middleware is applied:
- Verify `'admin'` middleware in routes
- Check user role in database
- Middleware redirects non-admins to login

---

## Best Practices

1. **Never expose tokens in URLs**
2. **Use HTTPS in production**
3. **Implement rate limiting**
4. **Rotate tokens regularly**
5. **Use CORS properly for API**
6. **Keep IIS Basic Auth credentials secure**
7. **Use environment variables for sensitive data**

---

## Summary

✅ **Admin Login:** Traditional form-based login with session cookies  
✅ **API Auth:** Bearer token authentication  
✅ **IIS Support:** Custom `X-Api-Token` header for dual auth  
✅ **Role-Based:** Admin vs User access control  
✅ **Swagger Docs:** Available at `/api/documentation`  

**All authentication methods work independently and securely!** 🔒
