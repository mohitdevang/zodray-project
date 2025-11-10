# IIS Hosting Guide - Resolving 401 HTML Errors

## Problem
When deploying to IIS hosting with Basic Auth enabled, you may see **HTML 401 errors** instead of JSON responses, even for public routes like `/api/register` and `/api/login`.

## Root Cause
IIS Basic Auth is enforced **before** Laravel runs. If IIS rejects the Basic Auth credentials, it shows an HTML 401 page and the request never reaches Laravel.

## Solution

### 1. Upload `web.config` File
The `public/web.config` file is **critical**. It ensures:
- ✅ Errors pass through to Laravel (so JSON is returned, not HTML)
- ✅ All requests are routed through Laravel's `index.php`
- ✅ Laravel handles authentication logic

**Action:** Make sure `public/web.config` is uploaded to your live server.

### 2. Use Valid IIS Basic Auth Credentials
IIS Basic Auth is **required** by your hosting provider. You **must** send valid credentials:

```bash
# Example curl with valid Basic Auth
curl --location 'http://yoursite.com/api/register' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Basic <VALID_BASE64_CREDENTIALS>' \
  --data-raw '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

**How to get valid credentials:**
- Check your hosting provider's control panel
- Look for IIS Basic Auth credentials or FTP credentials
- The format is usually: `username:password` base64 encoded

**Example:**
```bash
# If your IIS username is "11273244" and password is "60-dayfreetrial"
# Base64 encode: echo -n "11273244:60-dayfreetrial" | base64
# Result: MTEyNzMyNDQ6NjAtZGF5ZnJlZXRyaWFs
```

### 3. Understanding the Flow

```
Request with Basic Auth
    ↓
IIS validates Basic Auth
    ↓
✅ Valid → Request reaches Laravel → JSON response
❌ Invalid → IIS shows HTML 401 → Request never reaches Laravel
```

### 4. Public Routes vs Protected Routes

**Public Routes (No Laravel Auth Required):**
- `/api/register` - Registration
- `/api/login` - Login

**Protected Routes (Require Laravel Bearer Token):**
- `/api/me` - Get current user
- `/api/checkout` - Create order
- `/api/payment/{id}` - Payment operations

**For Protected Routes:**
You need **BOTH**:
1. **IIS Basic Auth** (required by hosting)
2. **Laravel Bearer Token** (via `X-Api-Token` header)

```bash
curl --location 'http://yoursite.com/api/me' \
  --header 'Accept: application/json' \
  --header 'Authorization: Basic <IIS_BASIC_AUTH>' \
  --header 'X-Api-Token: <LARAVEL_SANCTUM_TOKEN>'
```

### 5. Testing

**Test Public Route (should work with valid IIS Basic Auth):**
```bash
curl --location 'http://yoursite.com/api/register' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Basic <VALID_IIS_CREDENTIALS>' \
  --data-raw '{"name":"Test","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
```

**Expected Response:**
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {...},
    "token": "..."
  }
}
```

**If you still see HTML 401:**
1. ✅ Verify `public/web.config` is uploaded
2. ✅ Check IIS Basic Auth credentials are correct
3. ✅ Ensure base64 encoding is correct
4. ✅ Check hosting provider's documentation for Basic Auth requirements

### 6. Troubleshooting

**Issue: HTML 401 on public routes**
- **Cause:** IIS Basic Auth credentials are wrong or missing
- **Fix:** Use correct IIS Basic Auth credentials from your hosting provider

**Issue: HTML 500 instead of JSON**
- **Cause:** `web.config` not uploaded or IIS not configured
- **Fix:** Upload `public/web.config` and ensure `existingResponse="PassThrough"` is set

**Issue: JSON 401 from Laravel (not HTML)**
- **Cause:** Laravel authentication is working, but credentials are invalid
- **Fix:** This is expected! Use valid Laravel Bearer token for protected routes

## Summary

✅ **Upload `public/web.config`** - Critical for JSON responses  
✅ **Use valid IIS Basic Auth credentials** - Required by hosting  
✅ **Public routes work with just IIS Basic Auth** - No Laravel token needed  
✅ **Protected routes need both** - IIS Basic Auth + Laravel Bearer Token  

Once IIS lets the request through, Laravel will **always** return JSON (not HTML) thanks to our middleware and exception handlers.

